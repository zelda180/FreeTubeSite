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
require '../include/language/' . LANG . '/admin/user_search.php';

Admin::auth();

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';
$user_ip = isset($_GET['user_ip']) ? $_GET['user_ip'] : '';

if (! empty($user_id)) {
    if (is_numeric($user_id)) {
        $user_info = User::getById($user_id);
        if (! $user_info) {
            $err = str_replace('[USER_ID]', $user_id, $lang['user_id_not_found']);
        } else {
            $redirect_url = FREETUBESITE_URL . '/admin/user_view.php?user_id=' . $user_id;
            Http::redirect($redirect_url);
        }
    } else {
        $err = $lang['user_id_invalid'];
    }
} else if (! empty($user_name)) {
    $user_info = User::getByName($user_name);
    if (! $user_info) {
        $err = str_replace('[USERNAME]', $user_name, $lang['user_name_not_found']);
    } else {
        $redirect_url = FREETUBESITE_URL . '/admin/user_view.php?user_id=' . $user_info['user_id'];
        Http::redirect($redirect_url);
    }
} else if (! empty($user_ip)) {

    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

    if ($page < 1) {
        $page = 1;
    }

    if (! empty($_GET['sort'])) {
        $query = " ORDER BY " . DB::quote($_GET['sort']);
    } else {
        $query = " ORDER BY `user_id` DESC";
    }

    $sql = "SELECT count(*) AS `total` FROM `users` WHERE
           `user_ip` LIKE '%" . DB::quote($user_ip) . "%'";
    $total = DB::getTotal($sql);

    $admin_listing_per_page = Config::get('admin_listing_per_page');
    $start_from = ($page - 1) * $admin_listing_per_page;

    $links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

    $sql = "SELECT * FROM `users` WHERE
           `user_ip` LIKE '%" . DB::quote($user_ip) . "%'
            $query
            LIMIT $start_from, $admin_listing_per_page";
    $users = DB::fetch($sql);

    $smarty->assign('links', $links);
    $smarty->assign('total', $total + 0);
    $smarty->assign('page', $page + 0);
    $smarty->assign('users', $users);
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/user_search.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
