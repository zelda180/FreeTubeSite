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

if (isset($_GET['sort']) && ! empty($_GET['sort'])) {
    $query = "WHERE `video_approve`=0 ORDER BY $_GET[sort]";
} else {
    $query = "WHERE `video_approve`=0 ORDER BY `video_id` DESC";
}

if (isset($_GET['action']) && $_GET["action"] == 'approve') {
    $sql = "UPDATE `videos` SET
           `video_approve`='1',
           `video_active`='1' WHERE
           `video_id`='" . (int) $_GET['video_id'] . "'";
    DB::query($sql);

    $msg = 'Video [VID: <a href="' . FREETUBESITE_URL . '/show.php?id=' . $_GET['video_id'] . '" target=_blank>' . $_GET['video_id'] . '</a>] Approved';

    $tmp = Video::getById($_GET['video_id']);
    $type = $tmp['video_type'];
    $keyword = $tmp['video_keywords'];
    $channel = $tmp['video_channels'];
    $keyword = DB::quote($keyword);

    if ($type == 'public') {
        $tags = new Tag($keyword, $_GET['video_id'], 'user_id_not_used', $channel);
        $tags->add();
        $video_tags = $tags->get_tags();
        $sql = "UPDATE `videos` SET
               `video_keywords`='" . DB::quote(implode(' ', $video_tags)) . "' WHERE
               `video_id`='" . (int) $_GET['video_id'] . "'";
        DB::query($sql);
    }

    User::updateVideoCount($tmp['video_user_id'], 1);
}

if (isset($_GET['action']) && $_GET['action'] == 'approve_all') {
    $sql = "SELECT `video_user_id` FROM `videos` WHERE
           `video_approve`='0'";
    $approved_video_users = DB::fetch($sql);

    foreach ($approved_video_users as $video_user) {
        User::updateVideoCount($video_user['video_user_id'], 1);
    }

    $sql = "UPDATE `videos` SET `video_approve`='1'";
    DB::query($sql);
    $msg = 'All Videos Approved';
}

$sql = "SELECT count(*) AS `total` FROM `videos` WHERE
       `video_approve`='0'";
$total = DB::getTotal($sql);

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start_from = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `videos`
       $query
       LIMIT $start_from, $admin_listing_per_page";
$videos = DB::fetch($sql);

$smarty->assign('links', $links);
$smarty->assign('total', $total);
$smarty->assign('page', $page);
$smarty->assign('videos', $videos);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/video_approve.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
