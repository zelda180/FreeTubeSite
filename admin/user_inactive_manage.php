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

if ($config['signup_verify'] != 2) {
    set_message('Admin activation disabled.', 'error');
    Http::redirect($_SERVER['HTTP_REFERER']);
}

if (isset($_POST['user_ids'])) {
    foreach ($_POST['user_ids'] as $user_id) {
        User::delete($user_id, 1);
    }
    set_message('Selected users have been deleted.', 'success');
    $redirect_url = FREETUBESITE_URL . '/admin/user_inactive_manage.php';
    Http::redirect($redirect_url);
}

$admin_listing_per_page = Config::get('admin_listing_per_page');
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = ($page < 1) ? 1 : $page;

$sql = "SELECT COUNT(`user_id`) AS `total` FROM `users` WHERE
       `user_account_status`='Inactive'";
$total = DB::getTotal($sql);

$start_from = ($page - 1) * $admin_listing_per_page;
$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql_extra = '';

if (! empty($_GET['sort'])) {
    $sql_extra .= " ORDER BY " . DB::quote($_GET['sort']);
} else {
    $sql_extra .= " ORDER BY `user_id` DESC";
}

$sql = "SELECT * FROM `users` WHERE
       `user_account_status`='Inactive'
        $sql_extra
        LIMIT $start_from, $admin_listing_per_page";
$users = DB::fetch($sql);

$smarty->assign('links', $links);
$smarty->assign('total', $total + 0);
$smarty->assign('page', $page + 0);
$smarty->assign('users', $users);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/user_inactive_manage.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
