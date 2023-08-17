<?php
//******************************************************************************************************
//	Name: ubr_default_config.php
//	Revision: 1.7
//	Date: 10:27 PM Tuesday, January 13, 2009
//	Link: http://uber-uploader.sourceforge.net
//	Developer: Peter Schmandra
//	Description: Configure upload options
//
//	BEGIN LICENSE BLOCK
//	The contents of this file are subject to the Mozilla Public License
//	Version 1.1 (the "License"); you may not use this file except in
//	compliance with the License. You may obtain a copy of the License
//	at http://www.mozilla.org/MPL/
//
//	Software distributed under the License is distributed on an "AS IS"
//	basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See
//	the License for the specific language governing rights and
//	limitations under the License.
//
//	Alternatively, the contents of this file may be used under the
//	terms of either the GNU General Public License Version 2 or later
//	(the "GPL"), or the GNU Lesser General Public License Version 2.1
//	or later (the "LGPL"), in which case the provisions of the GPL or
//	the LGPL are applicable instead of those above. If you wish to
//	allow use of your version of this file only under the terms of
//	either the GPL or the LGPL, and not to allow others to use your
//	version of this file under the terms of the MPL, indicate your
//	decision by deleting the provisions above and replace them with the
//	notice and other provisions required by the GPL or the LGPL. If you
//	do not delete the provisions above, a recipient may use your
//	version of this file under the terms of any one of the MPL, the GPL
//	or the LGPL.
//	END LICENSE BLOCK
//********************************************************************************************************
//***************************************************************************************************************
//	ATTENTION
//
//	Any extra config settings added to this file will be passed through the uploader.
//	They can be accessed in the 'ubr_upload.pl' script using $config
//	They can be accessed in the 'ubr_finished.php' script using $_CONFIG_DATA
//***************************************************************************************************************
$_CONFIG['config_file_name']                      = 'ubr_default_config';                                                                                         // Name of this config file
$_CONFIG['upload_dir']                            = FREETUBESITE_DIR . '/video/';                                                                  // Path to upload directory
$_CONFIG['max_upload_slots']                      = 1;                                                                                                            // Maximum number of files a user can upload at once
$_CONFIG['embedded_upload_results']               = 0;                                                                                                            // Display the upload results in an iframe
$_CONFIG['check_file_name_format']                = 1;                                                                                                            // Check the format of the file names BEFORE upload
$_CONFIG['check_null_file_count']                 = 1;                                                                                                            // Make sure the user selected at least one file to upload
$_CONFIG['check_duplicate_file_count']            = 1;                                                                                                            // Make sure the user did not select duplicate files
$_CONFIG['check_file_name_regex']                  = '^[a-zA-Z0-9\-\_\s]+[a-zA-Z0-9\-\_\.]+[a-zA-Z0-9\-\_]$';                                                       // Regex used on client side file name check
$_CONFIG['check_file_name_error_message']          = 'Error, allowed file name characters are 1-9, a-z, A-Z, _, -';                                                 // Error message used for client side regex fail
$_CONFIG['show_percent_complete']                 = 1;                                                                                                            // Show percent complete info
$_CONFIG['show_files_uploaded']                   = 1;                                                                                                            // Show files uploaded info
$_CONFIG['show_current_file']                     = 0;                                                                                                            // Show files uploaded info
$_CONFIG['show_current_position']                 = 1;                                                                                                            // Show current bytes uploaded info
$_CONFIG['show_elapsed_time']                     = 1;                                                                                                            // Show elapsed time info
$_CONFIG['show_est_time_left']                    = 1;                                                                                                            // Show estimated time left info
$_CONFIG['show_est_speed']                        = 1;                                                                                                            // Show estimated speed info
$_CONFIG['cedric_progress_bar']                   = 1;                                                                                                            // Enable the 'Cedric' style progress bar
$_CONFIG['cedric_hold_to_sync']                   = 0;                                                                                                            // Hold 'Cedric' progress bar if it races ahead of actual upload
$_CONFIG['bucket_progress_bar']                   = 0;                                                                                                            // Enable the 'Bucket' style progress bar (Must disable Cedric style progress bar)
$_CONFIG['progress_bar_width']                    = 400;                                                                                                          // The width of the progress bar in pixels (IMPORTANT, USED IN CALCULATIONS)
$_CONFIG['unique_upload_dir']                     = 0;                                                                                                            // Upload the files to a folder based on upload id inside the upload folder
$_CONFIG['unique_file_name']                      = 0;                                                                                                            // Rename the file to a unique file name
$_CONFIG['unique_file_name_length']               = 16;                                                                                                           // Number of characters to use in the unique anme
$_CONFIG['max_upload_size']                       = 524288000;                                                                                                      // Maximum upload size (5 * 1024 * 1024 = 5242880 = 5MB)
$_CONFIG['overwrite_existing_files']              = 0;                                                                                                            // Overwrite any existing files by the same name in the upload folder
$_CONFIG['redirect_using_location']               = 1;                                                                                                            // Redirect using perl location
$_CONFIG['redirect_using_html']                   = 0;                                                                                                            // Redirect using html
$_CONFIG['redirect_using_js']                     = 0;                                                                                                            // Redirect using javascript
$_CONFIG['redirect_url']                          = FREETUBESITE_URL . '/upload_file.php';                                                      // What page to load after the upload completes
$_CONFIG['check_allow_extensions_on_client']      = 1;                                                                                                            // Check allow file extensions BEFORE upload
$_CONFIG['check_disallow_extensions_on_client']   = 0;                                                                                                            // Check disallow file extensions BEFORE upload
$_CONFIG['check_allow_extensions_on_server']      = 1;                                                                                                            // Checks for allow file extensions on the server
$_CONFIG['check_disallow_extensions_on_server']   = 0;                                                                                                            // Checks for dissalow file extensions on the server
$_CONFIG['allow_extensions']                      = '(flv|3gp|mp4|mov|asf|mpg|avi|mpeg|wmv|rm|dat|m4v|webm)';                                                  // Include file extentions that are allowed to be uploaded
$_CONFIG['disallow_extensions']                   = '(sh|php|php3|php4|php5|php6|php7|py|shtml|stm|shtm|phtml|html|htm|js|jsp|asp|aspx|exe|cgi|pl|plx|htaccess|htpasswd)';  // Include file extentions that are NOT allowed to be uploaded
$_CONFIG['normalize_file_names']                  = 1;                                                                                                            // Only allows  a-z A-Z 0-9 _ . - and space characters in file names
$_CONFIG['normalize_file_delimiter']              = '_';                                                                                                          // The character that is used as a replacement any disallowed characters in the file name
$_CONFIG['normalize_file_regex']                  = '[^a-zA-Z0-9\_\-\.]';                                                                                        // Search and replace regex used in file name normalization
$_CONFIG['max_file_name_chars']                   = 48;                                                                                                          // The maximum characters allowed in the file name
$_CONFIG['min_file_name_chars']                   = 5;                                                                                                           // The maximum characters allowed in the file name
$_CONFIG['strict_file_name_check']                = 0;                                                                                                           // Strict check of file name. If check fails, the file WILL NOT be transfered.
$_CONFIG['strict_file_name_regex']                = '^[a-zA-Z0-9\-\_\s]+[a-zA-Z0-9\-\_\.]+[a-zA-Z0-9\-\_]$';                                                       // REGEX applied to file name in strict mode
$_CONFIG['link_to_upload']                        = 0;                                                                                                            // Create a web link to the uploaded file
$_CONFIG['path_to_upload']                        = FREETUBESITE_URL . '/video/';                                                           // Used for a web link to the uploaded file
$_CONFIG['send_email_on_upload']                  = 0;                                                                                                            // Send an email when the upload is finished
$_CONFIG['html_email_support']                    = 0;                                                                                                            // Add html support to email
$_CONFIG['link_to_upload_in_email']               = 0;                                                                                                            // Provide web links to uploaded files in email
$_CONFIG['email_subject']                         = 'Uber File Upload';                                                                                           // Subject of the email
$_CONFIG['to_email_address']                      = 'email1@yoursite.com,email2@yoursite.com';                                                                    // To Email addresses
$_CONFIG['from_email_address']                    = 'admin@yoursite.com';                                                                                         // From email address
$_CONFIG['log_uploads']                           = 1;                                                                                                            // Log all uploads
$_CONFIG['log_dir']                               = '/tmp/ubr_logs/';                                                                                             // Path to log directory
$_CONFIG['opera_browser']                         = (strstr(getenv("HTTP_USER_AGENT"), "Opera"))  ? 1 : 0;                                                        // Track Opera browser   ( must unfortunately post to iframe )
$_CONFIG['safari_browser']                        = (strstr(getenv("HTTP_USER_AGENT"), "Safari")) ? 1 : 0;                                                        // Track Safari browser  ( must unfortunately post to iframe )
?>
