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

$category = '';

if (isset($_GET['chid']) && is_numeric($_GET['chid'])) {
    $sql = "SELECT * FROM `channels` WHERE
           `channel_id`='" . (int) $_GET['chid'] . "'";
    $tmp = DB::fetch1($sql);
    $category_tpl = htmlspecialchars_uni($tmp['channel_name']);
} else {
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
    } else {
        $redirect_url = FREETUBESITE_URL . '/groups/featured/1';
        Http::redirect($redirect_url);
    }
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

if (isset($_GET['chid']) && is_numeric($_GET['chid'])) {
    $sql = "SELECT count(*) AS `total` FROM `groups` WHERE
           `group_channels` LIKE '%|" . (int) $_GET['chid'] . "|%'";
    $rows = '';
} else {

    if ($category == 'featured') {
        $sql = "SELECT count(*) AS `total` FROM `groups` WHERE
               `group_featured`='yes'";
        $rows = 0;
        $category_tpl = 'Featured';
    } else if ($category == 'recent') {
        $sql = "SELECT count(*) AS `total` FROM `groups`";
        $rows = 0;
        $category_tpl = 'Most Recent';
    } else if ($category == 'members') {
//        $sql = "SELECT DISTINCT count(*) AS `total`,`group_member_user_id` FROM
        $sql = "SELECT count(*) AS `total`,`group_member_user_id` FROM
               `group_members` AS gm,
               `groups` AS g WHERE
                gm.group_member_group_id=g.group_id
                GROUP BY gm.group_member_group_id";
        $rows = 1;
        $category_tpl = 'Most Members';
    } else if ($category == 'videos') {
//        $sql = "SELECT DISTINCT *,count(group_video_video_id) AS `total` FROM
        $sql = "SELECT *,count(group_video_video_id) AS `total` FROM
               `group_videos` AS gv,
               `groups` AS g WHERE
                gv.group_video_group_id=g.group_id
                GROUP BY gv.group_video_group_id
                ORDER BY `total` DESC";
        $rows = 1;
        $category_tpl = 'Most Videos';
    } else {
//        $sql = "SELECT DISTINCT *,count(gt.group_topic_group_id) AS `total` FROM
        $sql = "SELECT *,count(gt.group_topic_group_id) AS `total` FROM
               `group_topics` AS gt,
               `groups` AS g WHERE
                gt.group_topic_group_id=g.group_id
                GROUP BY gt.group_topic_group_id
                ORDER BY `total` DESC";
        $rows = 1;
        $category_tpl = 'Most Topics';
    }
}

if ($rows == 0) {
    $tmp = DB::fetch1($sql);
    $total = $tmp['total'];
} else if ($rows == 1) {
    $tmp = DB::fetch($sql);
    $total = count($tmp);
}

$start_from = ($page - 1) * $config['items_per_page'];

if (isset($_GET['chid']) && is_numeric($_GET['chid'])) {
    $sql = "SELECT * FROM `groups` WHERE
           `group_channels` LIKE '%|" . (int) $_GET['chid'] . "|%'
            LIMIT $start_from, $config[items_per_page]";
} else {
    if ($category == 'featured') {
        $sql = "SELECT * FROM `groups` WHERE
               `group_featured`='yes'
                LIMIT $start_from, $config[items_per_page]";
    } else if ($category == 'recent') {
        $sql = "SELECT * FROM `groups`
                ORDER BY `group_create_time` DESC
                LIMIT $start_from, $config[items_per_page]";
    } else if ($category == 'members') {
        //$sql = "SELECT DISTINCT *,count(gm.group_member_user_id) AS `total` FROM
        $sql = "SELECT *,count(gm.group_member_user_id) AS `total` FROM
               `group_members` AS gm,
               `groups` AS g WHERE
                gm.group_member_group_id=g.group_id
                GROUP BY gm.group_member_group_id
                ORDER BY `total` DESC
                LIMIT $start_from, $config[items_per_page]";
    } else if ($category == 'videos') {
        //$sql = "SELECT DISTINCT *,count(gv.group_video_video_id) AS `total` FROM
        $sql = "SELECT *,count(gv.group_video_video_id) AS `total` FROM
               `group_videos` AS gv,
               `groups` AS g WHERE
                gv.group_video_group_id=g.group_id
                GROUP BY gv.group_video_group_id
                ORDER BY `total` DESC
                LIMIT $start_from, $config[items_per_page]";
    } else {
        //$sql = "SELECT DISTINCT *,count(gt.group_topic_group_id) AS `total` FROM
        $sql = "SELECT *,count(gt.group_topic_group_id) AS `total` FROM
               `group_topics` AS gt,
               `groups` AS g WHERE
                gt.group_topic_group_id=g.group_id
                GROUP BY gt.group_topic_group_id
                ORDER BY `total` DESC
                LIMIT $start_from, $config[items_per_page]";
    }
}

$group_info = DB::fetch($sql);

if (count($group_info) == 0 && $category == 'featured') {
    $sql = "SELECT * FROM `groups` LIMIT 4";
    $group_info = DB::fetch($sql);
    $total = count($group_info);
}

$start_num = $start_from + 1;
$end_num = $start_from + count($group_info);

$page_link = Paginate::getLinks2($total, $config['items_per_page'], '.', '', $page);

$channels = Channel::get();

$html_title = "$category_tpl Groups - page $page";

$smarty->assign(array(
    'html_title' => $html_title,
    'html_description' => $html_title,
    'html_keywords' => $html_title
));

$smarty->assign('channels', $channels);
$smarty->assign('category', $category_tpl);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('page', $page);
$smarty->assign('start_num', $start_num);
$smarty->assign('end_num', $end_num);
$smarty->assign('page_links', $page_link);
$smarty->assign('total', $total);
$smarty->assign('group_info', $group_info);
$smarty->assign('sub_menu', 'menu_groups.tpl');
$smarty->display('header.tpl');
$smarty->display('groups.tpl');
$smarty->display('footer.tpl');
DB::close();
