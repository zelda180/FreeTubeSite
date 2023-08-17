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
require '../include/language/' . LANG . '/admin/reserve_user_name.php';

Admin::auth();

if (isset($_GET['action']) && $_GET['action'] == 'del' && is_numeric($_GET['id'])) {
    $sql = "DELETE FROM `disallow` WHERE
           `disallow_id`='" . (int) $_GET['id'] . "'";
    DB::query($sql);
}

if (isset($_POST['action']) && $_POST['action'] == 'add') {
    if ($_POST['name'] == '') {
        $err = $lang['user_name_null'];
        $smarty->assign('err', $err);
    } else {
        $user_name = mb_strtolower($_POST['name']);
        $user_name = trim($user_name);
        $sql = "INSERT INTO `disallow` SET
               `disallow_username`='" . DB::quote($user_name) . "'";
        DB::query($sql);
        $msg = str_replace('[USERNAME]', $user_name, $lang['user_name_reserved']);
        $smarty->assign('msg', $msg);
    }
}

$sql = "SELECT * FROM `disallow`";
$disallow = DB::fetch($sql);

$smarty->assign('disallow', $disallow);
$smarty->display('admin/header.tpl');
$smarty->display('admin/reserve_user_name.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
