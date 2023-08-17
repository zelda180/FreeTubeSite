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

if (isset($_GET['sort'])) {

    $allowed_sort = array(
        'video_id desc',
        'video_id asc',
        'video_title desc',
        'video_title asc',
        'video_type desc',
        'video_type asc',
        'video_duration desc',
        'video_duration asc',
        'video_featured desc',
        'video_featured asc',
        'video_featured asc',
        'video_add_date desc',
        'video_add_date asc'
    );

    if (in_array($_GET['sort'], $allowed_sort)) {
        $query = " ORDER BY " . $_GET['sort'];
    } else {
        echo '<p>Invalid sort : ' . $_GET['sort'] . '</p>';
    }
} else {
    $query = " ORDER BY `video_id` DESC";
}

$sql = "SELECT count(*) AS `total` FROM `videos` WHERE
       `video_user_id`='0'";
$total = DB::getTotal($sql);

$start = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `videos` WHERE
       `video_user_id`='0'
        $query
        LIMIT $start, $admin_listing_per_page";
$videos = DB::fetch($sql);

$smarty->assign('links', $links);
$smarty->assign('total', $total);
$smarty->assign('page', $page);
$smarty->assign('msg', $msg);
$smarty->assign('err', $err);
$smarty->assign('videos', $videos);
$smarty->display('admin/header.tpl');
$smarty->display('admin/video_user_deleted.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
