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
include '../include/config.php';

Admin::auth();

$admin_listing_per_page = Config::get('admin_listing_per_page');

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

if (isset($_GET['action']) && $_GET['action'] == 'del') {
    if (is_numeric($_GET['id'])) {
        $sql = "DELETE FROM `comments` WHERE
               `comment_id`='" . (int) $_GET['id'] . "'";
        DB::query($sql);
    }
}

$sql = "SELECT count(*) AS `total` FROM `comments`";
$total = DB::getTotal($sql);

$start_from = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM
       `comments` AS c,
       `users` AS u WHERE
        c.comment_user_id=u.user_id
        ORDER BY c.comment_id DESC
        LIMIT $start_from, $admin_listing_per_page";
$comments = DB::fetch($sql);

$smarty->assign('links', $links);
$smarty->assign('page', $page);
$smarty->assign('total', $total);
$smarty->assign('comments', $comments);
$smarty->display('admin/header.tpl');
$smarty->display('admin/comment.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
