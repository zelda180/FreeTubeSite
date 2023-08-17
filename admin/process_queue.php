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
require '../include/language/' . LANG . '/admin/process_queue.php';
Admin::auth();

$admin_listing_per_page = Config::get('admin_listing_per_page');

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $sql = "SELECT * FROM `process_queue` WHERE
           `id`='" . (int) $id . "'";
    $tmp = DB::fetch1($sql);
    $sql = "DELETE FROM `process_queue` WHERE
           `id`='" . (int) $id . "'";
    DB::query($sql);

    $video_path = FREETUBESITE_DIR . '/video/' . $tmp['file'];

    if (file_exists($video_path) && is_file($video_path)) {
        $vid = $tmp['vid'];
        if ($vid == 0) {
            unlink($video_path);
        }
    }
    $msg = $lang['process_q_deleted'];
}

if ((isset($_GET['action'])) && ($_GET['action'] == 'delete_all')) {
    $sql = "SELECT * FROM `process_queue`";
    $process_queue_all = DB::fetch($sql);

    foreach ($process_queue_all as $process_queue) {
        $video_path = FREETUBESITE_DIR . '/video/' . $process_queue['file'];
        if (file_exists($video_path) && is_file($video_path)) {
            $vid = $process_queue['vid'];
            if ($vid == 0) {
                if (! unlink($video_path)) {
                    echo str_replace('[VIDEO_PATH]', $video_path, $lang['unable_to_delete']);
                    exit(0);
                }
            }
        }
    }

    $sql = "DELETE FROM  `process_queue`";
    DB::query($sql);
    $msg = $lang['process_q_deleted'];
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$sql = "SELECT count(*) AS `total` FROM
       `process_queue` AS p,
       `users` AS u WHERE
        p.user=u.user_name ORDER BY
       `status` ASC";
$total = DB::getTotal($sql);

$start = ($page - 1) * $admin_listing_per_page;

$links = Paginate::getLinks2($total, $admin_listing_per_page, '', $page);

$sql = "SELECT * FROM
       `process_queue` AS p,
       `users` AS u WHERE
        p.user=u.user_name
        ORDER BY `id` DESC
        LIMIT $start, $admin_listing_per_page";
$process_queue_info = DB::fetch($sql);

$smarty->assign('msg', $msg);
$smarty->assign('page', $page);
$smarty->assign('links', $links);
$smarty->assign('process_queue', $process_queue_info);
$smarty->display('admin/header.tpl');
$smarty->display('admin/process_queue.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
