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

$_GET['a'] = isset($_GET['a']) ? $_GET['a'] : 'all';

if ($_GET['a'] == '') {
    $_GET['a'] = 'all';
}

if (isset($_GET['action']) && $_GET['action'] == 'del') {
    $sql = "DELETE FROM `groups` WHERE
           `group_id`='" . (int) $_GET['gid'] . "'";
    DB::query($sql);
    $sql = "DELETE FROM `group_videos` WHERE
           `group_video_group_id`='" . (int) $_GET['gid'] . "'";
    DB::query($sql);
    $sql = "DELETE FROM `group_topics` WHERE
           `group_topic_group_id`='" . (int) $_GET['gid'] . "'";
    DB::query($sql);
    $sql = "DELETE FROM `group_members` WHERE
           `group_member_group_id`='" . (int) $_GET['gid'] . "'";
    DB::query($sql);
}

if (($_GET['a'] == 'all') || ($_GET['a'] == 'public') || ($_GET['a'] == 'private') || ($_GET['a'] == 'protected')) {

    if ($_GET['a'] != 'all') {
        $query = "WHERE `group_type`='$_GET[a]'";
    } else {
        $query = '';
    }

    $_GET['sort'] = isset($_GET['sort']) ? $_GET['sort'] : '';

    if ($_GET['sort'] != '') {
        $sort = $_GET['sort'];
    } else {
        $sort = " `group_id` DESC";
    }

    $sql = "SELECT count(*) AS `total` FROM `groups`
            $query";
    $total = DB::getTotal($sql);

    $start_from = ($page - 1) * $admin_listing_per_page;

    $links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

    $sql = "SELECT * FROM `groups`
            $query
            ORDER BY $sort
            LIMIT  $start_from, $admin_listing_per_page";
    $groups = DB::fetch($sql);

    $smarty->assign('sort', $sort);
    $smarty->assign('links', $links);
    $smarty->assign('total', $total);
    $smarty->assign('page', $page);
    $smarty->assign('groups', $groups);

}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/groups.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
