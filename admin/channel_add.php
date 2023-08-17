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
require '../include/language/' . LANG . '/admin/channel_add.php';

Admin::auth();

if (isset($_POST['add_channel'])) {

    if ($_POST['channel_name'] == '') {
        $err = $lang['channel_name_null'];
    } else if ($_POST['channel_description'] == '') {
        $err = $lang['channel_description_null'];
    } else if ($_FILES['channel_image']['name'] == '') {
        $err = $lang['channel_image'];
    }

    $seo_name = Url::seoName($_POST['channel_name']);

    if (check_field_exists($_POST['channel_name'], 'channel_name', 'channels')) {
        $err = $lang['channel_exists'];
    } else if (check_field_exists($seo_name, 'channel_seo_name', 'channels')) {
        $err = $lang['channel_exists'];
    }

    if ($err == '') {
        $sql = "INSERT INTO `channels` SET
               `channel_name`='" . DB::quote($_POST['channel_name']) . "',
               `channel_seo_name`='" . DB::quote($seo_name) . "',
               `channel_description`='" . DB::quote($_POST['channel_description']) . "'";
        $channel_id = DB::insertGetId($sql);
        $err = upload_jpg($_FILES, 'channel_image', $channel_id . '.jpg', 120, FREETUBESITE_DIR . '/chimg/');
    }

    if ($err == '') {
        set_message($lang['channel_added'], 'success');
        $redirect_url = FREETUBESITE_URL . '/admin/channels.php';
        Http::redirect($redirect_url);
    }
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/channel_add.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
