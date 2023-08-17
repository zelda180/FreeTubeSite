#!/usr/bin/perl -w
#**********************************************************************************************************************************
#	Name: ubr_upload.pl
#	Link: http://uber-uploader.sourceforge.net/
#	Revision: 2.6
#	Date: 10:29 PM Tuesday, January 13, 2009
#	Developer: Peter Schmandra
#	Description: Upload files to a temp dir based on upload id, transfer files to upload dir and redirects.
#
#	BEGIN LICENSE BLOCK
#	The contents of this file are subject to the Mozilla Public License
#	Version 1.1 (the "License"); you may not use this file except in
#	compliance with the License. You may obtain a copy of the License
#	at http://www.mozilla.org/MPL/
#
#	Software distributed under the License is distributed on an "AS IS"
#	basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See
#	the License for the specific language governing rights and
#	limitations under the License.
#
#	Alternatively, the contents of this file may be used under the
#	terms of either the GNU General Public License Version 2 or later
#	(the "GPL"), or the GNU Lesser General Public License Version 2.1
#	or later (the "LGPL"), in which case the provisions of the GPL or
#	the LGPL are applicable instead of those above. If you wish to
#	allow use of your version of this file only under the terms of
#	either the GPL or the LGPL, and not to allow others to use your
#	version of this file under the terms of the MPL, indicate your
#	decision by deleting the provisions above and replace them with the
#	notice and other provisions required by the GPL or the LGPL. If you
#	do not delete the provisions above, a recipient may use your
#	version of this file under the terms of any one of the MPL, the GPL
#	or the LGPL.
#	END LICENSE BLOCK
#**********************************************************************************************************************************

#**********************************************************************************************************************************
#	ATTENTION: THE $TEMP_DIR AND $DATA_DELIMITER VALUES MUST BE DUPLICATED IN THE "UBR_INI.PHP" FILE
#**********************************************************************************************************************************
my $TEMP_DIR        = '/home/CPANEL-USER-NAME-HERE/public_html/templates_c/';       # MUST BE DUPLICATED IN THE "UBR_INI.PHP" FILE
my $DATA_DELIMITER  = '<=>';                  # MUST BE DUPLICATED IN THE "UBR_INI.PHP" FILE
my $DEBUG_PERL = 0;                           # Enable to view version and environment variables (config will not be loaded)

$| = 1;                                       # Make STDOUT flush immediately

use strict;                                   # Insert whipping sound here
use CGI::Carp 'fatalsToBrowser';              # Dump fatal errors to screen
use CGI qw(:cgi);                             # Load the CGI.pm module
use File::Copy;                               # Module for moving uploaded files
use File::Path;                               # Module for creating and removing directories
use IO::File;                                 # Module for file IO

my $UBER_VERSION = "6.6";                     # Version of UU
my $THIS_VERSION = "2.6";                     # Version of this script
my $UPLOAD_ID = '';                           # Initialize upload id

# Makes %ENV safer
$ENV{'PATH'} = '/bin:/usr/bin:/usr/local/bin';
delete @ENV{'IFS', 'CDPATH', 'ENV', 'BASH_ENV'};

###############################################################
#	The following possible query string formats are assumed
#
#	1. ?upload_id=32_character_alpha_numeric_string
#	2. ?about
###############################################################
my %query_string = parse_query_string($ENV{'QUERY_STRING'});   # Parse query string
my $print_issued = 0;                                          # Track print statement
my $remove_temp_dir = 0;                                       # Track remove upload_id.dir

if($DEBUG_PERL){
	# Pass blank config
	my %dummy_config = ();

	# Display version and environment variables
	&show_debug_info($UBER_VERSION, $THIS_VERSION, $TEMP_DIR, $DATA_DELIMITER, %dummy_config);
}
elsif(exists($query_string{'upload_id'})){
	# Check for tainted upload id
	if($query_string{'upload_id'} !~ m/(^[a-zA-Z0-9]{32}$)/){ die("Invalid upload id"); }
	else{ $UPLOAD_ID = $1; }
}
elsif(exists($query_string{'about'})){ &kak("<u><b>UBER UPLOADER VERSION</b><\/u><br> UBER UPLOADER VERSION = <b>" . $UBER_VERSION . "<\/b><br> UBR_UPLOAD = <b>" . $THIS_VERSION . "<\/b><br>\n", 1, __LINE__); }
else{ die("Invalid parameters passed"); }

