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
Admin::auth();
$err = 0;

if (isset($_POST['submit'])) {

    $videos = $_POST['local_videos'];
    $server_id = (int) $_POST['server'];

    $ftp_config = array();
    $ftp_config['must_upload'] = 0;
    $ftp_config['debug'] = $config['debug'];
    $ftp = new Ftp();

    for ($i = 0; $i < count($videos); $i ++) {
        $ftp_config['server_id'] = $server_id;
        $ftp_config['video_id'] = (int) $videos[$i];
        $ftp_config['log_file_name'] = 'move_video_' . $ftp_config['video_id'];
        $ftp->upload_video($ftp_config);
        $ftp_config['log_file_name'] = 'move_video_jpg' . $ftp_config['video_id'];
        $ftp_config['server_id'] = '';
        $ftp->upload_thumb($ftp_config);
    }
}

DB::close();

if ($config['debug'] == 0) {
    $redirect_url = FREETUBESITE_URL . '/admin/video_local.php';
    Http::redirect($redirect_url);
}
