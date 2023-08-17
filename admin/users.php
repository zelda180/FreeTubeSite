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

if (! isset($_GET['a']) || $_GET['a'] == '') {
    $_GET['a'] = 'All';
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

if ($_GET['a'] != 'All') {
    $query = "WHERE `user_account_status`='" . DB::quote($_GET['a']) . "'";
} else {
    $query = '';
}

if (! empty($_GET['sort'])) {
    $query .= " ORDER BY " . DB::quote($_GET['sort']);
} else {
    $query .= " ORDER BY `user_id` DESC";
}

$sql = "SELECT count(*) AS `total` FROM `users`
        $query";
$total = DB::getTotal($sql);

$start_from = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `users`
        $query
        LIMIT $start_from, $admin_listing_per_page";
$users = DB::fetch($sql);

$smarty->assign('links', $links);
$smarty->assign('total', $total + 0);
$smarty->assign('page', $page + 0);
$smarty->assign('users', $users);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/users.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
