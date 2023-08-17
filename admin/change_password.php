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
require '../include/language/' . LANG . '/admin/change_password.php';

Admin::auth();

if (isset($_POST['submit'])) {
    if ($_POST['admin_name'] == '') {
        $err = $lang['admin_name_null'];
    } else if (md5($_POST['password']) != $_SESSION['APASSWORD']) {
        $err = $lang['password_wrong'];
    } else if (strlen($_POST['password_new']) < 4) {
        $err = $lang['password_short'];
    } else if ($_POST['password_new'] != $_POST['password_confirm']) {
        $err = $lang['password_confirm_error'];
    }

    if ($err == '') {
        if ($config['admin_name'] != $_POST['admin_name']) {
            $sql = "UPDATE `sconfig` SET
                   `svalue`='" . DB::quote($_POST['admin_name']) . "' WHERE
                   `soption`='admin_name'";
            DB::query($sql);
            $_SESSION['AUID'] = $_POST['admin_name'];
            $smarty->assign('admin_name', $_POST['admin_name']);
        }

        $password_new_md5 = md5($_POST['password_new']);
        $sql = "UPDATE `sconfig` SET
               `svalue`='$password_new_md5' WHERE
               `soption`='admin_pass'";
        DB::query($sql);
        $_SESSION['APASSWORD'] = $password_new_md5;
        $msg = $lang['password_changed'];
    }
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/change_password.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
