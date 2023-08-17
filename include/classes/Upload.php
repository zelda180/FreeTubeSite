<?php
class Upload{
# Download the video (for YT, Dailymotion & other online web video links)
public static function downloadVideo($vid){
        global $config;
        $err = 0;
        $log_text = '<p>Download Started</p>';
        write_log($log_text, $log_file_name, $debug, 'html');
        $sql = "UPDATE `process_queue` SET
    	       `status`='1' WHERE
    	       `id`='" . (int) $vid . "'";
        DB::query($sql);

        $sql = "SELECT * FROM `process_queue` WHERE
    	       `id`='" . (int) $vid . "'";
        $video_info =  DB::fetch1($sql);
        $video_url = $video_info['url'];
        echo "<P>video url : $video_url</p>";

        $video_filename = FREETUBESITE_DIR . '/video/' . $video_info['file'];
        echo "<P>Video Downloaded to : $video_filename</p>";

        if (file_exists($video_filename) && is_file($video_filename)) {
            unlink($video_filename);
            echo "<p>Deleted File: $video_filename</p>";
        }
        write_log($video_url);

        if (strstr($video_url, 'youtube.com')) {
            $file_extn = 'flv';
            $rename_file = 1;
            $video_url = Youtube::getFlvUrl($video_url);
            echo "<p>Youtube FLV location: $video_url</p>";
        } else if (strstr($video_url, 'googlevideo')) {
            $file_extn = 'flv';
            $rename_file = 1;
        } else if (strstr($video_url, 'dailymotion.com')) {
            $file_extn = 'flv';
            $rename_file = 1;
        } else {
            require FREETUBESITE_DIR . '/include/settings/upload.php';
# Is uploaded extention allowed
            $file_name = basename($video_url);
            $pos = strrpos($file_name, '.');
            $file_extn = strtolower(substr($file_name, $pos + 1, strlen($file_name) - $pos));
            if (! in_array($file_extn, $file_types)) {
                $allowed_types = implode(',', $file_types);
                write_log('Unsupported video type: ' . $file_extn);
                $err = 1;
            }
        }
# Rename And Clean up Video File Name
        if ($err == 0) {
            if ($rename_file == 1) {
                $file_no_extn = md5(rand());
            } else {
                $file_name = basename($video_url);
                $pos = strrpos($file_name, '.');
                $file_extn = strtolower(substr($file_name, $pos + 1, strlen($file_name) - $pos));
                $file_no_extn = basename($file_name, ".$file_extn");
                $file_no_extn = ereg_replace("[&$#]+", " ", $file_no_extn);
                $file_no_extn = ereg_replace("[ ]+", "-", $file_no_extn);
            }

            $file_name = $file_no_extn . '.' . $file_extn;
            $desination = FREETUBESITE_DIR . '/video/' . $file_name;
            $i = 0;
            while (file_exists($desination)) {
                $i ++;
                $file_name = $file_no_extn . '_' . $i . '.' . $file_extn;
                $desination = FREETUBESITE_DIR . '/video/' . $file_name;
            }

            echo "<p>Downloading Video : $video_url</p>";
            echo "<p>Destination : $desination</p>";
            $file_size = Http::download($video_url, $desination);
            write_log('Download Finished');
            write_log('File Name: ' . $desination);
            write_log('File Size: ' . $file_size);
            echo '<b>Download Finished<br />File Name: ' . $desination . '<br />File Size: ' . $file_size . '</b>';

            if ($file_size == 0) {
                write_log('ERROR: unable to connect');
                echo 'ERROR: unable to connect<br />';
                $err = 1;
            } else if ($file_size == 1) {
                write_log('ERROR: unable to write to ' . $desination . ' make sure folder has 755 permission');
                echo 'ERROR: unable to write to ' . $desination . ' make sure folder has 755 permission<br />';
                $err = 1;
            } else if ($file_size < 40960) {
                write_log('ERROR: Video size is too small (' . $file_size . '), it should me more than 40 kb');
                echo 'ERROR: Video size is too small (' . $file_size . '), it should me more than 40 kb<br />';
                $err = 1;
            }
            if ($err == 0) {
                $sql = "UPDATE `process_queue` SET
    				   `status`='2',
    				   `file`='$file_name' WHERE
    				   `id`='$vid'";
            } else {
                $sql = "UPDATE `process_queue` SET
    			       `status`=3 WHERE
    			       `id`='$vid'";
            }
            DB::query($sql);
        }
    }
# Process and convert the videos
public static function processVideo($vid, $debug = 1){
        global $config , $db_host , $db_user , $db_pass , $db_name;
        $log_file_name = 'convert_log_' . $vid;
        $video_output_format = Config::get('video_output_format');
        $err = 0;
        $re_convert = 0;
        $re_process_video =0;
# Get video conversion step
        $sql = "SELECT * FROM
               `process_queue` AS `process_queue`,
               `users` AS `users` WHERE
    			process_queue.id=$vid AND
    			process_queue.user=users.user_name";
        $download_info = DB::fetch1($sql);
        $video_file_name = $download_info['file'];
        $re_process_vid = $download_info['vid'];
        $convert_vid = $download_info['id'];
        $status = $download_info['status'];

       if ($status == 2) {
            $sql = "UPDATE `process_queue` SET
    		       `status`='4' WHERE
    		       `id`='$vid'";
            DB::query($sql);
        }

       if ($status == 6) {
            $log_text = '<h2>ERROR: Video Conversion Failed. Current Status is: ' . $status . '. Please Look At The Video Convert And FFMPEG Convert Log For Info.</h2>';
            write_log($log_text, $log_file_name, $debug, 'html');
            exit();
}

       if ($status == 8) {
            $log_text = '<h2>Reprocessing Video....</h2>';
            write_log($log_text, $log_file_name, $debug, 'html');
            $re_process_video = 1;
            $re_convert = 1;
}

        write_log($log_text, $log_file_name, $debug, 'html');
# Check If flvideo exists
        $video_src = FREETUBESITE_DIR . '/video/' . $video_file_name;
        $log_text = '<p>File: ' . $video_src . '</p>';
        write_log($log_text, $log_file_name, $debug, 'html');
        if (! file_exists($video_src)) {
            $log_text = '<h2>ERROR: file not found - ' . $video_src . '</h2>';
            write_log($log_text, $log_file_name, $debug, 'html');
            $err = 1;
        }

        if ($err == 0){
            $pos = strrpos($video_file_name, '.');
            $file_extn = strtolower(substr($video_file_name, $pos + 1, strlen($video_file_name) - $pos));
            $log_text = '<p>$file_extn = ' . $file_extn . '</p>';
            write_log($log_text, $log_file_name, $debug, 'html');
            $seo_name = Url::seoName($download_info['title']);
            $seo_name_org = $seo_name;
            $i = 1;

            while (check_field_exists($seo_name, 'video_seo_name', 'videos')) {
                $seo_name = $seo_name_org . '-' . $i;
                $i ++;
            }
# If this Is The First Upload Make Video Name & Update The Database.
if ($status == 4 && $re_process_video == 0){
				$log_text = '<h1>STARTING VIDEO CONVERSION (process_queue.id = ' . $vid . ')</h1>';
				write_log($log_text, $log_file_name, $debug, 'html');
                $log_text = '<p>$re_process_vid = ' . $re_process_vid . '</p>';
                write_log($log_text, $log_file_name, $debug, 'html');
                $rand1 = $_SERVER['REQUEST_TIME'];
                $rand2 = mt_rand();
                $rand_name = $rand1 . $rand2;
                $outExtn = '.mp4';

# Get Video Extention (Only used if we are using video output other than .mp4 EG. .webm etc.) 
# Removing .flv video output. Keeping code here to use for future .webm output support
/*                if ($video_output_format == 'mp4') {
                    $outExtn = '.mp4';
                } else {
                    $outExtn = '.flv';
                } */
# Make video name
                $rand_flv_name = $rand_name . $outExtn;
                while (check_field_exists($rand_flv_name, 'video_flv_name', 'videos') == 1) {
                    $rand1 = time();
                    $rand2 = mt_rand();
                    $rand_name = $rand1 . $rand2;
                    $rand_flv_name = $rand_name . $outExtn;
                }

                $log_text = '<p>$rand_flv_name = ' . $rand_flv_name . '</p>';
                write_log($log_text, $log_file_name, $debug, 'html');
                $video_folder = floor($convert_vid / 1000) + 1;
                $video_folder = md5($video_folder);
                $video_folder = substr($video_folder, 1, 10);
                $video_folder = trim($video_folder);
                $video_folder .= '/';
                $videoOutFolder = FREETUBESITE_DIR . '/flvideo/' . $video_folder;
                if (! is_dir($videoOutFolder)) {
                    if (! @mkdir($videoOutFolder)) {
                        $log_text ="<p><b>ERROR: Could not create folder $videoOutFolder. Please check permission.</b></p>";
                        write_log($log_text, $log_file_name, $debug, 'html');
                        $sql = "UPDATE `process_queue` SET `status`='6' WHERE `id`=" . (int) $vid;
                        DB::query($sql);
                        return 0;
                    }
                }
                if ($config['approve'] == 1 && Config::get('moderate_video_links') == 1) {
                    if (preg_match('{\b(?:http://)?(www\.)?([^\s]+)*(\.[a-z]{2,3})\b}mi', $download_info['description'])) {
                        $config['approve'] = 0;
                    }
                }
# Update database with video info
                $sql = "INSERT INTO `videos` SET
                       `video_user_id`='" . (int) $download_info['user_id'] . "',
                       `video_seo_name`='" . DB::quote($seo_name) . "',
                       `video_title`='" . DB::quote($download_info['title']) . "',
                       `video_description`='" . DB::quote($download_info['description']) . "',
                       `video_keywords`='" . DB::quote($download_info['keywords']) . "',
                       `video_channels`='0|" . DB::quote($download_info['channels']) . "|0',
                       `video_name`='" . DB::quote($video_file_name) . "',
                       `video_flv_name`='" . DB::quote($rand_flv_name) . "',
                       `video_add_time`='" . DB::quote($_SERVER['REQUEST_TIME']) . "',
                       `video_add_date`='" . date("Y-m-d") . "',
                       `video_type`='" . DB::quote($download_info['type']) . "',
                       `video_active`='0',
                       `video_approve`='" . $config['approve'] . "',
                       `video_adult`='" . (int) $download_info['adult'] . "',
                       `video_folder`='" . DB::quote($video_folder) . "',
                       `video_vtype`='0',
                       `video_location`='',
                       `video_country`='',
                       `video_view_time`='" . DB::quote($_SERVER['REQUEST_TIME']) . "',
                       `video_embed_code`='',
                       `video_voter_id`=''";

                $convert_vid = DB::insertGetId($sql);
                if (! empty($download_info['import_track_id'])) {
                    $sql = "UPDATE `import_track` SET `import_track_video_id`=" . (int) $convert_vid . " WHERE
                           `import_track_id`=" . (int) $download_info['import_track_id'];
                    $result = DB::fetch($sql);
                }

                $log_text = '<hr /><p>sql</p><p>' . $sql . '</p>';
                write_log($log_text, $log_file_name, $debug, 'html');

                $sql = "UPDATE `process_queue` SET
                       `vid`='$convert_vid' WHERE
                       `id`='$vid'";
                DB::query($sql);

                $log_text = "<hr /><p>$sql</p><hr />";
                write_log($log_text, $log_file_name, $debug, 'html');

                if (($download_info['type'] == 'public') && ($config['approve'] == 1)) {
                    $tags = new Tag($download_info['keywords'], $convert_vid, $download_info['user_id'], "0|{$download_info['channels']}|0");
                    $tags->add();
                    $video_tags = $tags->get_tags();
                    $video_keywords = implode(' ', $video_tags);
                    $sql = "UPDATE `videos` SET
                            `video_keywords`='" . DB::quote($video_keywords) . "' WHERE
    						`video_id`='" . (int) $convert_vid . "'";
                    DB::query($sql);
                    unset($tags);
                }
            }
# If We Are Re Processing The Video Just Reconvert & Make Thumbs Again.
if ($re_process_video == 1) {
				$log_text = '<h1>START RECONVERT VIDEO (process_queue.id = ' . $vid . ')</h1>';
				write_log($log_text, $log_file_name, $debug, 'html');
                $log_text = '<p>$re_process_vid = ' . $re_process_vid . '</p>';
                write_log($log_text, $log_file_name, $debug, 'html');
                
                $sql = "UPDATE `videos` SET
    				   `video_active`='0' WHERE
    				   `video_id`='$re_process_vid'";
                DB::query($sql);
                
				$convert_vid = $re_process_vid;
				$video_info = Video::getById($convert_vid);
				$rand_flv_name = $video_info['video_flv_name'];
				$video_folder = $video_info['video_folder'];
				$re_convert = 1;
				DB::freeResult();

                $videoOutFolder = FREETUBESITE_DIR . '/flvideo/' . $video_folder;
                if (! is_dir($videoOutFolder)) {
                    if (! @mkdir($videoOutFolder)) {
                        $log_text ="<p><b>ERROR: Could not create folder $videoOutFolder. Please check permission.</b></p>";
                        write_log($log_text, $log_file_name, $debug, 'html');
                        $sql = "UPDATE `process_queue` SET `status`='6' WHERE `id`=" . (int) $vid;
                        DB::query($sql);
                        return 0;
                }}
                $log_text = '<hr /><p>' . $sql . '</p><hr />';
                write_log($log_text, $log_file_name, $debug, 'html');
            }
# Convert The Video With FFMPEG
if ($status == 4 || $re_convert ==1){
            $log_text = '<p>Video id: ' . $convert_vid . '</p>';
            write_log($log_text, $log_file_name, $debug, 'html');
            $video_flv = FREETUBESITE_DIR . '/flvideo/' . $video_folder . $rand_flv_name; #flv(mp4) video dir
            $log_text = 'Video Output File Name : ' . $video_flv;
            write_log($log_text, $log_file_name, $debug, 'html');
            $log_text = '<p>$video_flv = ' . $log_text . '</p>';
            write_log($log_text, $log_file_name, $debug, 'html');
            DB::close();
# This is to set max bitrate for generated videos
/*
            $videoBitrate = (int) VideoBitrate::find($video_src);
            $videoBitrateMax = 500;
            if ($videoBitrate > $videoBitrateMax) {
                $videoBitrate = 500;
            }

            $log_text = "<p>Source Video bitrate: $videoBitrate</p>";
            write_log($log_text, $log_file_name, $debug, 'html');
*/
# Convert Video
            if (! is_dir(FREETUBESITE_DIR . '/flvideo/ffmpeg_output')) {
                mkdir(FREETUBESITE_DIR . '/flvideo/ffmpeg_output');
            }
				$ffmpeg_out = FREETUBESITE_DIR . '/flvideo/ffmpeg_output/'. $vid . '.txt';
                require FREETUBESITE_DIR . '/include/settings/video_conversion.php';
                $cmd_convert_v = 'convert_' . $file_extn;
                $cmd_convert = $cmd_mp4;
                $log_text = '<p>$cmd_convert_v = ' . $cmd_convert_v . '</p>';
                write_log($log_text, $log_file_name, $debug, 'html');
                $log_text = '<p>' . __LINE__ . ' $cmd_convert = ' . $cmd_convert . '</p>';
                write_log($log_text, $log_file_name, $debug, 'html');
				$tmp = exec("($cmd_convert $exec_result)"); # Convert The Video
                $return_data_all = '';

                for ($i = 0; $i < count($exec_result); $i ++) {
                    $return_data = $exec_result[$i];
                    $return_data = trim($return_data);
                    $return_data = $return_data . "\n";
                    $return_data_all = $return_data_all . $return_data;
                }

                $log_text = '<hr /><pre>' . $return_data_all . '</pre><hr />';
                write_log($log_text, $log_file_name, $debug, 'html');
                $log_text = '<p>Return value: ' . $tmp . '</p>';
                write_log($log_text, $log_file_name, $debug, 'html');

require FREETUBESITE_DIR . '/include/ffmpeg_output.php';
if ($status == 4 || $re_convert ==1) {
	echo "<b>If Page Dose Not continue <a href=\"./check_convert_status.php?id=$vid\">Just Click Here To Begin The Video Conversion.</a></b>";
	echo "<meta http-equiv=\"Refresh\" content=\"0; url=./check_convert_status.php?id=$vid\" />";
}
}
# After Video Conversion Is Done, Finish The Job....
if ($status == 7 && $err == 0){
		$convert_vid = $re_process_vid;
		$video_info = Video::getById($convert_vid);
		$rand_flv_name = $video_info['video_flv_name'];
		$video_folder = $video_info['video_folder'];
		$video_flv = FREETUBESITE_DIR . '/flvideo/' . $video_folder . $rand_flv_name; 
		DB::freeResult();
# Get The Video Time H:M.S
	$log_text = '<h2>Find video duration</h2>';
	write_log($log_text, $log_file_name, $debug, 'html');
	$tool_video_thumb = Config::get('tool_video_thumb');
	DB::close();
	$videoDuration = VideoDuration::find($video_src, $tool_video_thumb, $debug);
	$log_text = "<p>Duration ($tool_video_thumb): $videoDuration</p>";
	write_log($log_text, $log_file_name, $debug, 'html');
	$videoDurationHMS = sec2hms($videoDuration); //covert to 00:00:00 i.e. hrs:min:sec
	$log_text = "<p>DURATION: $videoDurationHMS</p>";
	write_log($log_text, $log_file_name, $debug, 'html');
# Create thumnail with mplayer/ffmpeg
	$log_text = "<h2>Create Thumbnail - START</h2>";
	write_log($log_text, $log_file_name, $debug, 'html');
if (! is_dir(FREETUBESITE_DIR . '/thumb/' . $video_folder)) {
mkdir(FREETUBESITE_DIR . '/thumb/' . $video_folder);
}
	$t_info = array();
	$t_info['src'] = $video_src;
	$t_info['vid'] = $convert_vid;
	$t_info['duration'] = $videoDuration;
	$t_info['video_folder'] = $video_folder;
	$t_info['debug'] = $debug;
	$t_info['tool'] = $tool_video_thumb;
	$tmp = VideoThumb::make($t_info);
	$log_text = "<p>Create Thumbnail with $tool_video_thumb - END</p>";
	write_log($log_text, $log_file_name, $debug, 'html');
# Inject The mp4 Metadata & Update The Database Once The FFMPEG Convert Is Done
            DB::connect($db_host, $db_user, $db_pass, $db_name);
# insert Video metadata
if ($video_output_format == 'mp4') {
	$log_text = "<p>Injecting mp4 Video Metadata.</p>";

	write_log($log_text, $log_file_name, $debug, 'html');
	Media::mp4Metadata($rand_flv_name, $video_folder, $log_file_name, $debug);
            }
            if (! file_exists($video_flv)) {
                    $log_text = "<p>Convert Error $video_flv DOSE NOT EXIST, verify convert command works on the server.</p>";
                    write_log($log_text, $log_file_name, $debug, 'html');
                $err = 1;
            }else{
                $flv_size = filesize($video_flv);

                if ($flv_size < 1024) {
                    $log_text = "<p>Convert Error or converted video is less than 1 kb in size, try to upload a bigger video. If that do not work, verify convert command works on the server.</p>";
                    write_log($log_text, $log_file_name, $debug, 'html');
                    $err = 1;
                }else{
                    $flv_size = $flv_size / (1024 * 1024);
                    $flv_size = round($flv_size, 2);

                    $sql = "UPDATE `videos` SET
                           `video_flv_name`='" . DB::quote($rand_flv_name) . "',
                           `video_space`=$flv_size,
                           `video_duration`='$videoDuration',
                           `video_length`='$videoDurationHMS' WHERE
                           `video_id`=$convert_vid";
                    DB::query($sql);
//echo "<b><p>DURATION: $videoDuration</p></b>";
//echo "<b><p>DURATION: $videoDurationHMS</p></b>";
                    if ($re_process_vid > 0 && $rand_flv_name != $rand_flv_name_old) {
                        $video_flv_old = FREETUBESITE_DIR . '/flvideo/' . $video_folder . $rand_flv_name_old;
                        if (file_exists($video_flv_old)) {
                            unlink($video_flv_old);
                            $log_text = "<p>Old output file($video_flv_old) found and deleted.</p>";
                            write_log($log_text, $log_file_name, $debug, 'html');
			}}}}
# Upload to remote server after video convert has finished
# Check if video needs to be upload to remote server
            $must_upload_to_remote = 0;
            $re_convert_server_id = 0;
            $upload_to_ftp = 1;
            if ($re_process_vid > 0) {
                $re_convert_server_id = $video_info['video_server_id'];
                $must_upload_to_remote = 1;
                $log_text = '<p>video already processed, so it must go to same server. (video_server_id = ' . $re_convert_server_id . ')</p>';
                write_log($log_text, $log_file_name, 1, 'html');
                if ($re_convert_server_id == 0) {
                    $upload_to_ftp = 0;
            }}
            global $servers;
            if (count($servers) < 2) {
                $upload_to_ftp = 0;
            }
            if ($upload_to_ftp) {
                $ftp_config = array();
                $ftp_config['video_id'] = $convert_vid;
                $ftp_config['must_upload'] = $must_upload_to_remote;
                $ftp_config['log_file_name'] = $log_file_name;
                $ftp_config['debug'] = $debug;
                $ftp = new Ftp();
                $ftp->upload_video($ftp_config);
                $ftp->upload_thumb($ftp_config);

                if ($re_process_vid > 0 && $rand_flv_name != $rand_flv_name_old) {
                    if ($ftp->delete_video($ftp_config)) {
                        $log_text = "<p>Old output file found and deleted (video_server_id = " . $video_info['video_server_id'] . ").</p>";
                        write_log($log_text, $log_file_name, $debug, 'html');
			}}}
# If no error update database after video convert has finished
            $sql = "UPDATE `process_queue` SET
    		       `status`='5' WHERE
    		       `id`=$vid";
            DB::query($sql);

if ($re_process_vid == 0) {
                $sql = "UPDATE `subscriber` SET
                       `used_space`=`used_space`+$flv_size,
    				   `total_video`=`total_video`+1 WHERE
    				   `UID`='" . (int) $download_info['user_id'] . "'";
                DB::query($sql);
         } # END- if ($re_process_vid == 0) (To Update Database)

            $sql = "UPDATE `videos` SET
    			   `video_active`='1' WHERE
    			   `video_id`='$convert_vid'";
            DB::query($sql);
//echo "<br><b>convert_vid = </b>" . $convert_vid . "<br>";
            $log_text = '<p>' . $sql . '</p>';
            write_log($log_text, $log_file_name, $debug, 'html');
            $log_text = '<h1>Video Conversion Finished</h1>';
            write_log($log_text, $log_file_name, $debug, 'html');
            return $convert_vid;

if ($re_process_video == 0) {
# Notify Admin Of Upload
                $video_url = FREETUBESITE_URL . '/view/' . $convert_vid . '/' . $seo_name . '/';
                if ($config['notify_upload'] == 1) {
                    $ch_id = str_replace('|', ',', $download_info['channels']);
                    $channels_name = '';
                    $sql = "SELECT * FROM `channels` WHERE
                           `channel_id` IN ($ch_id)";
                    $channels_all = DB::fetch($sql);

                    foreach ($channels_all as $channel) {
                        $channels_name .= $channel['channel_name'];
                        $channels_name .= '&nbsp;';
                    }

                    $sql = "SELECT * FROM `email_templates` WHERE
    				       `email_id`='upload_notify_admin'";
                    $email_info = DB::fetch1($sql);
                    $email_subject = $email_info['email_subject'];
                    $email_body = $email_info['email_body'];
                    $email_subject = str_replace('[SITE_NAME]', $config['site_name'], $email_subject);
                    $email_subject = str_replace('[USERNAME]', $download_info['user_name'], $email_subject);
                    $email_body = str_replace('[USER_ID]', $download_info['user_id'], $email_body);
                    $email_body = str_replace('[USER_IP]', $download_info['process_queue_upload_ip'], $email_body);
                    $email_body = str_replace('[USERNAME]', $download_info['user_name'], $email_body);
                    $email_body = str_replace('[TITLE]', $download_info['title'], $email_body);
                    $email_body = str_replace('[DESCRIPTION]', $download_info['description'], $email_body);
                    $email_body = str_replace('[KEYWORDS]', $download_info['keywords'], $email_body);
                    $email_body = str_replace('[CHANNELS]', $channels_name, $email_body);
                    $email_body = str_replace('[TYPE]', $download_info['type'], $email_body);
                    $email_body = str_replace('[VIDEO_URL]', $video_url, $email_body);
                    $msg = array();
                    $msg['from_email'] = $config['admin_email'];
                    $msg['from_name'] = $config['site_name'];
                    $msg['to_email'] = $config['admin_email'];
                    $msg['to_name'] = $config['site_name'];
                    $msg['subject'] = $email_subject;
                    $msg['body'] = $email_body;
                    $mail = new Mail();
                    $mail->send($msg);
                } # END- if ($config['notify_upload'] == 1)
# Notify User Of Upload
                $process_notify_user = Config::get('process_notify_user');
                if ($process_notify_user == 1) {
                    $ch_id = str_replace('|', ',', $download_info['channels']);
                    $channels_name = '';
                    $sql = "SELECT * FROM `channels` WHERE
                           `channel_id` IN ($ch_id)";
                    $channels_all = DB::fetch($sql);

                    foreach ($channels_all as $channel) {
                        $channels_name .= $channel['channel_name'];
                        $channels_name .= '&nbsp;';
                    }

                    $sql = "SELECT * FROM `email_templates` WHERE
    				       `email_id`='upload_notify_user'";
                    $tmp = DB::fetch1($sql);
                    $email_subject = $tmp['email_subject'];
                    $email_body_tmp = $tmp['email_body'];
                    $email_subject = str_replace('[VIDEO_TITLE]', $download_info['title'], $email_subject);
                    $email_subject = str_replace('[SITE_NAME]', $config['site_name'], $email_subject);
                    $email_body = str_replace('[USER_ID]', $download_info['user_id'], $email_body_tmp);
                    $email_body = str_replace('[USER_IP]', $download_info['process_queue_upload_ip'], $email_body);
                    $email_body = str_replace('[USERNAME]', $download_info['user'], $email_body);
                    $email_body = str_replace('[VIDEO_TITLE]', $download_info['title'], $email_body);
                    $email_body = str_replace('[DESCRIPTION]', $download_info['description'], $email_body);
                    $email_body = str_replace('[KEYWORDS]', $download_info['keywords'], $email_body);
                    $email_body = str_replace('[CHANNELS]', $channels_name, $email_body);
                    $email_body = str_replace('[TYPE]', $download_info['type'], $email_body);
                    $email_body = str_replace('[VIDEO_URL]', $video_url, $email_body);
                    $msg = array();
                    $msg['from_email'] = $config['admin_email'];
                    $msg['from_name'] = $config['site_name'];
                    $msg['to_email'] = $download_info['user_email'];
                    $msg['to_name'] = $download_info['user_name'];
                    $msg['subject'] = $email_subject;
                    $msg['body'] = $email_body;
                    $mail = new Mail();
                    $mail->send($msg);
            } # END- if ($process_notify_user == 1)
          } # END- if ($re_process_video == 0) (For Email Notify)
         } # END- if no error update database after video convert has finished
        } # END- Check If video exists
# If An Error Happens Update The Status To 6 & Display Failed Message.
        if ($err == 1){
            $sql = "UPDATE `process_queue` SET
    		       `status`='6' WHERE
    		       `id`='$vid'";
            DB::query($sql);

            $log_text = '<p>' . $sql . '</p>';
            write_log($log_text, $log_file_name, $debug, 'html');
            $log_text = '<p><b>ERROR: failed video conversion. Please check the convert log for info.</b></p>';
            write_log($log_text, $log_file_name, $debug, 'html');
            return 0;
        }
     } # END- public static function processVideo
}
