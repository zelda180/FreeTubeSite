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

require 'include/config.php';
require 'include/language/' . LANG . '/lang_upload_success.php';

$guest_upload = Config::get('guest_upload');

if ($guest_upload == 0) {
    User::is_logged_in();
}

$upload_id = $_GET['upload_id'];

if ($upload_id != 'remote') {

    $id = $_GET['id'];

    if (! is_numeric($id)) {
        echo 'Invalid id';
        exit(0);
    }

    $sql = "SELECT * FROM `process_queue` WHERE
           `id`='" . (int) $id . "'";
    $queue_info = DB::fetch1($sql);
    $status = $queue_info['status'];
    $user = $queue_info['user'];

    if ($guest_upload == 0) {
        if ($user != $_SESSION['USERNAME']) {
            echo "Invalid user";
            exit(0);
        }
    }

    if ($status == 5) {
        $video_processed = 1;
        $vid = $queue_info['vid'];
        $video_info = Video::getById($vid);
        $video_flv_name = $video_info['video_flv_name'];
        $video_info['video_thumb_url'] = $servers[$video_info['video_thumb_server_id']];

        if (! $video_info) {
            $err = $lang['video_not_found'];
        }

        $smarty->assign('video_info', $video_info);
    } else {
        $video_processed = 0;
    }

} else {
    $vid = $_GET['id'];
    $video_info = Video::getById($vid);

    $video_flv_name = $video_info['video_flv_name'];

    if (! $video_info) {
        $err = $lang['video_not_found'];
    }

    $smarty->assign('video_info', $video_info);
    $video_processed = 1;
}

if ($video_processed == 1) {
    if ($video_info['video_vtype'] == 0) {
        if ($video_info['video_server_id'] == 0) {
            $flv_url = FREETUBESITE_URL . '/flvideo/' . $video_info['video_folder'] . $video_info['video_flv_name'];
        } else {
            $sql = "SELECT * FROM `servers` WHERE
                   `id`='" . (int) $video_info['video_server_id'] . "'";
            $server_info = DB::fetch1($sql);
            $flv_url = $server_info['url'] . '/' . $video_info['video_folder'] . $video_info['video_flv_name'];
        }

        $smarty->assign('flv_url', $flv_url);
    }

    if ($video_info['video_active'] == 1 && $video_info['video_approve'] == 1) {
        if (isset($_SESSION['UID'])) {
	        User::updateVideoCount($_SESSION['UID'], 1);
        } else if ($guest_upload == 1) {
            $guest_upload_user = Config::get('guest_upload_user');
	        $user_info = User::getByName($guest_upload_user);
	        User::updateVideoCount($user_info['user_id'], 1);
        }
    }
}

if ($upload_id != 'remote') {
    unset($_SESSION["$upload_id"]['field_privacy']);
    unset($_SESSION["$upload_id"]['description']);
    unset($_SESSION["$upload_id"]['title']);
    unset($_SESSION["$upload_id"]['keywords']);
    unset($_SESSION["$upload_id"]['channels']);
    unset($_SESSION["$upload_id"]['adult']);
}

$smarty->assign('video_processed', $video_processed);
$smarty->assign('err', $err);
if (isset($_GET['vid'])) $smarty->assign('vidid', $_GET['vid']);
$smarty->display('header.tpl');
$smarty->display('upload_success.tpl');
$smarty->display('footer.tpl');
DB::close();
