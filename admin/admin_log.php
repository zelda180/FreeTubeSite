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
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$allow_sort = array(
    'admin_log_ip asc',
    'admin_log_ip desc',
    'admin_log_time asc',
    'admin_log_time desc'
);

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

if (! in_array($sort, $allow_sort)) {
    $query = " ORDER BY `admin_log_id` DESC";
} else {
    $query = " ORDER BY " . DB::quote($sort);
}

$sql = "SELECT count(*) AS `total` FROM
	   `admin_log` $query";
$total = DB::getTotal($sql);
$start = ($page - 1) * $admin_listing_per_page;
$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `admin_log`
		$query
		LIMIT $start, $admin_listing_per_page";
$admin_log_info = DB::fetch($sql);

$smarty->assign('admin_log_info', $admin_log_info);
$smarty->assign('links', $links);
$smarty->assign('page', $page + 0);
$smarty->assign('sort', $sort);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/admin_log.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
