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
require '../include/config.php';
require '../include/language/' . LANG . '/lang_video_inappropriate.php';
$video_id = isset($_POST['vid']) ? $_POST['vid'] : '';
$abuse_type = isset($_POST['abuse_type']) ? $_POST['abuse_type'] : '';
$comment = isset($_POST['comment']) ? $_POST['comment'] : '';
$comment = trim(strip_tags($comment));

$abuse_type_all = array(
    'porn',
    'racism',
    'prohibited',
    'violent',
    'copyright'
);

if (! in_array($abuse_type, $abuse_type_all))
{
    $err = 'Invalid abuse type';
}

if (! is_numeric($video_id))
{
    $err = $lang['vid_invalid'];
}
else if (strlen($comment) < 10)
{
    $err = $lang['comment_short'];
}

if ($err != '')
{
    Ajax::returnJson($err, 'error');
    exit();
}

$sql = "SELECT count(*) AS `total` FROM `inappropriate_requests` WHERE
       `inappropriate_request_video_id`='" . (int) $video_id . "'";
$already_reported = DB::getTotal($sql);

if ($already_reported) {
    $sql = "UPDATE `inappropriate_requests` SET
           `inappropriate_request_count`=`inappropriate_request_count`+1,
           `inappropriate_request_date`='" . date("Y-m-d") . "' WHERE
           `inappropriate_request_video_id`=" . (int) $video_id;
} else {
    $sql = "INSERT `inappropriate_requests` SET
           `inappropriate_request_video_id`='" . (int) $video_id . "',
           `inappropriate_request_count`=1,
           `inappropriate_request_date`='" . date("Y-m-d") . "'";
}

DB::query($sql);

$mail_abuse_report = Config::get('mail_abuse_report');

if ($mail_abuse_report) {

    $sql = "SELECT `video_id`,`video_title`,`video_seo_name` FROM `videos` WHERE
           `video_id`='" . (int) $video_id . "'";
    $video_info = DB::fetch1($sql);

    $sql = "SELECT * FROM `email_templates` WHERE
           `email_id`='abuse_report'";
    $tmp = DB::fetch1($sql);

    $email_body = $tmp['email_body'];
    $email_subject = $tmp['email_subject'];

    $user_name = isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : 'Guest';
    $video_url = FREETUBESITE_URL . '/view/' . $video_id . '/' . $video_info['video_seo_name'] . '/';

    $email_subject = str_replace('[VIDEO_TITLE]', $video_info['video_title'], $email_subject);

    $email_body = str_replace('[SITE_NAME]', $config['site_name'], $email_body);
    $email_body = str_replace('[SITE_URL]', FREETUBESITE_URL, $email_body);
    $email_body = str_replace('[USER_NAME]', $user_name, $email_body);
    $email_body = str_replace('[VID]', $video_id, $email_body);
    $email_body = str_replace('[VIDEO_TITLE]', $video_info['video_title'], $email_body);
    $email_body = str_replace('[TITLE]', $video_info['video_seo_name'], $email_body);
    $email_body = str_replace('[VIDEO_URL]', $video_url, $email_body);
    $email_body = str_replace('[TYPE_ABUSE]', $abuse_type, $email_body);
    $email_body = str_replace('[ABUSE_COMMENTS]', nl2br(stripcslashes($comment)), $email_body);
    $email_body = str_replace('[REMOTE_ADDR]', $_SERVER['REMOTE_ADDR'], $email_body);

    $msg = array();
    $msg['from_email'] = $config['admin_email'];
    $msg['from_name'] = $config['site_name'];
    $msg['to_email'] = $config['admin_email'];
    $msg['to_name'] = $config['admin_name'];
    $msg['subject'] = $email_subject;
    $msg['body'] = $email_body;
    $mail = new Mail();
    $mail->send($msg);
}

Ajax::returnJson($lang['request_sent'], 'success');
DB::close();
