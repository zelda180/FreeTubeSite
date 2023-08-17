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
require '../include/language/' . LANG . '/admin/group_search.php';

Admin::auth();

$found = 0;

if (! empty($_POST['group_name'])) {
    $sql = "SELECT * FROM `groups` WHERE
           `group_name`='" . DB::quote($_POST['group_name']) . "'";
    $group_info = DB::fetch1($sql);
    if (! $group_info) {
        $err = str_replace('[GROUP_NAME]', $_POST['group_name'], $lang['group_not_found_name']);
    } else {
        $found = 1;
        $group_id = $group_info['group_id'];
    }
}

if ($found) {
    $redirect_url = FREETUBESITE_URL . '/admin/group_view.php?group_id=' . $group_id;
    Http::redirect($redirect_url);
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/group_search.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