my $start_upload = 0;                                                               # Timestamp start of upload
my $end_upload = 0;                                                                 # Timestamp end of upload
my $sleep_time = 1;                                                                 # Seconds to wait before upload proceeds (for small file uploads)
my %uploaded_files = ();                                                            # Hash used to store uploaded file names, sizes and types
my $temp_dir_id = $TEMP_DIR . $UPLOAD_ID . '.dir';                                  # The upload dir appendided to the temp dir
my $flength_file = $temp_dir_id . '/' . $UPLOAD_ID . '.flength';                    # Flength file is used to store the size of the upload in bytes
my $redirect_file = $TEMP_DIR . $UPLOAD_ID . '.redirect';                           # Redirect file (upload id.redirect)
my $hook_file = $temp_dir_id . '/' . $UPLOAD_ID . '.hook';                          # Hook file is used to store upload info
my $hook_handle;                                                                    # File handle used for hook file
my %current_file_uploads = ();                                                      # Hash used to store current upload info
my %config = &load_config_file($TEMP_DIR, $UPLOAD_ID, $DATA_DELIMITER);             # Load config values from the link file

####################################################################################################################
#	The config values from the link file are loaded at this point. You can now access or set config values. eg. $config{'party_on'} = "indeed";
####################################################################################################################

# Dump config settings and environment variables to screen and exit. Upload will not succeed.
if($config{'debug_upload'}){ &show_debug_info($UBER_VERSION, $THIS_VERSION, $TEMP_DIR, $DATA_DELIMITER, %config); }

umask(0);

# Set the max post value
$CGI::POST_MAX = $config{'max_upload_size'};

# Create a temp directory based on upload id
mkpath($temp_dir_id, 0, 0777) or die("Failed to make $temp_dir_id: $!");

# Create flength file
my $flength_handle = new IO::File;
$flength_handle->open("> $flength_file") or die("Failed to open $flength_file: $!");
$flength_handle->autoflush(1);
chmod 0666, $flength_file;

if(!$config{'found_link_file'}){
	# If fail to find upload_id.link file, write error to flength file and exit
	$flength_handle->print("ERROR" . $DATA_DELIMITER . "1" . $DATA_DELIMITER . "Failed to open link file " . $TEMP_DIR . $UPLOAD_ID . ".link");
	$flength_handle->close();

	die("Failed to open $UPLOAD_ID.link: $!\n");
}
elsif($ENV{'CONTENT_LENGTH'} > $config{'max_upload_size'}){
	# If file size exceeds maximum, write error to flength file and exit
	my $max_size = &format_bytes($config{'max_upload_size'}, 99);
	$flength_handle->print("ERROR" . $DATA_DELIMITER . "2" . $DATA_DELIMITER . "Maximum upload size of $max_size exceeded");
	$flength_handle->close();

	die("Maximum upload size of $max_size exceeded\n");
}
elsif($config{'cgi_upload_hook'} && $CGI::VERSION <= 3.15){
	# CGI.pm version must be greater than 3.15 to use an upload hook
	$flength_handle->print("ERROR" . $DATA_DELIMITER . "3" . $DATA_DELIMITER . "CGI.pm ver" . $CGI::VERSION . " does not support an upload hook");
	$flength_handle->close();

	die("CGI.pm ver" . $CGI::VERSION . " does not support an upload hook");
}
else{
	# Write total upload size in bytes to flength file
	$flength_handle->print($ENV{'CONTENT_LENGTH'});
	$flength_handle->close();

	# Clean up upload_id.dir when the script exits
	$remove_temp_dir = 1;
}

# Give progress bar a chance to get some info (for small file uploads)
sleep($sleep_time);

# Get remote address, user agent and server name
$config{'remote_addr'} = $ENV{'REMOTE_ADDR'};
$config{'http_user_agent'} = $ENV{'HTTP_USER_AGENT'};
$config{'server_name'} = $ENV{'SERVER_NAME'};

