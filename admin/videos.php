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

if (! isset($_GET['a']) || $_GET['a'] == '') {
    $_GET['a'] = 'all';
}

$view_types = array('all', 'public', 'private', 'adult', 'embedded');

if (isset($_POST['video_id_arr'])) {
    $video_id_arr = $_POST['video_id_arr'];
    $video_id_arr = array_unique($video_id_arr);
    $video_id_arr_count = count($video_id_arr);

    for ($i = 0;$i < $video_id_arr_count;$i++) {
        $video_info = Video::getById($video_id_arr[$i]);
        if ($video_info) {
            Video::delete($video_info['video_id'], $video_info['video_user_id']);
        }
    }
    $msg = 'Selected Videos are Deleted.';
}

if (! in_array($_GET['a'], $view_types)) {
    die('Invalid video type: ' . $_GET['a']);
}

if ($_GET['a'] == 'all') {
    $query = '';
} else if ($_GET['a'] == 'adult') {
    $query = "WHERE `video_adult`='1'";
} else if ($_GET['a'] == 'embedded') {
    $query = "WHERE `video_vtype`>'0'";
} else {
    $query = "WHERE `video_type`='$_GET[a]'";
}

if (! isset($_GET['sort']) || $_GET['sort'] == '') {
    $query .= " ORDER BY `video_id` DESC";
} else {
    $query .= " ORDER BY $_GET[sort]";
}

$sql = "SELECT count(*) AS `total` FROM
       `videos` $query";
$total = DB::getTotal($sql);

$start = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `videos`
       $query
       LIMIT $start, $admin_listing_per_page";
$videos = DB::fetch($sql);

$smarty->assign('links', $links);
$smarty->assign('grandtotal', $total);
$smarty->assign('total', $total);
$smarty->assign('page', $page);
$smarty->assign('videos', $videos);
$smarty->assign('a', $_GET['a']);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/videos.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
