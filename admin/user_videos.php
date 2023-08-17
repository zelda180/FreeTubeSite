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
require '../include/language/' . LANG . '/admin/user_videos.php';

Admin::auth();

if (! is_numeric($_GET['uid'])) {
    echo $lang['user_id_invalid'];
    exit(0);
}

$admin_listing_per_page = Config::get('admin_listing_per_page');

$user_info = User::getById($_GET['uid']);
$smarty->assign('user_name', $user_info['user_name']);
$smarty->assign('user_id', $_GET['uid']);

$query = " WHERE `video_user_id`='" . (int) $_GET['uid'] . "'";

if (isset($_GET['sort'])) {
    $query .= " ORDER BY $_GET[sort]";
} else {
    $query .= " ORDER BY `video_id` ASC";
}

$sql = "SELECT count(*) AS `total` FROM `videos`
        $query";
$total = DB::getTotal($sql);

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start_from = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `videos`
       $query
       LIMIT $start_from, $admin_listing_per_page";
$videos = DB::fetch($sql);

$smarty->assign('links', $links);
$smarty->assign('total', $total + 0);
$smarty->assign('page', $page + 0);
$smarty->assign('videos', $videos);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/user_videos.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