if($config{'cgi_upload_hook'}){
	# Create an upload hook file
	$hook_handle = new IO::File;
	$hook_handle->open("> $hook_file") or die("Failed to open $hook_file: $!");
	$hook_handle->autoflush(1);
	my $hook_query = CGI->new(\&hook, $hook_handle);
}
else{
	# Disable private temp files
	CGI::private_tempfiles(0);

	# Tell CGI.pm to use our directory based on upload id
	if($TempFile::TMPDIRECTORY){ $TempFile::TMPDIRECTORY = $temp_dir_id; }
	elsif($CGITempFile::TMPDIRECTORY){ $CGITempFile::TMPDIRECTORY = $temp_dir_id; }
	else{ die("Failed to assign CGI temp directory: $!"); }
}

# Timestamp start of upload
$config{'start_upload'} = time();

# Upload finished
my $query = new CGI;

####################################################################################################################
#	The upload is now complete, you can now access post values. eg. $query->param("some_post_value");
####################################################################################################################

# Delete the flength file
rmtree($flength_file, 0, 1) or warn("Failed to remove $flength_file: $!");

# Close and delete the hook file
if($config{'cgi_upload_hook'}){
	$hook_handle->close();
	rmtree($hook_file, 0, 1) or warn("Failed to remove $hook_file: $!");
}

####################################################################################################################
#	IF you are modifying the upload directory with a post or config  value, it may be done after this comment block.
#	Note: Making modifications based on posted input may be unsafe. Make sure your posted input is safe!
#
#	eg. $config{'upload_dir'} .= $query->param("employee_num") . '/';
#	eg. $config{'path_to_upload'} .= $query->param("employee_num") . '/';
#
#	eg. $config{'upload_dir'} .= $config{'employee_num'} . '/';
#	eg. $config{'path_to_upload'} .= $config{'employee_num'} . '/';
###################################################################################################################

# Create a directory based on upload_id inside the upload directory if config setting 'unique_upload_dir' is enabled
if($config{'unique_upload_dir'}){
	$config{'upload_dir'} .= $UPLOAD_ID . '/';

	if($config{'link_to_upload'} || $config{'link_to_upload_in_email'}){ $config{'path_to_upload'} .= $UPLOAD_ID . '/'; }
}

# Create upload directory if it does not exist
if(!-d $config{'upload_dir'}){ mkpath($config{'upload_dir'}, 0, 0777) or die("Failed to make $config{'upload_dir'}: $!\n"); }

