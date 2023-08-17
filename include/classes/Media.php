<?php
class Media{
    public static function mp4Metadata($mp4_name, $video_folder, $log_file_name, $debug){
        global $config;

        if (! isset($config['qt-faststart'])) {
            $log_text = '<h3 class="text-danger">ERROR: qt-faststart path not set.</h3>';
            $log_text .= '<p class="text-danger">Add below code in <b>include/config.php</b> file to set it.</p>';
            $log_text .= "<pre class=\"text-danger\">\$config['qt-faststart'] = '/path/to/qt-faststart';</pre>";
            write_log($log_text, $log_file_name, $debug, 'html');
            return ;
        }

        $video_metadata_mp4 = FREETUBESITE_DIR . '/flvideo/' . $video_folder . 'metadata_' . $mp4_name;
        $video_mp4 = (string) FREETUBESITE_DIR . '/flvideo/' . $video_folder . $mp4_name;
        $video_mp4 = trim($video_mp4);

        if (file_exists($video_mp4)) {
            $log_text = "Metadata Mp4 : $video_metadata_mp4";
            write_log($log_text, $log_file_name, $debug, 'html');
            $cmd_qt_faststart = $config['qt-faststart'] . " $video_mp4 $video_metadata_mp4";
            $tmp = exec($cmd_qt_faststart, $exec_result);
            $log_text = "<h2>Running qt-faststart: $cmd_qt_faststart</h2>";
            write_log($log_text, $log_file_name, $debug, 'html');
            $return_data_all = '';

            for ($i = 0; $i < count($exec_result); $i ++) {
                $return_data = $exec_result[$i];
                $return_data = trim($return_data);
                $return_data = $return_data . "\n";
                $return_data_all = $return_data_all . $return_data;
            }

            $log_text = '<hr /><pre>' . $return_data_all . '</pre><hr />';
            write_log($log_text, $log_file_name, $debug, 'html');

            if (file_exists($video_metadata_mp4)) {
                unlink($video_mp4);
                rename($video_metadata_mp4, FREETUBESITE_DIR . '/flvideo/' . $video_folder . $mp4_name);
                $log_text = "Rename : $video_metadata_mp4---$mp4_name";
                write_log($log_text, $log_file_name, $debug, 'html');
            }
        }
    }
}
