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
require 'include/language/' . LANG . '/lang_resend_activation_mail.php';

if (! isset($_SESSION['INACTIVE_USER']))
{
    $redirect_url = FREETUBESITE_URL . '/login';
    Http::redirect($redirect_url);
    exit(0);
}

if (isset($_POST['submit']))
{

    if ($_POST['email'] == '')
    {
        $err = $lang['email_null'];
    }
    else if (! Validate::email($_POST['email']))
    {
        $err = $lang['email_invalid'];
    }

    if ($err == '')
    {
        $user_info = User::getByName($_SESSION['INACTIVE_USER']);

        if ($user_info)
        {

            if ($user_info['user_email'] != $_POST['email'])
            {
                if (! check_field_exists($_POST['email'], 'user_email', 'users'))
                {
                    $sql = "UPDATE `users` SET
                           `user_email`='" . DB::quote($_POST['email']) . "' WHERE
                           `user_name`='" . DB::quote($_SESSION['INACTIVE_USER']) . "'";
                    DB::query($sql);
                    $user_info['user_email'] = $_POST['email'];
                }
                else
                {
                    $err = $lang['email_exist'];
                }
            }

            if ($err == '')
            {
                $data1 = 'SIGNUP' . $user_info['user_id'];

                $sql = "SELECT * FROM `verify_code` WHERE
                       `data1`='" . DB::quote($data1) . "'";
                $verify_info = DB::fetch1($sql);

                if ($verify_info)
                {
                    $vkey = $verify_info['vkey'];
                    $verify_id = $verify_info['id'];
                }
                else
                {
                    $vkey = $_SERVER['REQUEST_TIME'] . rand(1, 99999999);
                    $vkey = md5($vkey);

                    $sql = "INSERT INTO `verify_code` SET
                           `vkey`='" . DB::quote($vkey) . "',
                           `data1`='" . DB::quote($data1) . "'";
                   $verify_id = DB::insertGetId($sql);
                }

                $verify_url = FREETUBESITE_URL . '/verify/user/' . $user_info['user_id'] . '/' . $verify_id . '/' . $vkey . '/';

                $sql = "SELECT * FROM `email_templates` WHERE
                       `email_id`='resend_activation'";
                $tmp = DB::fetch1($sql);

                $email_subject = $tmp['email_subject'];
                $email_body_tmp = $tmp['email_body'];

                $email_subject = str_replace('[SITE_NAME]', $config['site_name'], $email_subject);
                $email_subject = str_replace('[SITE_URL]', FREETUBESITE_URL, $email_subject);

                $email_body_tmp = str_replace('[SITE_NAME]', $config['site_name'], $email_body_tmp);
                $email_body_tmp = str_replace('[SITE_URL]', FREETUBESITE_URL, $email_body_tmp);
                $email_body_tmp = str_replace('[VERIFY_URL]', $verify_url, $email_body_tmp);
                $email_body_tmp = str_replace('[USERNAME]', $user_info['user_name'], $email_body_tmp);

                $mail_detailes = array();
                $mail_detailes['from_email'] = $config['admin_email'];
                $mail_detailes['from_name'] = $config['site_name'];
                $mail_detailes['to_email'] = $_POST['email'];
                $mail_detailes['to_name'] = $_SESSION['INACTIVE_USER'];
                $mail_detailes['subject'] = $email_subject;
                $mail_detailes['body'] = $email_body_tmp;
                $mail = new Mail();
                $mail->send($mail_detailes);
                $smarty->assign('activation_mail_sent', 'mail_sent');
            }
        }
        else
        {
            $err = $lang['not_found'];
        }
    }
}

$user_info = User::getByName($_SESSION['INACTIVE_USER']);

$smarty->assign('user_email', $user_info['user_email']);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('resend_activation_mail.tpl');
$smarty->display('footer.tpl');
DB::close();