# Start processing the uploaded files
for my $upload_key (keys %{$query->{'.tmpfiles'}}){
	# Get the file slot name eg. 'upfile_0'
	$query->{'.tmpfiles'}->{$upload_key}->{'info'}->{'Content-Disposition'} =~ / name="([^"]*)"/;

	# Store file slot name
	my $file_slot = $1;

	# Get uploaded file name
	my $file_name = param($file_slot);

	# Strip extra path info from the file (IE). Note: Will likely cause problems with foreign languages like chinese
	$file_name =~ s/.*[\/\\](.*)/$1/;

	# Get the upload file handle
	my $upload_filehandle = $query->upload($file_slot);

	# Get the CGI temp file name
	my $tmp_filename = $query->tmpFileName($upload_filehandle);

	# Get the type of file being uploaded
	my $content_type = $query->uploadInfo($upload_filehandle)->{'Content-Type'};

	# Get base file name and extension
	my($f_name, $file_extension) = $file_name =~ /(.*)\.(.+)/;

	########################################################################################################
	#	IF you are modifying the file name with a post or config value, it may be done after this comment block.
	#
	#	Note: Making modifications based on posted input may be unsafe. Make sure your posted input is safe!
	#
	#	eg. $file_name = $f_name . "_" . $query->param("employee_num") . "." . $file_extension;
	#	eg. $file_name = $f_name . "_" . $config{'employee_num'} . "." . $file_extension;
	########################################################################################################

	my $strict_file_name_pass = 1;       # Default to pass check
	my $zero_length_file_pass = 1;       # Default to pass check
	my $allow_extensions_pass = 1;       # Default to pass check
	my $disallow_extensions_pass = 1;    # Default to pass check

	#Strict check of file name. If check fails, the file WILL NOT be transfered.
	if($config{'strict_file_name_check'}){ $strict_file_name_pass = &strict_file_name_check($file_name, $config{'strict_file_name_regex'}, $config{'min_file_name_chars'}, $config{'max_file_name_chars'}); }

	#Check for zero length file
	$zero_length_file_pass = -s $tmp_filename;

	# Check file extension
	if($config{'check_allow_extensions_on_server'}){ $allow_extensions_pass = &check_file_extension($file_extension, $config{'allow_extensions'}, 1); }
	if($config{'check_disallow_extensions_on_server'}){ $disallow_extensions_pass = &check_file_extension($file_extension, $config{'disallow_extensions'}, 2); }

	# Do not process files that fail checks
	if($strict_file_name_pass && $zero_length_file_pass && $allow_extensions_pass && $disallow_extensions_pass){
		# Create a unique filename if config setting 'unique_filename' is enabled
		if($config{'unique_file_name'}){
			my $unique_file_name = generate_random_string($config{'unique_file_name_length'});
			$unique_file_name = $unique_file_name . "." . $file_extension;
			$file_name = $unique_file_name;
		}
		elsif($config{'normalize_file_names'}){ $file_name = &normalize_filename($file_name, $config{'normalize_file_regex'}, $config{'normalize_file_delimiter'}, $config{'min_file_name_chars'}, $config{'max_file_name_chars'}); }

		# Check for an existing file and rename if it already exists
		if(!$config{'overwrite_existing_files'}){ $file_name = &rename_filename($file_name, 1, $config{'upload_dir'}); }

		# Path to file and file name
		my $upload_file_path = $config{'upload_dir'} . $file_name;

		# Win wants the file handle closed before transfer
		close($upload_filehandle);

		# Transfer uploaded file to final destination
		move($tmp_filename, $upload_file_path) or copy($tmp_filename, $upload_file_path) or die("Cannot move/copy from $tmp_filename to $upload_file_path: $!");

		chmod 0666, $upload_file_path;

		# Store the upload file info
		$uploaded_files{$file_slot}{'file_size'} = &get_file_size($config{'upload_dir'}, $file_name);
		$uploaded_files{$file_slot}{'file_name'} = $file_name;
		$uploaded_files{$file_slot}{'file_type'} = $content_type;
		$uploaded_files{$file_slot}{'file_status'} = 'OK';
	}
	else{
		close($upload_filehandle);

		# Store the upload file info
		$uploaded_files{$file_slot}{'file_size'} = 0;
		$uploaded_files{$file_slot}{'file_name'} = $file_name;
		$uploaded_files{$file_slot}{'file_type'} = $content_type;
		$uploaded_files{$file_slot}{'file_status'} = 'FAIL:';

		# Record why the file transfer failed
		if(!$strict_file_name_pass){ $uploaded_files{$file_slot}{'file_status'} .= 'Strict File Name'; }
		elsif(!$zero_length_file_pass){ $uploaded_files{$file_slot}{'file_status'} .= 'Zero length file'; }
		elsif(!$allow_extensions_pass){ $uploaded_files{$file_slot}{'file_status'} .= 'Allow extension'; }
		elsif(!$disallow_extensions_pass){ $uploaded_files{$file_slot}{'file_status'} .= 'Disallow extension'; }
	}
}

# Timestamp end of upload (includes file transfer)
$config{'end_upload'} = time();

# Delete the temp directory based on upload id and everything in it
rmtree($temp_dir_id, 0, 1) or warn("Failed to remove $temp_dir_id: $!\n");

# Purge old temp directories
if($config{'purge_temp_dirs'}){ &purge_ubr_dirs($TEMP_DIR, $config{'purge_temp_dirs_limit'}); }

# Log Upload
if($config{'log_uploads'}){
	my ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) = localtime(time());
	$year += 1900;
	$mon++;

	# Initialize log directory and file name
	my $log_day = $config{'log_dir'} . $year . '-' . $mon . '-' . $mday . '/';
	my $log_file = $log_day . $UPLOAD_ID . ".log";

	# Create log directory if it does not exist
	if(!-d $log_day){ mkpath($log_day, 0, 0777) or die("Failed to make $log_day: $!"); }

	# Create log file
	my $log_handle = new IO::File;
	$log_handle->open("> $log_file") or die("Failed to open $UPLOAD_ID.log: $!");
	$log_handle->autoflush(1);

	# Write log file
	&write_uu_file($log_handle, %config, %uploaded_files);
}

