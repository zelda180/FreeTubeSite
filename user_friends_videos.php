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

require 'include/config.php';

User::is_logged_in();

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$sql = "SELECT `friend_friend_id` FROM `friends` WHERE
       `friend_user_id`='" . (int) $_SESSION['UID'] . "' AND
       `friend_status`='Confirmed'";
$friends_all = DB::fetch($sql);

if ($friends_all) {

    foreach ($friends_all as $friend) {
        $friends[] = $friend['friend_friend_id'];
    }

    $my_friends = implode(',', $friends);

    if (isset($_REQUEST['type']) && $_REQUEST['type'] != "private") {
        $_REQUEST['type'] = 'public';
    }

    $sql = "SELECT count(*) AS `total` FROM `videos` WHERE
           `video_user_id` IN (" . DB::quote($my_friends) . ")";
    $total = DB::getTotal($sql);

    $start_from = ($page - 1) * $config['items_per_page'];

    $page_links = Paginate::getLinks2($total, $config['items_per_page'], './', $page);

    $sql = "SELECT * FROM `videos` WHERE
           `video_user_id` IN (" . DB::quote($my_friends) . ")
            ORDER BY `video_add_time` DESC
            LIMIT $start_from, $config[items_per_page]";
    $videos_all = DB::fetch($sql);

    $video_keywords_all = '';

    foreach ($videos_all as $videoRow) {
        $videoRow['video_thumb_url'] = $servers[$videoRow['video_thumb_server_id']];
        $videoRow['video_keywords_array'] = explode(' ', $videoRow['video_keywords']);
        $video_keywords_all .= $videoRow['video_keywords'] . ' ';
        $videoRows[] = $videoRow;
    }

    $view = array();
    $video_keywords_array_all = explode(' ', $video_keywords_all);
    $view['video_keywords_array_all'] = array_remove_duplicate($video_keywords_array_all);

    $start_num = $start_from + 1;
    $end_num = $start_from + count($videoRows);
    $smarty->assign('view', $view);
    $smarty->assign('start_num', $start_num);
    $smarty->assign('end_num', $end_num);
    $smarty->assign('total', $total);
    $smarty->assign('page_links', $page_links);
    $smarty->assign('videoRows', $videoRows);
    $smarty->assign('page', $page);
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('sub_menu', 'menu_friends.tpl');
$smarty->display('header.tpl');
$smarty->display('user_friends_videos.tpl');
$smarty->display('footer.tpl');
DB::close();
