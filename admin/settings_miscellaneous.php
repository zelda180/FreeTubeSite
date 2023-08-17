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
require '../include/language/' . LANG . '/admin/miscellaneous.php';

Admin::auth();

if (isset($_POST['submit'])) {

    if ($err == '') {
        $sql = "UPDATE `sconfig` SET
               `svalue`='" . DB::quote($_POST['video_rating']) . "' WHERE
               `soption`='video_rating'";
        DB::query($sql);
        $smarty->assign('video_rating', $_POST['video_rating']);

        if (is_numeric($_POST['allow_download'])) {
            $sql = "UPDATE `sconfig` SET
                   `svalue`='" . (int) $_POST['allow_download'] . "' WHERE
                   `soption`='allow_download'";
            DB::query($sql);
            $smarty->assign('allow_download', $_POST['allow_download']);
        }

        if (is_numeric($_POST['admin_listing_per_page'])) {
            $sql = "UPDATE `config` SET
                   `config_value`='" . (int) $_POST['admin_listing_per_page'] . "' WHERE
                   `config_name`='admin_listing_per_page'";
            DB::query($sql);
        }

        if (is_numeric($_POST['recommend_all'])) {
            $sql = "UPDATE `config` SET
                   `config_value`='" . (int) $_POST['recommend_all'] . "' WHERE
                   `config_name`='recommend_all'";
            DB::query($sql);
        }

        $sql = "UPDATE `config` SET
               `config_value`='" . DB::quote($_POST['php_path']) . "' WHERE
               `config_name`='php_path'";
        DB::query($sql);

        if (is_numeric($_POST['video_comments_per_page'])) {
            $sql = "UPDATE `sconfig` SET
                   `svalue`='" . (int) $_POST['video_comments_per_page'] . "' WHERE
                   `soption`='video_comments_per_page'";
            DB::query($sql);
            $smarty->assign('video_comments_per_page', $_POST['video_comments_per_page']);
        }

        if (is_numeric($_POST['video_comment_notify'])) {
            $sql = "UPDATE `config` SET
                   `config_value`='" . (int) $_POST['video_comment_notify'] . "' WHERE
                   `config_name`='video_comment_notify'";
            DB::query($sql);
            $smarty->assign('video_comment_notify', $_POST['video_comment_notify']);
        }

        if (is_numeric($_POST['user_comments_per_page'])) {
            $sql = "UPDATE `sconfig` SET
                   `svalue`='" . (int) $_POST['user_comments_per_page'] . "' WHERE
                   `soption`='user_comments_per_page'";
            DB::query($sql);
            $smarty->assign('user_comments_per_page', $_POST['user_comments_per_page']);
        }

        if (is_numeric($_POST['mail_abuse_report'])) {
            $sql = "UPDATE `config` SET
                   `config_value`='" . (int) $_POST['mail_abuse_report'] . "' WHERE
                   `config_name`='mail_abuse_report'";
            DB::query($sql);
        }

        if (is_numeric($_POST['num_channel_video'])) {
            $sql = "UPDATE `config` SET
                   `config_value`='" . (int) $_POST['num_channel_video'] . "' WHERE
                   `config_name`='num_channel_video'";
            DB::query($sql);
        }

        if (is_numeric($_POST['num_max_channels'])) {
            $sql = "UPDATE `config` SET
                   `config_value`='" . intval($_POST['num_max_channels']) . "' WHERE
                   `config_name`='num_max_channels'";
            DB::query($sql);
        }

        if (is_numeric($_POST['user_daily_mail_limit'])) {
            $sql = "UPDATE `config` SET
                   `config_value`='" . intval($_POST['user_daily_mail_limit']) . "' WHERE
                   `config_name`='user_daily_mail_limit'";
            DB::query($sql);
        }

        if (isset($_POST['dailymotion_api_key']))
        {
            $sql = "UPDATE `config` SET
                   `config_value`='" . DB::quote($_POST['dailymotion_api_key']) . "' WHERE
                   `config_name`='dailymotion_api_key'";
            DB::query($sql);
        }

        if (isset($_POST['dailymotion_api_secret']))
        {
            $sql = "UPDATE `config` SET
                   `config_value`='" . DB::quote($_POST['dailymotion_api_secret']) . "' WHERE
                   `config_name`='dailymotion_api_secret'";
            DB::query($sql);
        }

        if (isset($_POST['youtube_api_key']))
        {
            $sql = "UPDATE `config` SET
                   `config_value`='" . DB::quote($_POST['youtube_api_key']) . "' WHERE
                   `config_name`='youtube_api_key'";
            DB::query($sql);
        }

        if (isset($_POST['episode_enable']))
        {
            $sql = "UPDATE `sconfig` SET
                   `svalue`='" . (int) $_POST['episode_enable'] . "' WHERE
                   `soption`='episode_enable'";
            DB::query($sql);
            $smarty->assign('episode_enable', $_POST['episode_enable']);
        }

        $msg = $lang['settings_updated'];
    }
}

$smarty->assign('youtube_api_key', Config::get('youtube_api_key'));
$smarty->assign('dailymotion_api_key', Config::get('dailymotion_api_key'));
$smarty->assign('dailymotion_api_secret', Config::get('dailymotion_api_secret'));
$smarty->assign('video_comment_notify', Config::get('video_comment_notify'));
$smarty->assign('user_daily_mail_limit', Config::get('user_daily_mail_limit'));
$smarty->assign('num_channel_video', Config::get('num_channel_video'));
$smarty->assign('mail_abuse_report', Config::get('mail_abuse_report'));
$smarty->assign('recommend_all', Config::get('recommend_all'));
$smarty->assign('php_path', Config::get('php_path'));
$smarty->assign('item_per_page', Config::get('admin_listing_per_page'));
$smarty->assign('editor_wysiwyg_admin', Config::get('editor_wysiwyg_admin'));
$smarty->assign('editor_wysiwyg_email', Config::get('editor_wysiwyg_email'));
$smarty->assign('num_max_channels', Config::get('num_max_channels'));
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/settings_miscellaneous.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