# Redirect after upload or output data
if($config{'redirect_using_html'} || $config{'redirect_using_js'} || $config{'redirect_using_location'}){
	# Create redirect file
	my $redirect_handle = new IO::File;
	$redirect_handle->open("> $redirect_file") or die("Failed to open $UPLOAD_ID.redirect: $!");
	$redirect_handle->autoflush(1);

	# Write redirect file
	&write_uu_file($redirect_handle, %config, %uploaded_files);

	# Append upload id to redirect url
	my $redirect_url = $config{'redirect_url'} . "?upload_id=" . $UPLOAD_ID;

	if(!$config{'embedded_upload_results'} && ($config{'opera_browser'} || $config{'safari_browser'})){
		# Deal with Opera and Safari browser limitations
		$config{'redirect_using_js'} = 1;
		$config{'redirect_using_html'} = 0;
		$config{'redirect_using_location'} = 0;
		&kak("<script language=\"javascript\" type=\"text/javascript\">parent.location.href='$redirect_url';</script>", 1, __LINE__);
	}
	else{
		if($config{'redirect_using_html'}){
			print "content-type:text/html; charset=utf-8\n\n";
			print "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><meta http-equiv=\"refresh\" content=\"0; url='$redirect_url'\"></head><body></body></html>";
		}
		elsif($config{'redirect_using_js'}){
			&kak("<script language=\"javascript\" type=\"text/javascript\">document.location.href='$redirect_url';</script>", 1, __LINE__);
		}
		elsif($config{'redirect_using_location'}){
			# Uncomment next line if using Webstar V
			# print "HTTP/1.1 302 Redirection\n";
			print "Location: $redirect_url\n\n";
		}
	}
}
else{
	# All the config, post and upload values are stored on the following
	#
	# %config{"some_config_value"};
	# $query->param("some_post_value");
	# $uploaded_files{'upfile_0'}{'file_size'} ;
	# $uploaded_files{'upfile_0}'{'file_name'};
	# $uploaded_files{'upfile_0'}{'file_type'};

	# If you are going to display upload results through CGI, there is no need to
	# upload the 'ubr_finished.php' and 'ubr_finished_lib.php' files.

	my $cgi_output = 'OK';

	if($config{'embedded_upload_results'}){
		$cgi_output .= "<script language=\"javascript\" type=\"text/javascript\">";
		$cgi_output .= "parent.document.getElementById('upload_div').style.display='';";
		$cgi_output .= "parent.iniFilePage();";
		$cgi_output .= "</script>";
	}

	&kak($cgi_output, 1, __LINE__);
}

exit;
######################################################## START SUB ROUTINES ############################################################


#########################################
#	Clean up the upload_id.dir and everything in it
#########################################
END{
	if($remove_temp_dir){ if(-d $temp_dir_id){ rmtree($temp_dir_id, 0, 1) or warn("Failed to remove $temp_dir_id: $!"); } }
}

#########################################
#	Write upload info to hook file
#########################################
sub hook{
	my ($current_filename, $buffer, $bytes_read, $hook_handle) = @_;
	my $total_bytes_read = 0;
	my $files_uploaded = keys(%current_file_uploads);

	$files_uploaded--;
	$current_filename =~ s/.*[\/\\](.*)/$1/;
	$current_file_uploads{$current_filename} = $bytes_read;

	for my $file_slot (keys %current_file_uploads){ $total_bytes_read += $current_file_uploads{$file_slot}; }

	my $stat = $total_bytes_read . $DATA_DELIMITER . $files_uploaded . $DATA_DELIMITER . $current_filename . $DATA_DELIMITER . $bytes_read;

	$hook_handle->seek(0,0);
	$hook_handle->print($stat);
	$hook_handle->truncate($hook_handle->tell());
}

#########################################
#	Check file extension
#########################################
sub check_file_extension{
	my $file_extension = shift;
	my $config_extensions = shift;
	my $mode = shift;

	if($mode == 1){
		if($file_extension =~ m/^$config_extensions$/i){ return 1; }
		else{ return 0; }
	}
	elsif($mode == 2) {
		if($file_extension !~ m/^$config_extensions$/i){ return 1; }
		else{ return 0; }
	}
	else{ return 0; }
}

