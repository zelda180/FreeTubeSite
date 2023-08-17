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
require '../include/language/' . LANG . '/admin/subscription_extend.php';

Admin::auth();

if (isset($_POST['submit'])) {

    $duration = $_POST['duration'];
    $duration_type = $_POST['duration_type'];

    if ($duration == '') {
        $err = $lang['duration_null'];
    } else if (! is_numeric($duration)) {
        $err = $lang['duration_numeric'];
    } else if ($duration_type == '') {
        $err = $lang['duration_type_null'];
    } else {

        $period = "$duration $duration_type";

        /* Specific Users  */

        if ($_POST['extend_for'] == 'specific_user') {

            $user_name = trim($_POST['username']);

            if ($user_name == '') {
                $err = $lang['user_name_null'];
            } else {
                $extend = extend_subscription($user_name, $period);

                if ($extend == 1) {
                    $msg = str_replace('[USERNAME]', $user_name, $lang['specific_user_ok']);
                } else if ($extend == 2) {
                    $err = $lang['user_name_not_found'];
                }
            }

        } else if ($_POST['extend_for'] == 'expired_users') {

            $sql = "SELECT * FROM
                   `subscriber` AS s,
                   `users` AS u WHERE
                    s.UID=u.user_id";
            $subscribers_all = DB::fetch($sql);

            $user_list = '';

            foreach ($subscribers_all as $subscriber) {
                $pack_id = $subscriber['pack_id'];
                $my_expired_time = $subscriber['expired_time'];
                $user_name = $subscriber['user_name'];
                if (strtotime($my_expired_time) < strtotime(date('Y-m-d H:i:s'))) {
                    $extend = extend_subscription($user_name, $period);
                    $user_list .= $user_name . '<br />';
                }
            }

            $msg = str_replace('[USER_LIST]', $user_list, $lang['expired_users_ok']);

        } else if ($_POST['extend_for'] == 'all_users') {

            $sql = "SELECT * FROM `users`";
            $users_all = DB::fetch($sql);

            foreach ($users_all as $user_info) {
                $user_name = $user_info['user_name'];
                $extend = extend_subscription($user_name, $period);
            }

            if ($extend == 1) {
                $msg = $lang['all_users_ok'];
            }
        }
    }
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/subscription_extend.tpl');
$smarty->display('admin/footer.tpl');


function extend_subscription($user_name, $period)
{
    $user_info = User::getByName($user_name);

    if ($user_info) {

        $user_id = $user_info['user_id'];

        $sql = "SELECT * FROM `subscriber` WHERE
               `UID`=$user_id";
        $user_subscription = DB::fetch1($sql);

        if ($user_subscription)
        {
            $my_pack_id = $user_subscription['pack_id'];
            $my_expired_time = $user_subscription['expired_time'];

            if ($my_pack_id != 0) {

                # check if expired
                if (strtotime($my_expired_time) < strtotime(date('Y-m-d H:i:s'))) {
                    $new_expired_time = date('Y-m-d H:i:s', strtotime("+$period"));
                } else {
                    $new_expired_time = strtotime($my_expired_time);
                    $i = strtotime("+$period", $new_expired_time);
                    $new_expired_time = date('Y-m-d H:i:s', $i);
                }

                $sql = "UPDATE `subscriber` SET `expired_time`='$new_expired_time' WHERE
                       `UID`=$user_id";
                DB::query($sql);

                $return = 1; //Subscription Extended For User: $user_name<br />Expiry Date: $new_expired_time
            } else {
                $sql = "SELECT * FROM `packages` WHERE
                       `package_trial`='yes'";
                $package_info = DB::fetch1($sql);

                $pack_id = $package_info['pack_id'];

                $subscribe_time = date('Y-m-d H:i:s');
                $new_expired_time = date('Y-m-d H:i:s', strtotime("+$period"));

                $sql = "UPDATE `subscriber` SET
                       `pack_id`='$pack_id',
                       `subscribe_time`='$subscribe_time',
                       `expired_time`='$new_expired_time' WHERE
                       `UID`=$user_id";
                DB::query($sql);
                $return = 1; // Subscription Extended For User: $user_name
            }
        } else {

            $sql = "SELECT * FROM `packages` WHERE
                   `package_trial`='yes'";
            $package_info = DB::fetch1($sql);

            $pack_id = $package_info['package_id'];

            $subscribe_time = date('Y-m-d H:i:s');
            $new_expired_time = date('Y-m-d H:i:s', strtotime("+$period"));

            $sql = "INSERT INTO `subscriber` SET
                   `UID`=$user_id,
                   `pack_id`=$pack_id,
                   `subscribe_time`='$subscribe_time',
                   `expired_time`='$new_expired_time'";
            DB::query($sql);
            $return = 1; // Subscription Extended
        }
    } else {
        $return = 2; // user not found
    }

    return $return;
}

DB::close();
