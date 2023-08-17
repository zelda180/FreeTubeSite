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
if (isset($_GET['keyword'])) {
    $search_string = $_GET['keyword'];
    $page = isset($_GET['page']) ? $_GET['page'] : '';
    $user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';
    $channel_id = isset($_GET['channel']) ? (int) $_GET['channel'] : 0;
    $admin_listing_per_page = Config::get('admin_listing_per_page');
    $user_info = User::getByName($user_name);

    if ($search_string == '') {
        $err = 'Please enter keyword for search.';
    } else if (! $user_info) {
        $err = 'User not found - ' . $_GET['user_name'];
    } else if (! Channel::getById($channel_id)) {
        $err = 'Please select a channel.';
    }

    if ($err == '') {
        $videos = Youtube::getVideos($search_string, 10, $page);

        if (count($videos['videos']) > 1) {
            $smarty->assign('videos', $videos);
        } else {
            $err = 'There are no videos found with keyword.';
        }

        $smarty->assign('user_name', $user_name);
        $smarty->assign('channel_id', $channel_id);
    }
}

$smarty->assign('channels', Channel::get());
$smarty->assign('err', $err);
$smarty->display('admin/header.tpl');
$smarty->display('admin/import_bulk.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
