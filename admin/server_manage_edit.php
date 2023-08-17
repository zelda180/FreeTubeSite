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
require '../include/language/' . LANG . '/admin/server_manage_edit.php';

Admin::auth();

if (isset($_POST['submit'])) {
    $server_id = (int) $_POST['server_id'];
    if ($_POST['server_ip'] == '') {
        $err = $lang['ip_numeric'];
    } else if ($_POST['server_url'] == '') {
        $err = $lang['server_url_empty'];
    } else if ($_POST['user_name'] == '') {
        $err = $lang['user_name_empty'];
    } else if ($_POST['password'] == '') {
        $err = $lang['password_empty'];
    } else if ($_POST['server_type'] == 2 || $_POST['server_type'] == 3) {
        $server_secdownload_secret = isset($_POST['server_secdownload_secret']) ? $_POST['server_secdownload_secret'] : '';
        if (strlen($server_secdownload_secret) < 10) {
            $err = 'You must enter secdownload.secret';
        }
    }

    if ($err == '') {
        $sql = "UPDATE `servers` SET
               `ip`='" . DB::quote($_POST['server_ip']) . "',
               `url`='" . DB::quote($_POST['server_url']) . "',
               `user_name`='" . DB::quote($_POST['user_name']) . "',
               `password`='" . DB::quote($_POST['password']) . "',
               `folder`='" . DB::quote($_POST['folder']) . "',
               `server_type`='" . (int) $_POST['server_type'] . "',
               `server_secdownload_secret`='" . DB::quote($server_secdownload_secret) . "' WHERE
               `id`='" . (int) $server_id . "'";
        DB::query($sql);
        set_message($lang['server_info_updated'], 'success');
        $redirect_url = FREETUBESITE_URL . '/admin/server_manage.php';

    } else {
        set_message($err, 'error');
        $redirect_url = FREETUBESITE_URL . '/admin/server_manage_edit.php?id=' . $server_id;
    }

    DB::close();
    Http::redirect($redirect_url);
}

$server_id = isset($_GET['id']) ? $_GET['id'] : 0;

if (! is_numeric($server_id)) {
    $err = $lang['id_numeric'];
}

$sql = "SELECT * FROM `servers` WHERE
       `id`='" . (int) $server_id . "'";
$server_info = DB::fetch1($sql);

$smarty->assign('server_info', $server_info);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/server_manage_edit.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
