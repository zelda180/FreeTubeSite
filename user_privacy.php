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
require 'include/language/' . LANG . '/lang_user_privacy.php';

User::is_logged_in();

$user_info = User::getById($_SESSION['UID']);

if (isset($_POST['submit'])) {
    $sql = "UPDATE `users` SET
		   `user_friend_invition`=" . (int) $_POST['user_friend_invition'] . ",
		   `user_private_message`=" . (int) $_POST['user_private_message'] . ",
		   `user_profile_comment`=" . (int) $_POST['user_profile_comment'] . ",
		   `user_favourite_public`=" . (int) $_POST['user_favourite_public'] . ",
		   `user_playlist_public`=" . (int) $_POST['user_playlist_public'] . ",
		   `user_subscribe_admin_mail`=" . (int) $_POST['user_subscribe_admin_mail'] . "
		    WHERE `user_id`='" . (int) $_SESSION['UID'] . "'";
    DB::query($sql);
    set_message($lang['settings_updated'], 'success');
    $redirect_url = FREETUBESITE_URL . '/' . $_SESSION['USERNAME'];
    Http::redirect($redirect_url);
}

$smarty->assign('user_info', $user_info);
$smarty->display('header.tpl');
$smarty->display('user_privacy.tpl');
$smarty->display('footer.tpl');
DB::close();
