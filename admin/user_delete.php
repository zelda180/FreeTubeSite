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
require '../include/language/' . LANG . '/admin/user_delete.php';

Admin::auth();

if (! is_numeric($_GET['uid'])) {
    echo $lang['userid_numeric'];
    exit(0);
}

$user_info = User::get_user_by_id($_GET['uid']);

if (! $user_info) {
    set_message('user not found', 'error');
    $redirect_url = FREETUBESITE_URL . '/admin/users.php';
    Http::redirect($redirect_url);
}

User::delete($user_info['user_id'], 1);

$msg = str_replace('[USERNAME]', $user_info['user_name'], $lang['user_deleted']);
set_message($msg, 'success');

DB::close();

$_GET['a'] = isset($_GET['a']) ? $_GET['a'] : 'All';

if ($_GET['a'] == '') {
    $_GET['a'] = 'All';
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

$redirect_url = FREETUBESITE_URL . '/admin/users.php?a=' . $_GET['a'] . '&sort=' . $sort . '&page=' . $page;
Http::redirect($redirect_url);
