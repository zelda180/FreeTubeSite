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
require 'include/language/' . LANG . '/lang_group_invite_confirm.php';

User::is_logged_in();

$group_url = htmlspecialchars_uni($_GET['group_url']);

$sql = "SELECT * FROM `groups` WHERE
       `group_url`='" . DB::quote($group_url) . "'";
$group_info = DB::fetch1($sql);

if (! $group_info) {
    Http::redirect(FREETUBESITE_URL);
}

if (! isset($_GET['key'])) {
    DB::close();
    Http::redirect(FREETUBESITE_URL);
}

$sql = "SELECT * FROM `verify_code` AS v,
       `groups` AS g WHERE
        v.vkey='" . DB::quote($_GET['key']) . "' AND
        v.data1=g.group_id";
$verify_info = DB::fetch1($sql);

if (! $verify_info) {
    DB::close();
    set_message($lang['invalid_invite_key'], 'error');
    $redirect_url = FREETUBESITE_URL . '/group/' . $_GET['group_url'] . '/';
    Http::redirect($redirect_url);
}

$join_group_id = $group_info['group_id'];

$sql = "SELECT * FROM `group_members` WHERE
       `group_member_group_id`='" . (int) $join_group_id . "' AND
       `group_member_user_id`='" . (int) $_SESSION['UID'] . "'";
$is_member = DB::fetch1($sql);

if (! $is_member) {
    $sql = "INSERT INTO `group_members` SET
           `group_member_group_id`='" . (int) $join_group_id . "',
           `group_member_user_id`='" . (int) $_SESSION['UID'] . "',
           `group_member_since`='" . date("Y-m-d H:i:s") . "',
           `group_member_approved`='yes'";
    DB::query($sql);
    $msg = $lang['user_added'];
}

$sql = "DELETE FROM `verify_code` WHERE
       `vkey`='" . DB::quote($_GET['key']) . "'";
DB::query($sql);
DB::close();

$smarty->assign('accept_mem', 'true');

set_message($msg, 'success');

$redirect_url = FREETUBESITE_URL . '/group/' . $_GET['group_url'] . '/';
Http::redirect($redirect_url);
