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
require 'include/language/' . LANG . '/lang_tag.php';

$search_string = strip_tags($_GET['search_string']);
$search_string = trim($search_string);

if ($search_string == '') {
    $err = $lang['search_key_empty'];
}

if (isset($_GET['sort'])) {
    $allowed_sort = array(
        'adddate',
        'viewnum',
        'rate'
    );

    if (in_array($_GET['sort'], $allowed_sort)) {
        $_SESSION['tag_sort_order'] = $_GET['sort'];
    }
}

$sort = isset($_SESSION['tag_sort_order']) ? $_SESSION['tag_sort_order'] : '';
$sort_html = '';

switch ($sort) {

    case 'viewnum':
        $sortby = "ORDER BY `video_view_number` DESC";
        $sort_html = 'Most Viewed';
        break;
    case 'rate':
        $sortby = "ORDER BY (`video_rated_by`*`video_rate`) DESC";
        $sort_html = 'Most Rated';
        break;
    default:
        $sortby = "ORDER BY `video_add_time` DESC";
        $sort_html = 'Most Recent';
        break;
}

if ($err == '') {

    $sql = "SELECT tv.vid FROM
           `tags` AS `t`,`tag_video` AS `tv` WHERE
            t.tag='" . DB::quote($search_string) . "' AND
            t.id=tv.tag_id";
    $videos_array = DB::fetch($sql);

    $tag_videos = array();

    foreach($videos_array as $video_x) {
        $tag_videos[] = $video_x['vid'];
    }

    $total = count($tag_videos);

    if ($total > 0) {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        if ($page < 1) {
            $page = 1;
        }

        if ($page > ceil($total / $config['items_per_page'])) {
            require '404.php';
            exit();
        }

        $tag_videos = array_chunk($tag_videos, $config['items_per_page']);
        $tag_videos = $tag_videos[$page - 1];
        $tag_videos_text = implode(',', $tag_videos);

        $sql = "SELECT `video_id`,`video_user_id`,`video_title`,`video_seo_name`,`video_description`,
               `video_keywords`,`video_channels`,`video_length`,`video_view_number`,`video_com_num`,
               `video_rated_by`,`video_rate`,`video_thumb_server_id`,`video_folder`,`video_add_time` FROM `videos` WHERE
               `video_id` IN (" . $tag_videos_text . ") AND
               `video_type`='public' AND
               `video_active`='1' AND
               `video_approve`='1'
                $sortby";
        $videos = DB::fetch($sql);

        foreach ($videos AS $video) {
            $video['video_thumb_url'] = $servers[$video['video_thumb_server_id']];
            $video_info[] = $video;
            $video_users[] = $video['video_user_id'];
        }

        $total_current_page = count($videos);

        $start_from = ($page - 1) * $config['items_per_page'];
        $start_num = $start_from + 1;
        $end_num = $start_from + $total_current_page;
        $page_links = Paginate::getLinks2($total, $config['items_per_page'], './', $page);

        //Video users
        $video_users = array_unique($video_users);
        $video_users_text = implode(', ', $video_users);

        $sql = "SELECT `user_id`,`user_name` FROM `users`WHERE
               `user_id` IN(" . $video_users_text . ")";
        $result = DB::fetch($sql);
        $user_names = array();

        foreach($result as $user) {
            $user_id = $user['user_id'];
            $user_name = $user['user_name'];
            $user_names[$user_id] = $user_name;
        }

        $smarty->assign(array(
            'page' => $page,
            'start_num' => $start_num,
            'end_num' => $end_num,
            'page_links' => $page_links,
            'total' => $total,
            'video_info' => $video_info,
            'user_names' => $user_names
        ));
    } else {
        require '404.php';
        exit();
    }
}

$html_title = $sort_html . ' ' . $search_string . ' Videos - page ' . $page;
$search_string = str_replace('+', ' ', $search_string);

$smarty->assign(array(
    'search_string' => $search_string,
    'html_title' => $html_title,
    'html_keywords' => $html_title,
    'html_description' => $html_title,
    'err' => $err,
    'msg' => $msg,
));
$smarty->display('header.tpl');
$smarty->display('tag.tpl');
$smarty->display('footer.tpl');
DB::close();
