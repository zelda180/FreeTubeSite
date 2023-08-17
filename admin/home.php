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
$smarty->assign('freetubesite_version', $freetubesite_version);
# number of videos
$sql = "SELECT count(*) AS `total` FROM `videos` WHERE
       `video_active`='1'";
$total_video = DB::getTotal($sql);
$smarty->assign('total_video', $total_video);
# number of public videos
$sql = "SELECT count(*) AS `total` FROM `videos` WHERE
       `video_active`='1' AND
       `video_type`='public'";
$total_public_video = DB::getTotal($sql);
$smarty->assign('total_public_video', $total_public_video);
# number of private videos
$sql = "SELECT count(*) AS `total` FROM `videos` WHERE
       `video_active`='1' AND
       `video_type`='private'";
$total_private_video = DB::getTotal($sql);
$smarty->assign('total_private_video', $total_private_video);
# number of users
$sql = "SELECT count(*) AS `total` FROM `users`";
$total_users = DB::getTotal($sql);
$smarty->assign('total_users', $total_users);
# number of channels
$sql = "SELECT count(*) AS `total` FROM `channels`";
$total_channel = DB::getTotal($sql);
$smarty->assign('total_channel', $total_channel);
# number of groups
$sql = "SELECT count(*) AS `total` FROM `groups`";
$total_groups = DB::getTotal($sql);
$smarty->assign('total_groups', $total_groups);

if ($config['signup_verify'] == 2) {
    $sql = "SELECT COUNT(`user_id`) AS `total` FROM `users` WHERE
           `user_email_verified`='yes' AND
           `user_account_status`='Inactive'";
    $total_users_inactive = DB::getTotal($sql);
    $smarty->assign('total_users_inactive', $total_users_inactive);
}

if (isset($total_users_inactive) && $total_users_inactive > 0) {
    $smarty->assign('show_inactive_users_warning', 1);
}

if (Config::get('recaptcha_sitekey') == '' || Config::get('recaptcha_secretkey') == '') {
    $smarty->assign('show_recaptcha_warning', '1');
}
# check version
$version_file = FREETUBESITE_DIR . '/templates_c/version.txt';
$site_version_file=FREETUBESITE_DIR . '/templates_c/site_version.txt';
if (file_exists($version_file)) {
    $last_check = filemtime($version_file);
    $time_now = $_SERVER['REQUEST_TIME'];
    $time_since_last_chek = ($time_now - $last_check) / (60 * 60);
    if ($time_since_last_chek > 1) {
        $check_version_now = 1;
    } else {
        $check_version_now = 0;
    }
} else {
    $check_version_now = 1;
}

$errno = 0;
if ($check_version_now == 1) {
 file_put_contents($version_file, file_get_contents("https://raw.githubusercontent.com/zelda180/FreeTubeSite/master/version.txt"));
}
$fn = fopen($version_file,"r") or die("fail to open file");
$latest_version = fgets($fn);

$fns = fopen($site_version_file,"r") or die("fail to open file");
$freetubesite_version = fgets($fns);

$smarty->assign('latest_version', $latest_version);
$smarty->assign('freetubesite_version', $freetubesite_version);

if ($errno == 0) {
if(md5_file($site_version_file) === md5_file($version_file)) {
        $smarty->assign('freetubesite_status', 'new');
    } else {
        $smarty->assign('freetubesite_status', 'old');
    }
}
fclose( $fn );
fclose( $fns );

$smarty->display('admin/header.tpl');
$smarty->display('admin/home.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
