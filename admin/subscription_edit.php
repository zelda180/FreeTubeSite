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
require '../include/language/' . LANG . '/admin/subscription_edit.php';

Admin::auth();

if (isset($_POST['todo'])) {
    $todo = $_POST['todo'];
} else {
    $todo = 'get_username';
}

if (isset($_POST['edit'])) {
    $user_name = $_POST['username'];

    if ($user_name == '') {
        $err = $lang['user_name_null'];
    } else {
        $user_info = User::getByName($user_name);
        if ($user_info) {
            $user_id = $user_info['user_id'];
        } else {
            $err = $lang['user_not_found'];
        }
    }

    if ($err == '') {
        $todo = "show_edit_form";
        $sql = "SELECT `package_name` FROM `packages`";
        $packages = DB::fetch($sql);

        $sql = "SELECT * FROM `subscriber` WHERE
               `UID`=$user_id";
        $subscriber_info = DB::fetch1($sql);

        $expired_time = $subscriber_info['expired_time'];
        $pack_id = $subscriber_info['pack_id'];
        $used_space = $subscriber_info['used_space'];
        $total_video = $subscriber_info['total_video'];

        $sql = "SELECT `package_name` FROM `packages` WHERE
               `package_id`='" . (int) $pack_id . "'";
        $tmp = DB::fetch1($sql);
        $pack_name = $tmp['package_name'];

        $year = date("Y", strtotime($expired_time));
        $month = date("m", strtotime($expired_time));
        $date = date("d", strtotime($expired_time));

        $year_expire = date('Y');

        for ($y = $year_expire; $y <= $year_expire + 10; $y ++) {
            $expire_year[] = $y;
        }

        for ($m = 1; $m <= 12; $m ++) {
            $expire_month[] = $m;
        }

        for ($d = 1; $d <= 30; $d ++) {
            $expire_date[] = $d;
        }

        $smarty->assign('uid', $user_id);
        $smarty->assign('expire_year', $expire_year);
        $smarty->assign('year', $year);
        $smarty->assign('expire_month', $expire_month);
        $smarty->assign('month', $month);
        $smarty->assign('expire_date', $expire_date);
        $smarty->assign('date', $date);
        $smarty->assign('expired_time', $expired_time);
        $smarty->assign('packages', $packages);
        $smarty->assign('pack_name', $pack_name);
        $smarty->assign('username', $user_name);
        $smarty->assign('used_space', $used_space);
        $smarty->assign('total_video', $total_video);
    }

} else if (isset($_POST['save_subscription'])) {

    $new_expired_time = $_POST['expire_year'] . '-' . $_POST['expire_month'] . '-' . $_POST['expire_date'] . ' 00:00:00';
    $sql_pack_name = DB::quote($_POST['package']);
    $sql = "SELECT `package_id` FROM `packages` WHERE
           `package_name`='$sql_pack_name'";
    $myobj = DB::fetch1($sql);
    $pack_id = $myobj['package_id'];

    $sql = "UPDATE `subscriber` SET `pack_id`=$pack_id,
           `expired_time`='$new_expired_time',
           `used_space`='" . (float) $_POST['used_space'] . "',
           `total_video`='" . (int) $_POST['total_video'] . "' WHERE
           `UID`='" . (int) $_POST['uid'] . "'";
    DB::query($sql);
    $todo = 'saved';
    $smarty->assign('new_expired_time', $new_expired_time);
    $smarty->assign('username', $_POST['username']);
    $smarty->assign('package', $_POST['package']);
}

$smarty->assign('todo', $todo);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/subscription_edit.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
