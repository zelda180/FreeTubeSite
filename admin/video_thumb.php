<?php
/**************************************************************************************
 * PROJECT: FreeTubeSite Youtube Clone
 * VERSION: 0.1.0-ALPHA
 * LICENSE: https://raw.githubusercontent.com/zelda180/FreeTubeSite/master/LICENSE
 * WEBSITE: https://github.com/zelda180/FreeTubeSite
 * 
 * Feel Free To Donate Any Amount Of Digital Coins To The Addresses Below. Please 
 * Contact Us At Our Website If You Would Like To Donate Another Coin or Altcoin.
 * 
 * Donate BitCoin (BTC)    : 3Amhpt1v3jT5NYV7vdjx8PNUcsH4ccrn79
 * Donate LiteCoin (LTC)   : LSNpxsXTPH1a4YaeVjqQwGyu1fNea8dSLV
 *
 * FreeTubeSite is a free and open source video sharing site ( YouTube Clone Script ).
 * You are free to use and modify this script as you wish for commercial and non 
 * commercial use, within the GNU v3.0 (General Public License). We just ask that you 
 * keep our ads and credits unedited unless you have donated to the FreeTubeSite project,
 * by BitCoin, altcoin or you can contribute your code to our GitHub project. Then you 
 * may remove our ads but we ask that you leave our FreeTubeSite bottom links so that 
 * others may find and/or contribute to this project to benefit others too. Thank You,
 * 
 * The FreeTubeSite Team :)
 **************************************************************************************/

require 'admin_config.php';
require '../include/config.php';
require '../include/language/' . LANG . '/admin/video_thumb.php';

Admin::auth();

if (is_numeric($_GET['id'])) {

    ob_start();

    $video_info = Video::getById($_GET['id']);
    $video_src = FREETUBESITE_DIR . '/video/' . $video_info['video_name'];
    $log_file_name = 're_create_thumb_' . $video_info['video_id'];

    if ($config['debug']) {
        $log_text = '<p>$video_src = ' . $video_src . '</p>';
        write_log($log_text, $log_file_name, $config['debug'], 'html');
    }

    if (file_exists($video_src) && is_file($video_src)) {
        if ($config['debug']) {
            $log_text = '<p>File found = ' . $video_src . '</p>';
            write_log($log_text, $log_file_name, $config['debug'], 'html');
        }

        if ($video_info['video_folder'] != '') {
            if (! is_dir(FREETUBESITE_DIR . '/thumb/' . $video_info['video_folder'])) {
                mkdir(FREETUBESITE_DIR . '/thumb/' . $video_info['video_folder']);
            }
        }

        if ($config['debug']) {
            $log_text = '<p>thumb_folder = ' . FREETUBESITE_DIR . '/thumb/' . $video_info['video_folder'] . '</p>';
            write_log($log_text, $log_file_name, $config['debug'], 'html');
        }

        $thumb_data = array();
        $thumb_data['src'] = $video_src;
        $thumb_data['vid'] = (int) $_GET['id'];
        $thumb_data['video_folder'] = $video_info['video_folder'];
        $thumb_data['debug'] = $config['debug'];

        $tool_video_thumb = Config::get('tool_video_thumb');
        $thumb_data['tool'] = $tool_video_thumb;

        $tmp = VideoThumb::make($thumb_data);

        if ($video_info['video_thumb_server_id'] > 0) {
            if ($config['debug']) {
                $log_text = '<p>$video_info[\'video_thumb_server_id\'] = ' . $video_info['video_thumb_server_id'] . '</p>';
                write_log($log_text, $log_file_name, $config['debug'], 'html');
            }
            $ftp_config = array();
            $ftp_config['debug'] = $config['debug'];
            $ftp_config['video_id'] = $_GET['id'];
            $ftp_config['log_file_name'] = 'log_create_thumb';
            $ftp_config['must_upload'] = 1;
            $ftp = new Ftp();
            $ftp->upload_thumb($ftp_config);
        }

        $debug_log = ob_get_contents();
        ob_end_clean();

        $smarty->assign('debug_log', $debug_log);
        $msg = str_replace('[FIND_WIDTH]', $tool_video_thumb, $lang['thumb_created']);
        $smarty->assign('video_folder', $video_info['video_folder']);
        $video_thumb_url = $servers[$video_info['video_thumb_server_id']];
        $smarty->assign('video_thumb_url', $video_thumb_url);
    } else {
        $err = str_replace('[VIDEO_SRC]', $video_src, $lang['file_not_found']);
    }
} else {
    $err = $lang['video_id_invalid'];
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/video_thumb.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
