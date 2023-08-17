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
require '../include/language/' . LANG . '/admin/groupmembers.php';

Admin::auth();

$admin_listing_per_page = Config::get('admin_listing_per_page');

$sql = "SELECT `group_name` FROM `groups` WHERE
       `group_id`='" . (int) $_GET['group_id'] . "'";
$tmp = DB::fetch1($sql);
$smarty->assign('group_name', $tmp['group_name']);

$sort_allowed = array(
    'user_id asc',
    'user_id desc',
    'user_name asc',
    'user_name desc',
    'user_country asc',
    'user_country desc',
    'user_last_login_time asc',
    'user_last_login_time desc',
    'user_account_status asc',
    'user_account_status desc'
);
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

if (in_array($sort, $sort_allowed)) {
    $query = 'ORDER BY ' . DB::quote($sort);
} else {
    $query = 'ORDER BY u.user_id DESC';
}

if (isset($_GET['action']) && $_GET['action'] == 'del') {
    if (is_numeric($_GET['uid'])) {
        $sql = "SELECT `group_owner_id` FROM `groups` WHERE
               `group_id`='" . (int) $_GET['group_id'] . "'";
        $tmp = DB::fetch1($sql);
        if ($tmp['group_owner_id'] == $_GET['uid']) {
            $err = $lang['group_owner_del'];
        } else {
            $sql = "DELETE FROM `group_members` WHERE
                   `group_member_group_id`='" . (int) $_GET['group_id'] . "' AND
                   `group_member_user_id`='" . (int) $_GET['uid'] . "'";
            DB::query($sql);
        }
    }
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$sql = "SELECT count(*) AS `total` FROM
       `group_members` AS gm,
       `users` AS u WHERE
        gm.group_member_group_id='" . (int) $_GET['group_id'] . "' AND
        gm.group_member_user_id=u.user_id";
$total = DB::getTotal($sql);

$start_from = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM
       `group_members` AS gm,
       `users` AS u WHERE
        gm.group_member_group_id='" . (int) $_GET['group_id'] . "' AND
        gm.group_member_user_id=u.user_id
        $query
        LIMIT $start_from, $result_per_page";
$users = DB::fetch($sql);

$smarty->assign('link', $links);
$smarty->assign('grandtotal', $total);
$smarty->assign('total', $total);
$smarty->assign('page', $page);
$smarty->assign('users', $users);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/group_members.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
