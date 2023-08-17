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

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_desc';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$admin_listing_per_page = Config::get('admin_listing_per_page');

if ($page < 1) {
    $page = 1;
}

if ($sort == 'user_asc') {
    $order_by = 'p.payment_user_id ASC';
} else if ($sort == 'user_desc') {
    $order_by = 'p.payment_user_id DESC';
} else if ($sort == 'id_asc') {
    $order_by = 'p.payment_id  ASC';
} else {
    $order_by = 'p.payment_id  DESC';
}

$sql = "SELECT count(*) AS `total` FROM `payments` AS p,`users` AS u,`packages` AS pa WHERE
		u.user_id=p.payment_user_id AND p.payment_package_id=pa.package_id";
$total = DB::getTotal($sql);

if ($total) {

    $start_from = ($page - 1) * $admin_listing_per_page;

    $links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

    $sql = "SELECT * FROM `payments` AS p,`users` AS u,`packages` AS pa WHERE
			u.user_id=p.payment_user_id AND p.payment_package_id=pa.package_id ORDER BY $order_by LIMIT $start_from, $admin_listing_per_page";
    $payments_all = DB::fetch($sql);

    $payment_info = array();

    foreach ($payments_all as $payment) {
        $period = $payment['payment_amount'] / $payment['package_price'];
        $payment['total_period'] = $period;
        $payment_info[] = $payment;
    }

    $smarty->assign('payment_info', $payment_info);
    $smarty->assign('page_links', $links);
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $payment_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $redirect_url = $config['baseurl'] . '/admin/payments.php';

    if ($payment_id == 0) {
        Http::redirect($redirect_url);
    }

    $sql = "DELETE FROM `payments` WHERE `payment_id`='$payment_id'";
    DB::query($sql);

    Http::redirect($redirect_url);
}

$smarty->display('admin/header.tpl');
$smarty->display('admin/payments.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
