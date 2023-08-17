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
require 'include/language/' . LANG . '/lang_group_join.php';

User::is_logged_in();

$group_id = isset($_GET['group_id']) ? $_GET['group_id'] : 0;

$uer_info = User::get_user_by_id($_SESSION['UID']);

if ($uer_info == 0) {
    set_message($lang['group_security_error'], 'error');
    $redirect_url = FREETUBESITE_URL . '/';
    Http::redirect($redirect_url);
}

$group_info = Group::getById($group_id);

if ($group_info == 0) {
    set_message($lang['group_security_error'], 'error');
    $redirect_url = FREETUBESITE_URL . '/';
    Http::redirect($redirect_url);
}

$_GET['action'] = isset($_GET['action']) ? $_GET['action'] : '';

if ($_GET['action'] == 'join') {
    if ($group_info['group_type'] == 'protected') {
        $approved = 'no';
    } else {
        $approved = 'yes';
    }

    $sql = "SELECT * FROM `group_members` WHERE
           `group_member_group_id`='" . (int) $group_info['group_id'] . "' AND
           `group_member_user_id`='" . (int) $_SESSION['UID'] . "'";
    $group_member_info = DB::fetch1($sql);

    if ($group_member_info) {
        if ($group_info['group_type'] == 'protected' && $group_member_info['group_member_approved'] == 'no') {
            $msg = $lang['group_membership_requested'];
        } else {
            $msg = $lang['group_join_ok'];
        }
    } else {
        $sql = "INSERT INTO `group_members` SET
               `group_member_group_id`='" . (int) $group_info['group_id'] . "',
               `group_member_user_id`='" . (int) $_SESSION['UID'] . "',
               `group_member_since`='" . date("Y-m-d H:i:s") . "',
               `group_member_approved`='" . DB::quote($approved) . "'";
        DB::query($sql);

        if ($group_info['group_type'] == 'protected') {
            $msg = $lang['group_membership_requested'];
        } else {
            $msg = $lang['group_join_ok'];
        }
    }
}

if ($_GET['action'] == 'remove') {
    $sql = "DELETE FROM `group_members` WHERE
           `group_member_group_id`='" . (int) $group_info['group_id'] . "' AND
           `group_member_user_id`='" . (int) $_SESSION['UID'] . "'";
    DB::query($sql);
    $msg = $lang['group_membership_revoked'];
}

DB::close();

set_message($msg, 'success');
$redirect_url = FREETUBESITE_URL . '/group/' . $group_info['group_url'] . '/';
Http::redirect($redirect_url);
