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
require '../include/language/' . LANG . '/admin/channel_search.php';

Admin::auth();

if (isset($_GET['action']) && $_GET['action'] == 'search') {

    if (isset($_GET['id']) && $_GET['id'] != null) {
        if (is_numeric($_GET['id'])) {
            $channel_info = Channel::getById($_GET['id']);
            if (! $channel_info) {
                $err = str_replace("[CHANNEL_ID]", $_GET['id'], $lang['id_not_found']);
            } else {
                $channel_info = array_map("htmlspecialchars", $channel_info);
                $smarty->assign('channel', $channel_info);
            }
        } else {
            $err = $lang['id_invalid'];
        }

    } else if (isset($_GET['name']) && $_GET['name'] != null) {
        $channel_info = Channel::getByName($_GET['name']);
        if (! $channel_info) {
            $err = str_replace('[CHANNEL_NAME]', $_GET['name'], $lang['name_not_found']);
        } else {
            $channel_info = array_map("htmlspecialchars", $channel_info);
            $smarty->assign('channel', $channel_info);
        }
    }
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/channel_search.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
