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
require '../include/language/' . LANG . '/admin/user_add.php';

Admin::auth();

if (isset($_POST['submit'])) {
    if (strlen($_POST['user_name']) < 4) {
        $err = $lang['user_name_short'];
    } else if (User::isReserved($_POST['user_name'])) {
        $err = $lang['user_name_reserved'];
    } else if (check_field_exists($_POST['user_name'], 'user_name', 'users') == 1) {
        $err = $lang['user_name_exist'];
    } else if (empty($_POST['user_email']) || ! Validate::email($_POST['user_email'])) {
        $err = $lang['email_invalid'];
    } else if (check_field_exists($_POST['user_email'], "user_email", "users") == 1) {
        $err = $lang['email_exist'];
    } else if (strlen($_POST['user_password']) < 4) {
        $err = $lang['password_short'];
    }

    if ($err == '') {

        $request_password = $_POST['user_password'];
        $request_password = md5($request_password);

        $user_data = array(
            'user_email' => $_POST['user_email'],
            'user_name' => $_POST['user_name'],
            'user_password' => $request_password,
            'user_email_verified' => 'yes',
        );

        $user_id = User::create($user_data);

        if ($config['enable_package'] == 'yes') {

            $package_duration = (int) $_POST['user_package_duration'];
            $package_duration_type = $_POST['user_package_duration_type'];

            $sql = "SELECT * FROM `packages` WHERE
                   `package_id`='" . (int) $_POST['user_package_id'] . "'";
            $tmp = DB::fetch1($sql);

            $expired_time = date("Y-m-d H:i:s", strtotime("+ $package_duration $package_duration_type"));

            $sql = "INSERT INTO `subscriber` SET
                   `pack_id`='" . (int) $_POST['user_package_id'] . "',
                   `subscribe_time`='" . date("Y-m-d H:i:s") . "',
                   `expired_time`='$expired_time',
                   `UID`='" . (int) $user_id . "'";
            DB::query($sql);
        }

        $auto_friend = Config::get('signup_auto_friend');

        if ((strlen($auto_friend) > 1) && (check_field_exists($auto_friend, 'user_name', 'users'))) {
            Friend::makeFriends($auto_friend, $_POST['user_name']);
        }

        set_message($lang['user_add_success'], 'success');
        $redirect_url = FREETUBESITE_URL . '/admin/user_view.php?user_id=' . $user_id;
        Http::redirect($redirect_url);
    }
}

if ($config['enable_package'] == 'yes') {
    $sql = "SELECT * FROM `packages` WHERE
           `package_status`='Active'
            ORDER BY `package_price` DESC";
    $packages = DB::fetch($sql);
    $smarty->assign('package', $packages);
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/user_add.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
