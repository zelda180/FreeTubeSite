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

require 'include/config.php';
require 'include/language/' . LANG . '/lang_login.php';

$inactive_user = 0;
$user_name = '';

if (isset($_POST['action_login']))
{
    $user_name = htmlspecialchars_uni($_POST['user_name']);
    $password = $_POST['user_password'];

    if ($user_name == '') {
        $err = $lang['user_name_empty'];
    } else if ($password == '') {
        $err = $lang['password_empty'];
    }

    if ($err == '') {
        if (get_magic_quotes_gpc()) {
            $user_name = stripslashes($user_name);
            $password = stripslashes($password);
            $password = md5($salt.$password);
        }

        $user_info = User::getByName($user_name);

        if (! User::validate($user_info['user_name'], $password)) {
            $err = $lang['invalid_login'];
        }
        else
        {
            if ($user_info['user_account_status'] == 'Inactive') {
                if ($config['enable_package'] == 'yes') {
                    $sql = "SELECT * FROM
                           `subscriber` AS `subs`,
                           `packages` AS `p` WHERE
                            subs.UID=" . (int) $user_info['user_id'] . " AND
                            subs.pack_id=p.package_id";
                    $package_info = DB::fetch1($sql);

                    if ($package_info['package_trial'] != 'yes') {
                        $redirect_url = $config['baseurl'] . '/renew_account.php?uid=' . $user_info['user_id'];
                        Http::redirect($redirect_url);
                    }
                }

                if ($config['signup_verify'] == '2') {
                    $err = $lang['inactive_user_admin'];
                    $inactive_user = 0;
                } else {
                    $err = $lang['inactive_user'];
                    $inactive_user = 1;
                }

                $_SESSION['INACTIVE_USER'] = $user_info['user_name'];
            } else if ($user_info['user_account_status'] == 'Suspended') {
                $err = $lang['user_suspended'];
            }
        }

        if ($err == '')
        {
            if ($config['enable_package'] == 'yes') {
                $sql = "SELECT * FROM `subscriber` WHERE
                       `UID`=" . (int) $user_info['user_id'];
                $subscription = DB::fetch1($sql);

                if (! $subscription) {
                    $sql = "INSERT INTO `subscriber` SET
                           `UID`=" . (int) $user_info['user_id'];
                    DB::query($sql);
                    $sql = "SELECT * FROM `subscriber` WHERE
                           `UID`=" . (int) $user_info['user_id'];
                    $subscription = DB::fetch1($sql);
                }

                if ($subscription['pack_id'] == 0) {
                    $sql = "SELECT * FROM `packages` WHERE
                           `package_trial`='yes'";
                    $package_row = DB::fetch1($sql);
                    $expired_time = date("Y-m-d H:i:s", strtotime("+" . $package_row['package_trial_period'] . " day"));
                    $sql = "UPDATE `subscriber` SET
                           `pack_id`='" . (int) $package_row['package_id'] . "',
                           `subscribe_time`='" . date("Y-m-d H:i:s") . "',
                           `expired_time`='$expired_time' WHERE
                           `UID`=" . (int) $user_info['user_id'];
                    DB::query($sql);
                } else {
                    check_subscriber_duration($user_info['user_id']);
                }
            }

            User::login($user_name);

            if (isset($_POST['autologin'])) {
                User::set_auto_login_cookie($user_name);
            }

            if (isset($_SESSION['REDIRECT']) && $_SESSION['REDIRECT'] != '') {
                $redirect_url = $_SESSION['REDIRECT'];
                $_SESSION['REDIRECT'] = '';
            } else {
                $redirect_url = $config['baseurl'] . '/' . $user_info['user_name'];
            }

            Http::redirect($redirect_url);
        }
    }
}

$smarty->assign(array(
    'html_title' => 'Login',
    'html_description' => 'Login'
));
$smarty->assign('inactive_user', $inactive_user);
$smarty->assign('user_name', $user_name);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('login.tpl');
$smarty->display('footer.tpl');
DB::close();
