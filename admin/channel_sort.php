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
require '../include/language/' . LANG . '/admin/channel_sort.php';

Admin::auth();

$admin_listing_per_page = Config::get('admin_listing_per_page');

if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $sortorder = $_POST['sortorder'];
    for ($i = 0; $i < count($id); $i ++) {
        $sql = "UPDATE `channels` SET
               `channel_sort_order`='" . (int) $sortorder[$i] . "'
                WHERE `channel_id`='" . (int) $id[$i] . "'";
        DB::query($sql);
    }
    $smarty->assign('msg', $lang['channel_sort_updated']);
}

$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'asc';

if ($sort != 'asc') {
    $sort = 'desc';
}

$query = "ORDER BY `channel_sort_order` $sort";

$page = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$sql = "SELECT count(*) AS `total` FROM `channels` $query";
$total = DB::getTotal($sql);

$start_from = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `channels`
       $query
       LIMIT $start_from, $admin_listing_per_page";
$channels_all = DB::fetch($sql);

$channels = array();

foreach ($channels_all as $channel) {
    $channel['channel_name'] = htmlspecialchars_uni($channel['channel_name']);
    $channels[] = $channel;
}

$smarty->assign('link', $links);
$smarty->assign('page', $page);
$smarty->assign('channels', $channels);
$smarty->display('admin/header.tpl');
$smarty->display('admin/channel_sort.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
