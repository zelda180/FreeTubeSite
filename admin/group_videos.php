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

$admin_listing_per_page = Config::get('admin_listing_per_page');

$sql = "SELECT `group_name` FROM `groups` WHERE
       `group_id`='" . (int) $_GET['gid'] . "'";
$tmp = DB::fetch1($sql);
$smarty->assign('group_name', $tmp['group_name']);

$gid = $_GET['gid'];

if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
} else {
    $sort = 'v.video_id ASC';
}

if ($sort == 'video_id asc' || $sort == 'video_id desc') {
    $sort = 'v.' . $sort;
}

if (isset($_GET['action']) && $_GET['action'] == 'del') {
    $VID = $_GET['video_id'];
    $sql = "DELETE FROM `group_videos` WHERE
           `group_video_group_id`=" . (int) $gid . " AND
           `group_video_video_id`='" . (int) $VID . "'";
    DB::query($sql);
}

$query = ' WHERE gv.group_video_group_id=' . (int) $gid;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$sql = "SELECT count(*) AS `total` FROM
       `group_videos` AS gv,
       `videos` AS v
        $query AND
        gv.group_video_video_id=v.video_id";
$total = DB::getTotal($sql);
$grandtotal = $total;
$start_from = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM
       `group_videos` AS gv,
       `videos` AS v
        $query AND
        gv.group_video_video_id=v.video_id
        ORDER BY $sort
        LIMIT $start_from, $admin_listing_per_page";
$videos = DB::fetch($sql);

$smarty->assign('link', $links);
$smarty->assign('grandtotal', $grandtotal + 0);
$smarty->assign('total', $total + 0);
$smarty->assign('page', $page + 0);
$smarty->assign('videos', $videos);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/group_videos.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
