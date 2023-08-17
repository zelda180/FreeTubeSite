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
require '../include/language/' . LANG . '/admin/settings_home.php';
Admin::auth();

if (isset($_POST['submit']))
{
    $sql = "UPDATE `sconfig` SET
           `svalue`='" . DB::quote($_POST['user_poll']) . "' WHERE
           `soption`='user_poll'";
    DB::query($sql);
    $smarty->assign('user_poll', $_POST['user_poll']);

    if (is_numeric($_POST['home_num_tags']))
    {
        $sql = "UPDATE `config` SET
               `config_value` ='" . (int) $_POST['home_num_tags'] . "' WHERE
               `config_name`='home_num_tags'";
        DB::query($sql);
    }

    if (is_numeric($_POST['num_last_users_online']))
    {
        $sql = "UPDATE `config` SET
               `config_value`='" . (int) $_POST['num_last_users_online'] . "' WHERE
               `config_name`='num_last_users_online'";
        DB::query($sql);
    }

    if (is_numeric($_POST['num_new_videos']))
    {
        $sql = "UPDATE `sconfig` SET
               `svalue`='" . (int) $_POST['num_new_videos'] . "' WHERE
               `soption`='num_new_videos'";
        DB::query($sql);
        $smarty->assign('num_new_videos', $_POST['num_new_videos']);
    }

    if (is_numeric($_POST['recently_viewed_video']))
    {
        $sql = "UPDATE `sconfig` SET
               `svalue`='" . (int) $_POST['recently_viewed_video'] . "' WHERE
               `soption`='recently_viewed_video'";
        DB::query($sql);
        $smarty->assign('recently_viewed_video', $_POST['recently_viewed_video']);
    }

    $sql = "UPDATE `sconfig` SET
           `svalue`='" . (int) $_POST['show_stats'] . "' WHERE
           `soption`='show_stats'";
    DB::query($sql);
    $smarty->assign('show_stats', $_POST['show_stats']);

    $msg = $lang['settings_updated'];
}

$smarty->assign('home_num_tags', Config::get('home_num_tags'));
$smarty->assign('num_last_users_online', Config::get('num_last_users_online'));
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/settings_home.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
