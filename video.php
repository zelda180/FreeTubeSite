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

Cache::init();

$sql_adult_filter = '';

if (getFamilyFilter()) {
	$sql_adult_filter = "AND `video_adult`='0'";
}

$page = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$view_type = isset($_GET['view_type']) ? $_GET['view_type'] : 'basic';

if ($view_type != 'basic') {
    $view_type = 'detailed';
}

$category = $_GET['category'];

$category_all = array(
    'recent',
    'viewed',
    'discussed',
    'favorites',
    'rated',
    'featured'
);

if (! in_array($category, $category_all)) {
    $category = 'recent';
}

$cache_id = 'video_' . $view_type . $category . $page;

if (isset($_GET['chid'])) {
    $cache_id .= '_channel_id' . $_GET['chid'];
    $channel_sql = "AND `video_channels` LIKE '%|" . (int) $_GET['chid'] . "|%'";
    $sql = "SELECT * FROM `channels` WHERE
           `channel_id`='" . (int) $_GET['chid'] . "'";
    $tmp = DB::fetch1($sql);
    $smarty->assign('channel_name', $tmp['channel_name']);
    $channel_name = $tmp['channel_name'] . ' ';
} else {
    $channel_sql = $channel_name = '';
}

$view = Cache::load($cache_id);

if (! $view) {
    $view = array();

    $view['view_type'] = $view_type;
    $view['category'] = $category;

    if ($category == 'featured') {
        $sql = "SELECT count(*) AS `total` FROM `videos` WHERE
               `video_type`='public' AND
               `video_featured`='yes' AND
               `video_active`='1' AND
               `video_approve`='1'
                $channel_sql
                $sql_adult_filter";
    } else {
        $sql = "SELECT count(*) AS `total` FROM `videos` WHERE
               `video_type`='public' AND
               `video_active`='1' AND
               `video_approve`='1'
                $channel_sql
                $sql_adult_filter";
    }

    $view['total'] = DB::getTotal($sql);

    $start_from = ($page - 1) * $config['num_watch_videos'];

    if ($category == 'recent') {
        $view['html_title'] = 'Most Recent ' . $channel_name . 'Videos';
        $view['display_order'] = 'Most Recent';
        $sql = "SELECT * FROM `videos` WHERE
               `video_type`='public' AND
               `video_active`='1' AND
               `video_approve`='1'
                $channel_sql
                $sql_adult_filter
                ORDER BY `video_add_time` DESC
                LIMIT $start_from, $config[num_watch_videos]";
    } else if ($category == 'viewed') {
        $view['html_title'] = 'Most Viewed ' . $channel_name . 'Videos';
        $view['display_order'] = 'Most Viewed';
        $sql = "SELECT * FROM `videos` WHERE
               `video_type`='public' AND
               `video_active`='1' AND
               `video_approve`='1'
                $channel_sql
                $sql_adult_filter
                ORDER BY `video_view_number` DESC
                LIMIT $start_from, $config[num_watch_videos]";
    } else if ($category == 'discussed') {
        $view['html_title'] = 'Most Discussed ' . $channel_name . 'Videos';
        $view['display_order'] = 'Most Discussed';
        $sql = "SELECT * FROM `videos` WHERE
               `video_type`='public' AND
               `video_active`='1' AND
               `video_approve`='1'
                $channel_sql
                $sql_adult_filter
                ORDER BY `video_com_num` DESC
                LIMIT $start_from, $config[num_watch_videos]";
    } else if ($category == 'favorites') {
        $view['html_title'] = 'Top Favorites ' . $channel_name . 'Videos';
        $view['display_order'] = 'Top Favorites';
        $sql = "SELECT * FROM `videos` WHERE
               `video_type`='public' AND
               `video_active`='1' AND
               `video_approve`='1'
                $channel_sql
                $sql_adult_filter
                ORDER BY `video_fav_num` DESC
                LIMIT $start_from,$config[num_watch_videos]";
    } else if ($category == 'rated') {
        $view['html_title'] = 'Top Rated ' . $channel_name . 'Videos';
        $view['display_order'] = 'Top Rated';
        $sql = "SELECT * FROM `videos` WHERE
               `video_type`='public' AND
               `video_active`='1' AND
               `video_approve`='1'
                $channel_sql
                $sql_adult_filter
                ORDER BY (video_rate/video_rated_by) DESC, video_rated_by DESC
                LIMIT $start_from, $config[num_watch_videos]";
    } else if ($category == 'featured') {
        $view['html_title'] = 'Featured ' . $channel_name . 'Videos';
        $view['display_order'] = 'Featured';
        $sql = "SELECT * FROM `videos` WHERE
               `video_type`='public' AND
               `video_active`='1' AND
               `video_approve`='1'
                $channel_sql AND
               `video_featured`='yes'
                $sql_adult_filter
                ORDER BY `video_add_time` DESC
                LIMIT $start_from, $config[num_watch_videos]";
    }

    $videos_all = DB::fetch($sql);
    $video_count = count($videos_all);

    if (!$video_count && $page > 1) {
        require '404.php';
        exit();
    }

    $videos = array();

    foreach ($videos_all AS $video) {
        $video['video_thumb_url'] = $servers[$video['video_thumb_server_id']];
        $videos[] = $video;
    }

    $view['start_num'] = $start_from + 1;
    $view['end_num'] = $start_from + $video_count;
    $view['page'] = $page;
    $view['page_links'] = Paginate::getLinks2($view['total'], $config['num_watch_videos'], './', $page);
    $view['videos'] = $videos;

    $channels = Channel::get();
    $view['channels'] = $channels;

    Cache::save($cache_id, $view);
}

$view['html_title'] = $view['html_title'] . ' - page ' . $page;

$smarty->assign('html_title', $view['html_title']);
$smarty->assign('html_keywords', $view['html_title']);
$smarty->assign('html_description', $view['html_title']);
$smarty->assign('view', $view);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('video.tpl');
$smarty->display('footer.tpl');
DB::close();
