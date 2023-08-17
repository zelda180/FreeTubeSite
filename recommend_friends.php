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
require 'include/language/' . LANG . '/lang_recommend_friends.php';

$recommend_all = Config::get('recommend_all');

if ($recommend_all == 0) {
    User::is_logged_in();
}

if (isset($_POST['submit'])) {
    $emails = htmlspecialchars_uni($_POST['emails']);
    $user_fname = htmlspecialchars_uni($_POST['fname']);
    $from_email = htmlspecialchars_uni($_POST['guest_email']);
    $message = htmlspecialchars_uni($_POST['message']);
    $video_id = $_GET['video_id'];

    $smarty->assign('emails', $emails);
    $smarty->assign('fname', $user_fname);
    $smarty->assign('user_email', $from_email);
    $smarty->assign('message', $message);

    if ($emails == '') {
        $err = $lang['email_empty'];
    } else if ($user_fname == '') {
        $err = $lang['user_name_empty'];
    } else if ($from_email == '') {
        $err = $lang['user_email_empty'];
    } else if (! Validate::email($from_email)) {
        $err = $lang['email_invalid'];
    } else {
        if (isset($_SESSION['UID'])) {
            $sender_url = FREETUBESITE_URL . '/' . $_SESSION['USERNAME'];
        } else {
            $sender_url = FREETUBESITE_URL . '/';
        }

        $email_array = explode(',', $emails);
        $report = array();

        $video_info = Video::getById($video_id);
        $video_url = FREETUBESITE_URL . '/view/' . $video_id . '/' . $video_info['video_seo_name'] . '/';

        $sql = "SELECT * FROM `email_templates` WHERE
               `email_id`='recommend_video'";
        $email_info = DB::fetch1($sql);

        $email_subject = $email_info['email_subject'];
        $email_body = $email_info['email_body'];

        $email_subject = str_replace('[SENDER_NAME]', $user_fname, $email_subject);

        for ($i = 0; $i < count($email_array); $i ++) {
            $email_array[$i] = trim($email_array[$i]);
            if (! Validate::email($email_array[$i])) {
                $report[] = '<div class="alert alert-danger">Invalid email address ' . $email_array[$i] . '</div>';
            } else {
                $report[] = '<div class="alert alert-success">Mail Sent to ' . $email_array[$i] . '</div>';
                $to_email = $email_array[$i];
                $email_body_tmp = $email_body;

                $email_body_tmp = str_replace('[MESSAGE]', $message, $email_body_tmp);
                $email_body_tmp = str_replace('[VIDEO_URL]', $video_url, $email_body_tmp);
                $email_body_tmp = str_replace('[SITE_NAME]', $config['site_name'], $email_body_tmp);
                $email_body_tmp = str_replace('[SITE_URL]', FREETUBESITE_URL, $email_body_tmp);
                $email_body_tmp = str_replace('[SENDER_NAME]', $user_fname, $email_body_tmp);
                $email_body_tmp = str_replace('[SENDER_URL]', $sender_url, $email_body_tmp);

                $email = array();
                $email['from_email'] = $from_email;
                $email['from_name'] = $user_fname;
                $email['to_email'] = $to_email;
                $email['to_name'] = '';
                $email['subject'] = $email_subject;
                $email['body'] = $email_body_tmp;
                $mail = new Mail();
                $mail->send($email);
            }
        }
    }
}

if (isset($_SESSION["UID"])) {
    $user_info = User::get_user_by_id($_SESSION['UID']);

    if ($user_info != 0) {
        $smarty->assign('user_name', $user_info['user_first_name']);
        $smarty->assign('guest_email', $user_info['user_email']);
    }
}

$smarty->assign('err', $err);

if (isset($report)) {
    $smarty->assign('report', $report);
}

$smarty->display('header.tpl');
$smarty->display('recommend_friends.tpl');
$smarty->display('footer.tpl');
DB::close();
