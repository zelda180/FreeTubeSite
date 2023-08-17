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
require '../include/language/' . LANG . '/admin/video_inactive.php';
Admin::auth();
$action = isset($_GET['action']) ? $_GET['action'] : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

if ($action == 'activate') {
    if (is_numeric($_GET['video_id'])) {
        $video_info = Video::getById($_GET['video_id']);
        if ($video_info) {
	        $sql = "UPDATE `videos` SET
	               `video_active`='1' WHERE
	               `video_id`='" . (int) $_GET['video_id'] . "'";
	        DB::query($sql);
	        User::updateVideoCount($video_info['video_user_id'], 1);
	        $msg = $lang['activate_video'];
        }
    }
}

if ($action == 'activate_all')
{
    $sql = "SELECT `video_user_id` FROM `videos` WHERE
           `video_active`='0'";
    $videos_inactive_all = DB::fetch($sql);

    foreach($videos_inactive_all as $inactive_video) {
        User::updateVideoCount($inactive_video['video_user_id'], 1);
    }

    $sql = "UPDATE `videos` SET `video_active`='1'";
    DB::query($sql);
    $msg = $lang['activate_all_video'];
}

if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
} else {
    $sort = " `video_id` DESC";
}

$sql = "SELECT count(*) AS `total` FROM `videos` WHERE
       `video_active`='0' AND
       `video_user_id`!='0'
        ORDER BY $sort";
$total = DB::getTotal($sql);

$admin_listing_per_page = Config::get('admin_listing_per_page');
$start = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM `videos` WHERE
       `video_active`='0' AND
       `video_user_id`!='0'
        ORDER BY $sort
        LIMIT $start, $admin_listing_per_page";
$videos_inactive_all = DB::fetch($sql);

$smarty->assign('videos_inactive_all', $videos_inactive_all);
$smarty->assign('msg', $msg);
$smarty->assign('links', $links);
$smarty->assign('total', $total);
$smarty->display('admin/header.tpl');
$smarty->display('admin/video_inactive.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
