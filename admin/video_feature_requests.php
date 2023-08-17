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
require '../include/language/' . LANG . '/admin/video_feature_requests.php';
Admin::auth();
$admin_listing_per_page = Config::get('admin_listing_per_page');
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$sort_allowed = array(
    'feature_request_video_id asc',
    'feature_request_video_id desc',
    'feature_request_count asc',
    'feature_request_count desc',
    'feature_request_date asc',
    'feature_request_date desc'
);

if ((isset($_GET['sort'])) && (in_array($_GET['sort'], $sort_allowed))) {
    $sort = ' ORDER BY fr.' . $_GET['sort'];
} else {
    $sort = ' ORDER BY fr.feature_request_video_id DESC';
}

if ((isset($_GET['action'])) && ($_GET['action'] == 'del')) {
    if (is_numeric($_GET['vid'])) {
        $sql = "DELETE FROM `feature_requests` WHERE
               `feature_request_video_id`='" . (int) $_GET['vid'] . "'";
        DB::query($sql);
    }
}

if ((isset($_GET['action'])) && ($_GET['action'] == 'delete_all')) {
    $sql = "DELETE FROM `feature_requests`";
    DB::query($sql);
}

if ((isset($_GET['action'])) && ($_GET['action'] == 'approve')) {
    if (is_numeric($_GET['vid'])) {
        $sql = "UPDATE `videos` SET
               `video_featured`='yes' WHERE
               `video_id`='" . (int) $_GET['vid'] . "'";
        DB::query($sql);
        $sql = "DELETE FROM `feature_requests` WHERE
               `feature_request_video_id`='" . (int) $_GET['vid'] . "'";
        DB::query($sql);
        $msg = $lang['video_featured'];
    }
}

$query = " WHERE fr.feature_request_video_id=v.video_id AND v.video_featured='no'";

$sql = "SELECT count(fr.feature_request_video_id) AS `total` FROM
       `feature_requests` fr,
       `videos` v
        $query";
$total = DB::getTotal($sql);

$start_from = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM
       `feature_requests` fr,
       `videos` v
        $query  $sort
        LIMIT $start_from, $admin_listing_per_page";
$videos = DB::fetch($sql);

$smarty->assign('links', $links);
$smarty->assign('total', $total);
$smarty->assign('page', $page + 0);
$smarty->assign('videos', $videos);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/video_feature_requests.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
