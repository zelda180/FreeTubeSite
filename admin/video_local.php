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
include '../include/config.php';
Admin::auth();
$admin_listing_per_page = Config::get('admin_listing_per_page');
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$allowed_sort = array(
    "video_id asc",
    "video_id desc",
    "video_title asc",
    "video_title desc",
    "video_type asc",
    "video_type desc",
    "video_duration asc",
    "video_duration desc",
    "video_add_date asc",
    "video_add_date desc"
);

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

if (in_array($sort, $allowed_sort)) {
    $query = ' ORDER BY ' . $sort;
} else {
    $query = " ORDER BY `video_id` DESC";
}

$sql = "SELECT * FROM `servers` WHERE
        `server_type`!=1 AND
        `status`=1
        ORDER BY `space_used` ASC
        LIMIT 1";
$servers = DB::fetch($sql);

$sql = "SELECT count(`video_id`) AS `total` FROM `videos` WHERE
       `video_vtype`='0' AND
       `video_server_id`='0'";
$total = DB::getTotal($sql);

$start = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `videos` WHERE
       `video_vtype`='0' AND
       `video_server_id`='0'
        $query
        LIMIT $start, $admin_listing_per_page";
$videos_local_all = DB::fetch($sql);

$smarty->assign(array(
    'links' => $links,
    'total' => $total + 0,
    'page' => $page + 0,
    'videos_local_all' => $videos_local_all,
    'servers' => $servers
));

$smarty->display('admin/header.tpl');
$smarty->display('admin/video_local.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
