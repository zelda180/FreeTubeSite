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
require '../include/language/' . LANG . '/admin/settings_player.php';

Admin::auth();

if (isset($_POST['submit']))
{
    if (is_numeric($_POST['player_autostart']))
    {
        $sql = "UPDATE `sconfig` SET
               `svalue`='" . (int) $_POST['player_autostart'] . "' WHERE
               `soption`='player_autostart'";
        DB::query($sql);
        $smarty->assign('player_autostart', $_POST['player_autostart']);
    }

    if (is_numeric($_POST['player_bufferlength']))
    {
        $sql = "UPDATE `sconfig` SET
               `svalue`='" . (int) $_POST['player_bufferlength'] . "' WHERE
               `soption`='player_bufferlength'";
        DB::query($sql);
        $smarty->assign('player_bufferlength', $_POST['player_bufferlength']);
    }

    if (is_numeric($_POST['player_width']))
    {
        $sql = "UPDATE `sconfig` SET
               `svalue`='" . (int) $_POST['player_width'] . "' WHERE
               `soption`='player_width'";
        DB::query($sql);
        $smarty->assign('player_width', $_POST['player_width']);
    }

    if (is_numeric($_POST['player_height']))
    {
        $sql = "UPDATE `sconfig` SET
               `svalue`='" . (int) $_POST['player_height'] . "' WHERE
               `soption`='player_height'";
        DB::query($sql);
        $smarty->assign('player_height', $_POST['player_height']);
    }

    if (isset($_POST['youtube_player']))
    {
        $sql = "UPDATE `config` SET
               `config_value`='" . DB::quote($_POST['youtube_player']) . "' WHERE
               `config_name`='youtube_player'";
        DB::query($sql);
    }

    if (isset($_POST['freetubesite_player']))
    {
        $sql = "UPDATE `config` SET
               `config_value`='" . DB::quote($_POST['freetubesite_player']) . "' WHERE
               `config_name`='freetubesite_player'";
        DB::query($sql);
    }

    $sql = "UPDATE `sconfig` SET
           `svalue`='" . DB::quote($_POST['watermark_url']) . "' WHERE
           `soption`='watermark_url'";
    DB::query($sql);
    $smarty->assign('watermark_url', $_POST['watermark_url']);

    $sql = "UPDATE `sconfig` SET
           `svalue`='" . DB::quote($_POST['watermark_image_url']) . "' WHERE
           `soption`='watermark_image_url'";
    DB::query($sql);
    $smarty->assign('watermark_image_url', $_POST['watermark_image_url']);

    $msg = $lang['settings_updated'];
}

$smarty->assign('freetubesite_player', Config::get('freetubesite_player'));
$smarty->assign('youtube_player', Config::get('youtube_player'));
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/settings_player.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
