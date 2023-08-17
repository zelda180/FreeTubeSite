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
require '../include/language/' . LANG . '/admin/settings_signup.php';
Admin::auth();

if (Config::get('recaptcha_sitekey') == '' || Config::get('recaptcha_secretkey') == '') {
    $smarty->assign('show_recaptcha_warning', '1');
}

if (isset($_POST['submit']))
{

    if (is_numeric($_POST['signup_dob']))
    {
        $sql = "UPDATE `config` SET
               `config_value`='" . (int) $_POST['signup_dob'] . "' WHERE
               `config_name`='signup_dob'";
        DB::query($sql);
    }

    if (is_numeric($_POST['signup_age_min']))
    {
        $sql = "UPDATE `config` SET
               `config_value`='" . (int) $_POST['signup_age_min'] . "' WHERE
               `config_name`='signup_age_min'";
        DB::query($sql);
    }

    if (is_numeric($_POST['signup_age_min_enforce']))
    {
        $sql = "UPDATE `config` SET
               `config_value`='" . (int) $_POST['signup_age_min_enforce'] . "' WHERE
               `config_name`='signup_age_min_enforce'";
        DB::query($sql);

        if ($_POST['signup_age_min_enforce'] == 1)
        {
        	if ($_POST['signup_dob'] == 0)
        	{
		        $sql = "UPDATE `config` SET
		               `config_value`='1' WHERE
		               `config_name`='signup_dob'";
		        DB::query($sql);
        	}
        }
    }

    if (is_numeric($_POST['signup']))
    {
        $sql = "UPDATE `config` SET
               `config_value`='" . (int) $_POST['signup'] . "' WHERE
               `config_name`='signup_enable'";
        DB::query($sql);
    }

    if (is_numeric($_POST['signup']))
    {
        $sql = "UPDATE `sconfig` SET
               `svalue`='" . (int) $_POST['signup_verify'] . "' WHERE
               `soption`='signup_verify'";
        DB::query($sql);
        $smarty->assign('signup_verify', $_POST['signup_verify']);
    }

    if (is_numeric($_POST['notify_signup']))
    {
        $sql = "UPDATE `sconfig` SET
               `svalue`='" . (int) $_POST['notify_signup'] . "' WHERE
               `soption`='notify_signup'";
        DB::query($sql);
        $smarty->assign('notify_signup', $_POST['notify_signup']);
    }

    $sql = "UPDATE `config` SET
           `config_value`='" . DB::quote($_POST['recaptcha_sitekey']) . "' WHERE
           `config_name`='recaptcha_sitekey'";
    DB::query($sql);

    $sql = "UPDATE `config` SET
           `config_value`='" . DB::quote($_POST['recaptcha_secretkey']) . "' WHERE
           `config_name`='recaptcha_secretkey'";
    DB::query($sql);

    $signup_auto_friend = trim($_POST['signup_auto_friend']);

    if ($signup_auto_friend != '')
    {
        if (check_field_exists($signup_auto_friend, 'user_name', 'users'))
        {
            $sql = "UPDATE `config` SET
                   `config_value`='" . DB::quote($signup_auto_friend) . "' WHERE
                   `config_name`='signup_auto_friend'";
            DB::query($sql);
        }
        else
        {
            $err = str_replace('[USERNAME]', $signup_auto_friend, $lang['user_not_found']);
        }
    }

    $sql = "UPDATE `config` SET
           `config_value`='" . (int) $_POST['spam_filter'] . "' WHERE
           `config_name`='spam_filter'";
    DB::query($sql);

    if ($err == '')
    {
        $msg = $lang['settings_updated'];
    }
}

$smarty->assign('signup_age_min', Config::get('signup_age_min'));
$smarty->assign('signup_age_min_enforce', Config::get('signup_age_min_enforce'));
$smarty->assign('signup_enable', Config::get('signup_enable'));
$smarty->assign('signup_auto_friend', Config::get('signup_auto_friend'));
$smarty->assign('signup_dob', Config::get('signup_dob'));
$smarty->assign('recaptcha_sitekey', Config::get('recaptcha_sitekey'));
$smarty->assign('recaptcha_secretkey', Config::get('recaptcha_secretkey'));
$smarty->assign('spam_filter', Config::get('spam_filter'));
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/settings_signup.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