##################################################
#	Get the size of the ploaded file if it exists
##################################################
sub get_file_size{
	my $upload_dir = shift;
	my $file_name = shift;
	my $path_to_file = $upload_dir . $file_name;
	my $file_size = 0;

	if(-e $path_to_file && -f $path_to_file){ $file_size = -s $path_to_file; }

	return $file_size;
}

####################################################
#	formatBytes($file_size, 99) mixed file sizes
#	formatBytes($file_size, 0) KB file sizes
#	formatBytes($file_size, 1) MB file sizes etc
####################################################
sub format_bytes{
	my $bytes = shift;
	my $byte_format = shift;
	my $byte_size = 1024;
	my $i = 0;
	my @byte_type = (" KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");

	$bytes /= $byte_size;

	if($byte_format == 99 || $byte_format > 7){
		while($bytes > $byte_size){
			$bytes /= $byte_size;
			$i++;
		}
	}
	else{
		while($i < $byte_format){
			$bytes /= $byte_size;
			$i++;
		}
	}

	$bytes = sprintf("%1.2f", $bytes);
	$bytes .= $byte_type[$i];

	return $bytes;
}

############################################
#	Rename uploaded file if it already exists
############################################
sub rename_filename{
	my $file_name = shift;
	my $count = shift;
	my $upload_dir = shift;
	my $path_to_file = $upload_dir . $file_name;

	if(-e $path_to_file && -f $path_to_file){
		if($file_name =~ /(.*)_(\d*)\.(.*)/){
			# Already renamed so count on
			$count = $2 + 1;
			$file_name =~ s/(.*)_(\d*)\.(.*)/$1_$count\.$3/;
		}
		else{
			# Not renamed so start counting
			$file_name =~ s/(.*)\.(.*)/$1_$count\.$2/;
		}
		&rename_filename($file_name, $count, $upload_dir);
	}
	else{ return $file_name; }
}

#######################
#	Normalize file name
######################
sub normalize_filename{
	my $file_name = shift;
	my $normalize_file_regex = shift;
	my $normalize_file_delimiter = shift;
	my $min_file_name_chars = shift;
	my $max_file_name_chars = shift;

	# Remove whitespaces from the beginning and end of the filename
	$file_name = &trim($file_name);

	# Check the length of the file name and add characters if neseccary
	if(length($file_name) < $min_file_name_chars){ $file_name = &generate_random_string($min_file_name_chars - length($file_name)) . $file_name; }

	# Check the length of the file name and cut characters if neseccary
	if(length($file_name) > $max_file_name_chars){ $file_name = substr($file_name, length($file_name) - $max_file_name_chars); }

	# Search and replace non-latin characters below. eg.
	# $file_name =~ s/�/e/g;
	# $file_name =~ s/�/ue/g;
	# $file_name =~ s/�/ae/g;
	# $file_name =~ s/�/e/g;
	# $file_name =~ s/�/o/g;

	# Search and replace illegal file name characters
	$file_name =~ s/$normalize_file_regex/$normalize_file_delimiter/g;

	return $file_name;
}

####################################
#	Strict check of the file name
####################################
sub strict_file_name_check{
	my $file_name = shift;
	my $strict_file_name_regex = shift;
	my $min_file_name_chars = shift;
	my $max_file_name_chars = shift;

	if($file_name !~ m/$strict_file_name_regex/){ return 0; }
	elsif(length($file_name) < $min_file_name_chars){ return 0; }
	elsif(length($file_name) > $max_file_name_chars){ return 0; }
	else{ return 1; }
}

####################################
#	Strip whitespace from the begginning and end of a string
####################################
sub trim{
	my $string = shift;

	$string =~ s/^\s+//; # Trim left
	$string =~ s/\s+$//; # Trim right

	return $string;
}

#########################
#	Generate Randon String
#########################
sub generate_random_string{
	my $length_of_randomstring = shift;
	my @chars=('a'..'z', '0'..'9');
	my $random_string;

	for(my $i = 0; $i < $length_of_randomstring; $i++){ $random_string .= $chars[int(rand(36))]; }

	return $random_string;
}

##########################
#	Parse the query string
##########################
sub parse_query_string{
	my $buffer = shift;
	my @pairs = split(/&/, $buffer);
	my %query_string = ();

	foreach my $pair (@pairs){
		my ($name, $value) = split(/=/, $pair);

		$value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		$query_string{$name} = $value;
	}

	return %query_string;
}

