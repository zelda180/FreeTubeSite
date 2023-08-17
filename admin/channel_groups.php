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
require '../include/language/' . LANG . '/admin/channel_groups.php';

Admin::auth();

$admin_listing_per_page = Config::get('admin_listing_per_page');

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$tmp = Channel::getById($_GET['chid']);
$smarty->assign('channel_name', $tmp['channel_name']);

if (isset($_GET['action']) && $_GET['action'] == 'del') {
    $sql = "SELECT `group_channels` FROM `groups` WHERE
           `group_id`='" . (int) $_GET['gid'] . "'";
    $channel = DB::fetch1($sql);
    $ch = explode("|", $channel['channel']);

    if (count($ch) <= 3) {
        $err = $lang['group_channel_last'];
    } else {
        $new_type = str_replace("|$_GET[chid]|", '|', $channel['channel']);
        $sql = "UPDATE `groups` SET `channel`='$new_type' WHERE
           `group_id`='" . (int) $_GET['gid'] . "'";
        DB::query($sql);
    }
}

$query = " WHERE `group_channels` LIKE '%|" . (int) $_GET['chid'] . "|%'";

if (isset($_GET['sort']) && $_GET['sort'] != '') {
    $query .= " ORDER BY $_GET[sort]";
} else {
    $query .= " ORDER BY `group_id` ASC";
}

$sql = "SELECT count(*) AS `total` FROM `groups` $query";
$total = DB::getTotal($sql);

$start_from = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `groups` $query
        LIMIT $start_from, $admin_listing_per_page";
$groups = DB::fetch($sql);

$smarty->assign('link', $links);
$smarty->assign('grandtotal', $total);
$smarty->assign('total', $total);
$smarty->assign('page', $page);
$smarty->assign('groups', $groups);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/channel_groups.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
