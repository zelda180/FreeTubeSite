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
require 'include/language/' . LANG . '/lang_video_edit.php';

User::is_logged_in();

$video_id = isset($_GET['video_id']) ? (int) $_GET['video_id'] : 0;
$num_max_channels = Config::get('num_max_channels');
$smarty->assign('num_max_channels', $num_max_channels);

if (isset($_POST['submit'])) {
    $video_id = $_POST['video_id'];
    $video = new Video();
    $video->video_id = $video_id;
    $video->video_title = $_POST['video_title'];
    $video->video_description = $_POST['video_description'];
    $video->video_keywords = $_POST['video_keywords'];
    $video->video_channels = $_POST['channel_list'];
    $video->video_type = $_POST['video_type'];
    $video->video_allow_comment = $_POST['video_allow_comment'];
    $video->video_allow_rated = $_POST['video_allow_rated'];
    $video->video_allow_embed = $_POST['video_allow_embed'];
    $save = $video->video_update();

    if ($save == 1) {
        set_message($lang['video_info_update'], 'success');
        $redirect_url = FREETUBESITE_URL . '/view/' . $video_id . '/' . $video->video_info['video_seo_name'] . '/';
        Http::redirect($redirect_url);
    } else {
        $err = $save;
    }
}

$video_info = Video::getById($video_id);

if ($video_info['video_user_id'] == $_SESSION['UID']) {
    $video_info['video_description'] = str_replace(array('<p>', '</p>'), '', $video_info['video_description']);
    $is_video_owner = 1;
} else {
    $err = $lang['video_owner'];
    $is_video_owner = 0;
}

$chid = explode('|', $video_info['video_channels']);

$smarty->assign('channels_all', Channel::get());
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('chid', $chid);
$smarty->assign('video_info', $video_info);
$smarty->display('header.tpl');
if ($is_video_owner) $smarty->display('video_edit.tpl');
$smarty->display('footer.tpl');
DB::close();