##########################
#	Load config file
##########################
sub load_config_file{
	my $temp_dir = shift;
	my $upload_id = shift;
	my $data_delimiter = shift;
	my $config_file = $temp_dir . $upload_id . ".link";
	my %config = ();
	my $config_handle = new IO::File;
	my $timeout_limit = 3;

	$config{'found_link_file'} = 0;

	# Keep trying to read the link file until timeout
	for(my $i = 0; $i < $timeout_limit; $i++){
		if($config_handle->open("< $config_file")){
			$config{'found_link_file'} = 1;
			my @raw_config =  <$config_handle>;
			$config_handle->close();

			foreach my $config_setting (@raw_config){
				chop($config_setting);
				my($config_name, $config_value) = split($data_delimiter, $config_setting);
				$config{$config_name} = $config_value;
			}

			if($config{'delete_link_file'}){ rmtree($config_file, 0, 1) or warn("Failed to remove $config_file: $!"); }

			last;
		}
		else{ sleep(1); }
	}

	return %config;
}

################################
#	Purge old upload directories
################################
sub purge_ubr_dirs{
	my $temp_dir = shift;
	my $purge_temp_dirs_limit = shift;
	my @upload_dirs = glob("$temp_dir*.dir");
	my $now_time = time();

	foreach my $upload_dir (@upload_dirs){
		my $dir_time = (stat($upload_dir))[9];

		if(($now_time - $dir_time) > $purge_temp_dirs_limit){ rmtree($upload_dir, 0, 1) or warn("Failed to remove $upload_dir: $!"); }
	}
}

