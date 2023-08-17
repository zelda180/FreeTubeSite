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

if (! is_numeric($_GET['gid'])) {
    echo 'gid empty';
    exit(0);
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$sql = "SELECT `group_name` FROM `groups` WHERE
       `group_id`=" . (int) $_GET['gid'];
$tmp = DB::fetch1($sql);
$group_name = $tmp['group_name'];

$smarty->assign('group_name', $group_name);

if (isset($_GET['action']) && $_GET['action'] == 'del' && is_numeric($_GET['TID'])) {
    $sql = "DELETE FROM `group_topics` WHERE
           `group_topic_id`='" . (int) $_GET['TID'] . "'";
    DB::query($sql);
}

$query = " WHERE `group_topic_group_id`='" . (int) $_GET['gid'] . "'";

$sort_allowed = array(
    'group_topic_title asc',
    'group_topic_title desc',
    'group_topic_add_time asc',
    'group_topic_add_time desc',
    'group_topic_approved asc',
    'group_topic_approved desc'
);

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

if (in_array($sort, $sort_allowed)) {
    $query .= " ORDER BY " . DB::quote($sort);
} else {
    $query .= " ORDER BY `group_topic_id` DESC";
}

$sql = "SELECT count(*) AS `total` FROM `group_topics` $query";
$total = DB::getTotal($sql);

$start_from = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `group_topics`
        $query
        LIMIT $start_from, $admin_listing_per_page";
$topics = DB::fetch($sql);

$smarty->assign('link', $links);
$smarty->assign('grandtotal', $total);
$smarty->assign('total', $total + 0);
$smarty->assign('page', $page + 0);
$smarty->assign('grptps', $topics);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/group_topics.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
