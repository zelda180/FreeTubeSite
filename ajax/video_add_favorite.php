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
<?php
require '../include/config.php';
require '../include/language/' . LANG . '/lang_video_add_favorite.php';
if (! isset($_SESSION['UID'])) {
    Ajax::returnJson($lang['user_must_login'], 'error');;
    exit(0);
}
$video_id = isset($_POST['video_id']) ? $_POST['video_id'] : '';

if (! is_numeric($video_id)) {
    Ajax::returnJson($lang['hacking'], 'error');
    exit(0);
}
$video_info = Video::getById($video_id);

if ($_SESSION['UID'] == $video_info['video_user_id']) {
    Ajax::returnJson($lang['favorite_self'], 'error');
    exit(0);
}
$sql = "SELECT * FROM `favourite` WHERE
       `favourite_user_id`=" . (int) $_SESSION['UID'] . " AND
       `favourite_video_id`='" . (int) $video_id . "'";
$is_favourite = DB::fetch($sql);

if (! $is_favourite) {
    $sql = "INSERT INTO `favourite` SET
	       `favourite_user_id`='" . (int) $_SESSION['UID'] . "',
	       `favourite_video_id`='" . (int) $video_id . "'";
    DB::query($sql);
    $sql = "UPDATE `videos` SET
	       `video_fav_num`=`video_fav_num`+1
	        WHERE `video_id`=" . (int) $video_id;
    DB::query($sql);
    Ajax::returnJson($lang['favorite_added'], 'success');
} else {
    Ajax::returnJson($lang['favorite_exists'], 'success');
}
DB::close();
