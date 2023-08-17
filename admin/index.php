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
require '../include/language/' . LANG . '/admin/index.php';

if (isset($_SESSION['AUID']) && isset($_SESSION['APASSWORD'])) {
    if (($_SESSION['AUID'] == $config['admin_name']) && ($_SESSION['APASSWORD'] == $config['admin_pass'])) {
        $redirect_url = FREETUBESITE_URL . '/admin/home.php';
        Http::redirect($redirect_url);
    }
}

if (isset($_POST['submit'])) {
    $user_password = $_POST['password'];
    $user_name = $_POST['user_name'];

    if (get_magic_quotes_gpc()) {
        $user_password = stripslashes($user_password);
        $user_name = stripslashes($user_name);
    }

    $user_password_md5 = md5($user_password);

    if ($user_name == '' || $user_password == '') {
        $err = $lang['login_empty'];
    } else if (($user_name == $config['admin_name']) && ($user_password_md5 == $config['admin_pass'])) {
        $_SESSION['AUID'] = $config['admin_name'];
        $_SESSION['APASSWORD'] = $config['admin_pass'];
        $redirect_url = FREETUBESITE_URL . '/admin/home.php';
        Http::redirect($redirect_url);
    } else {
        $err = $lang['login_invalid'];
    }
}

$login_error = '';

if ($err != '') {
    $login_error = $err;
    $err = '';
} else if ($msg != '') {
    $login_error = $msg;
    $msg = '';
}

$smarty->assign('login_error', $login_error);
$smarty->display('admin/index.tpl');
DB::close();
