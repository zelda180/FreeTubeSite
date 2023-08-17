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
require 'include/language/' . LANG . '/lang_upload.php';

if (Config::get('guest_upload') != 1) {
    User::is_logged_in();
    if ($config['enable_package'] == 'yes') {
        check_subscriber_space($_SESSION['UID']);
        check_subscriber_videos($_SESSION['UID']);
    }
}

$num_max_channels = Config::get('num_max_channels');
$smarty->assign('num_max_channels', $num_max_channels);

if (isset($_POST['action_upload'])) {

    if (get_magic_quotes_gpc()) {
        $_POST['video_keywords'] = stripslashes($_POST['video_keywords']);
        $_POST['video_title'] = stripslashes($_POST['video_title']);
        $_POST['video_description'] = stripslashes($_POST['video_description']);
    }

    $channel = isset($_POST['channel']) ? (int) $_POST['channel'] : 0;

    $_POST['video_description'] = Xss::clean($_POST['video_description']);
    $_POST['video_title'] = htmlspecialchars_uni($_POST['video_title']);
    $_POST['video_keywords'] = strip_tags($_POST['video_keywords']);

    if (strlen_uni($_POST['video_title']) < 4) {
        $err = $lang['title_too_short'];
    } else if (strlen_uni($_POST['video_description']) < 14) {
        $err = $lang['description_too_short'];
    } else if (strlen_uni($_POST['video_keywords']) < 8) {
        $err = $lang['tags_too_short'];
    } else if (! check_field_exists($channel, 'channel_id', 'channels')) {
        $err = $lang['channel_not_selected'];
    }

    $upload_from = isset($_POST['upload_from']) ? $_POST['upload_from'] : 'local';

    if ($_POST['field_privacy'] != 'public') {
        $_POST['field_privacy'] = 'private';
    }

    if ($_POST['video_adult'] != 1) {
        $_POST['video_adult'] = 0;
    }

    $upload_id = md5($_SERVER['REQUEST_TIME'] . rand(1, 2000));
    $upload_info = array();
    $upload_info['title'] = $_POST['video_title'];
    $upload_info['description'] = $_POST['video_description'];
    $upload_info['keywords'] = $_POST['video_keywords'];
    $upload_info['channels'] = $channel;
    $upload_info['field_privacy'] = $_POST['field_privacy'];
    $upload_info['adult'] = $_POST['video_adult'];
    $upload_info['type'] = $_POST['field_privacy'];

    $_SESSION["$upload_id"] = $upload_info;

    if ($err == '') {
        if ($upload_from == 'remote') {
            $redirect_url = FREETUBESITE_URL . '/upload_remote.php?upload_id=' . $upload_id;
        } else {
            $redirect_url = FREETUBESITE_URL . '/upload_file.php?id=' . $upload_id;
        }
        Http::redirect($redirect_url);
    }
}

$channels = Channel::get();

$smarty->assign('channel_info', $channels);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('upload.tpl');
$smarty->display('footer.tpl');
DB::close();
