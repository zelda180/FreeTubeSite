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

if (isset($_POST['submit'])) {

    $data1 = 'ADMIN_PWD_CHANGE';

    $sql = "SELECT * FROM `verify_code` WHERE
           `data1`='$data1'";
    $verify_info = DB::fetch1($sql);

    if ($verify_info) {
        $verify_vkey = $verify_info['vkey'];
        $verify_id = $verify_info['id'];
        $new_password = $verify_info['data2'];
    } else {
        $new_password = password_generator(6);
        $verify_vkey = substr(time() . rand(1, 99999999), 6);
        $verify_vkey = md5($verify_vkey);
        $sql = "INSERT INTO `verify_code` SET
           `vkey`='$verify_vkey',
           `data1`='$data1',
           `data2`='" . $new_password . "'";
        $verify_id = DB::insertGetId($sql);
    }

    $verify_url = FREETUBESITE_URL . '/admin/reset_password.php?k=' . $verify_vkey . '&i=' . $verify_id;

    $sql = "SELECT * FROM `email_templates` WHERE
           `email_id`='password_reset_admin'";
    $email_info = DB::fetch1($sql);
    $email_subject = $email_info['email_subject'];
    $email_body = $email_info['email_body'];

    $email_subject = str_replace('[SITE_NAME]', $config['site_name'], $email_subject);
    $email_body = str_replace('[ADMIN_NAME]', $config['admin_name'], $email_body);
    $email_body = str_replace('[PASSWORD]', $new_password, $email_body);
    $email_body = str_replace('[REMOTE_ADDR]', $_SERVER['REMOTE_ADDR'], $email_body);
    $email_body = str_replace('[VERIFY_URL]', $verify_url, $email_body);
    $email_body = str_replace('[SITE_URL]', FREETUBESITE_URL, $email_body);
    $email_body = str_replace('[SITE_NAME]', $config['site_name'], $email_body);

    $mail_info = array();
    $mail_info['from_email'] = $config['admin_email'];
    $mail_info['from_name'] = $config['site_name'];
    $mail_info['to_email'] = $config['admin_email'];
    $mail_info['to_name'] = $config['admin_name'];
    $mail_info['subject'] = $email_subject;
    $mail_info['body'] = $email_body;
    $mail = new Mail();
    $mail->send($mail_info);
}

$smarty->display('admin/lost_password.tpl');
DB::close();