####################################################
#	Write a XML file containing configuration upload and post information
####################################################
sub write_uu_file{
	my $file_handle = shift;
	my $config = shift;
	my $uploaded_files = shift;
	my @names = $query->param;

	binmode $file_handle;

	$file_handle->print("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
	$file_handle->print("<uu_upload>\n");
	$file_handle->print("	<config>\n");

	# Write config values
	foreach my $config_setting (sort keys(%config)){ $file_handle->print("		<$config_setting>$config{$config_setting}<\/$config_setting>\n"); }

	$file_handle->print("	<\/config>\n");
	$file_handle->print("	<post>\n");

	#Write post values
	foreach my $key (@names){
		my @post_values = $query->param($key);

		foreach my $post_value (@post_values){
			$post_value =~ s/&/&amp;/g;
			$post_value =~ s/</&lt;/g;
			$post_value =~ s/>/&gt;/g;
			$post_value =~ s/'/&apos;/g;
			$post_value =~ s/"/&quot;/g;

			# Search and replace non-latin characters below. eg.
			# $post_value =~ s/�/e/g;
			# $post_value =~ s/�/ue/g;
			# $post_value =~ s/�/ae/g;
			# $post_value =~ s/�/e/g;
			# $post_value =~ s/�/o/g;

			$key =~ s/[^a-zA-Z0-9\_\-]//g;

			$file_handle->print("		<$key>$post_value<\/$key>\n");
		}
	}

	$file_handle->print("	<\/post>\n");
	$file_handle->print("	<file>\n");

	# Write upload file info
	for my $file_slot (keys %uploaded_files){
		my $file_name = $uploaded_files{$file_slot}{'file_name'};
		my $file_size = $uploaded_files{$file_slot}{'file_size'};
		my $file_type = $uploaded_files{$file_slot}{'file_type'};
		my $file_status = $uploaded_files{$file_slot}{'file_status'};

		$file_handle->print("		<file_upload>\n");
		$file_handle->print("			<slot>$file_slot<\/slot>\n");
		$file_handle->print("			<name>$file_name<\/name>\n");
		$file_handle->print("			<size>$file_size<\/size>\n");
		$file_handle->print("			<type>$file_type<\/type>\n");
		$file_handle->print("			<status>$file_status<\/status>\n");
		$file_handle->print("		<\/file_upload>\n");
	}

	$file_handle->print("	<\/file>\n");
	$file_handle->print("<\/uu_upload>\n");
	$file_handle->close();

	chmod 0666, $file_handle;
}

########################################################################
#	Output a message to the screen
#
#	You can use this function to debug your script.
#
#	eg. &kak("The value of blarg is: " . $blarg . "<br>", 1, __LINE__);
#	This will print the value of blarg and exit the script.
#
#	eg. &kak("The value of blarg is: " . $blarg . "<br>", 0, __LINE__);
#	This will print the value of blarg and continue the script.
########################################################################
sub kak{
	my $msg = shift;
	my $kak_exit = shift;
	my $line  = shift;

	if(!$print_issued){
		print "Content-type: text/html\n\n";
		$print_issued = 1;
	}

	print "<!DOCTYPE HTML PUBLIC \"-\/\/W3C\/\/DTD HTML 4.01 Transitional\/\/EN\">\n";
	print "<html>\n";
	print "  <head>\n";
	print "    <title>Uber-Uploader - Free File Upload Progress Bar<\/title>\n";
	print "      <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n";
	print "      <meta http-equiv=\"Pragma\" content=\"no-cache\">\n";
	print "      <meta http-equiv=\"cache-control\" content=\"no-cache\">\n";
	print "      <meta http-equiv=\"expires\" content=\"-1\">\n";
	print "      <meta name=\"robots\" content=\"none\">\n";
	print "  <\/head>\n";
	print "  <body style=\"background-color: #EEEEEE; color: #000000; font-family: arial, helvetica, sans_serif;\">\n";
	print "    <br>\n";
	print "    <div align='center'>\n";
	print "    $msg\n";
	print "    <br>\n";
	print "    <!-- kak on line $line -->\n";
	print "    </div>\n";
	print "  </body>\n";
	print "</html>\n";

	if($kak_exit){
		close(STDIN);
		exit;
	}
}

#####################################################################
#	Print config, driver and environment values to screen
#####################################################################
sub show_debug_info{
	my $uber_version = shift;
	my $this_version = shift;
	my $temp_dir = shift;
	my $data_delimiter = shift;
	my $config = shift;
	my $msg = '';
	my $perl_version = $];
	my $perl_os = $^O;
	my $cgi_version = $CGI::VERSION;
	my $file_copy_version = $File::Copy::VERSION;
	my $file_path_version = $File::Path::VERSION;
	my $file_handle_version = $FileHandle::VERSION;

	$msg .= "<div align='left'>\n";
	$msg .= "<u><b>VERSION INFORMATION<\/b><\/u><br>\n";
	$msg .= "UBER UPLOADER VERSION = <b>$uber_version<\/b><br>\n";
	$msg .= "UBR_UPLOAD = <b>$this_version<\/b><br>\n";
	$msg .= "PERL VERSION = <b>$perl_version<\/b><br>\n";
	$msg .= "PERL OS = <b>$perl_os<\/b><br>\n";
	$msg .= "CGI.PM VERSION = <b>$cgi_version<\/b><br>\n";
	$msg .= "FILE::COPY VERSION = <b>$file_copy_version<\/b><br>\n";
	$msg .= "FILE::PATH VERSION = <b>$file_path_version<\/b><br>\n";
	$msg .= "FILEHANDLE::VERSION = <b>$file_handle_version<\/b><br>\n";
	$msg .= "<br><u><b>CONFIGURATION VARIABLES<\/b><\/u><br>\n";

	# Local temp_dir and data_delimiter settings
	$msg .= "TEMP_DIR = <b>$temp_dir<\/b><br>\n";
	$msg .= "DATA_DELIMITER = <b>$data_delimiter<\/b><br>\n";

	# Print loaded config settings to screen
	foreach my $key (sort keys(%config)){ $msg .= "$key = <b>$config{$key}<\/b><br>\n"; }

	$msg .= "<br><u><b>ENVIRONMENT VARIABLES<\/b><\/u><br>\n";

	# Print environment variables to screen
	foreach my $key (sort keys(%ENV)){ $msg .= "$key = <b>$ENV{$key}<\/b><br>\n"; }

	$msg .= "<\/div>\n";

	if($config{'embedded_upload_results'} || ($config{'opera_browser'} || $config{'safari_browser'})){ $msg .= "<script language=\"javascript\" type=\"text/javascript\">parent.document.getElementById('upload_div').style.display = '';</script>\n"; }

	&kak($msg, 1, __LINE__);
}
