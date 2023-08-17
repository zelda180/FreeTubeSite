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
require '../include/language/' . LANG . '/admin/server_add.php';

Admin::auth();

if (isset($_POST['submit'])) {
    $server_url = $_POST['server_url'];
    $server_ip = $_POST['server_ip'];

    $server_user_name = $_POST['server_username'];
    $server_password = $_POST['server_password'];
    $server_folder = $_POST['server_folder'];
    $server_type = $_POST['server_type'];
    $server_secdownload_secret = isset($_POST['server_secdownload_secret']) ? $_POST['server_secdownload_secret'] : '';

    if ($server_url == '') {
        $err = $lang['server_url_null'];
    } else if (check_field_exists($server_url, 'url', 'servers') == 1) {
        $err = $lang['server_url_exist'];
    } else if (! preg_match("/^http:\/\//i", $server_url)) {
        $err = $lang['server_url_invalid'];
    } else if ($server_ip == '') {
        $err = $lang['server_ip_null'];
    } else if ($server_user_name == '') {
        $err = $lang['server_user_name_null'];
    } else if ($server_password == '') {
        $err = $lang['password_empty'];
    } else if (! is_numeric($server_type)) {
        $err = $lang['server_type_invalid'];
    } else if ($server_type == 2 || $server_type == 3) {
        if (strlen($server_secdownload_secret) < 4) {
            $err = $lang['server_secdownload_secret_empty'];
        }
    }

    if ($err == '') {
        $sql = "INSERT INTO `servers` SET
               `ip`='" . DB::quote($server_ip) . "',
               `url`='" . DB::quote($server_url) . "',
               `user_name`='" . DB::quote($server_user_name) . "',
               `password`='" . DB::quote($server_password) . "',
               `folder` = '" . DB::quote($server_folder) . "',
               `status`='1',
               `server_type`='" . (int) $server_type . "',
               `server_secdownload_secret`='" . (int) $server_secdownload_secret . "'";
        DB::query($sql);
        DB::close();
        set_message($lang['server_added'], 'success');
        $redirect_url = FREETUBESITE_URL . '/admin/server_manage.php';
        Http::redirect($redirect_url);
    }
}

$smarty->assign('msg', $msg);
$smarty->assign('err', $err);
$smarty->display('admin/header.tpl');
$smarty->display('admin/server_add.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
