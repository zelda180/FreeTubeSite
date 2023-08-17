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

Admin::auth();

if (isset($_POST['user_ids'])) {
    foreach ($_POST['user_ids'] as $user_id) {
        activate_user($user_id);
    }
    set_message('Selected users have been activated.', 'success');
}

function activate_user($user_id) {
    global $config;

    $user_info = User::getById($user_id);

    if ($user_info) {
        $sql = "UPDATE `users` SET
               `user_account_status`='Active',
               `user_email_verified`='yes' WHERE
               `user_id`='$user_id'";
        DB::query($sql);

        if (DB::affectedRows()) {
            $sql = "SELECT * FROM `email_templates` WHERE
                   `email_id`='user_signup_verify_admin_active'";
            $email_template = DB::fetch1($sql);
            $email_subject = $email_template['email_subject'];
            $email_body_tmp = $email_template['email_body'];

            $email_subject = str_replace('[SITE_NAME]', $config['site_name'], $email_subject);

            $email_body_tmp = str_replace('[SITE_NAME]', $config['site_name'], $email_body_tmp);
            $email_body_tmp = str_replace('[SITE_URL]', FREETUBESITE_URL, $email_body_tmp);
            $email_body_tmp = str_replace('[USERNAME]', $user_info['user_name'], $email_body_tmp);

            $headers = "From: $config[site_name] <$config[admin_email]> \n";
            $headers .= "Content-Type: text/html\n";

            $email = array();
            $email['from_email'] = $config['admin_email'];
            $email['from_name'] = $config['site_name'];
            $email['to_email'] = $user_info['user_email'];
            $email['to_name'] = $user_info['user_name'];
            $email['subject'] = $email_subject;
            $email['body'] = $email_body_tmp;
            $mail = new Mail();
            $mail->send($email);
        }
    }
}

DB::close();

if (isset($_SERVER['HTTP_REFERER'])) {
    $redirect_url = $_SERVER['HTTP_REFERER'];
} else {
    $redirect_url = FREETUBESITE_URL . '/admin/users.php?a=Inactive';
}

Http::redirect($redirect_url);
