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
$friends = array();
$friend_videos = array();

if ($page < 1) {
    $page = 1;
}

$sql = "SELECT `friend_friend_id` FROM `friends` WHERE
       `friend_user_id`='" . (int) $_SESSION['UID'] . "' AND
       `friend_status`='Confirmed'";
$friends_all = DB::fetch($sql);

foreach ($friends_all as $friend) {
    $friends[] = $friend['friend_friend_id'];
}

$num_friends = count($friends);

if ($num_friends) {

    if ($friends[0] != '') {
        $my_friends = implode(',', $friends);
    }

    $sql = "SELECT count(*) AS `total` FROM
           `videos` AS v,
           `favourite` AS f WHERE
            f.favourite_user_id in (" . DB::quote($my_friends) . ") AND
            f.favourite_video_id=v.video_id";
    $total = DB::getTotal($sql);

    $start_from = ($page - 1) * $config['items_per_page'];

    if ($my_friends != '') {
        $sql = "SELECT * FROM
               `videos` AS v,
               `favourite` AS f WHERE
                f.favourite_user_id in (" . DB::quote($my_friends) . ") AND
                f.favourite_video_id=v.video_id
                GROUP BY f.favourite_video_id
                ORDER BY v.video_add_time DESC
                LIMIT $start_from, $config[items_per_page]";
        $friends_fav_all = DB::fetch($sql);
        $favorite_video_id = array();

        if ($friends_fav_all) {
            foreach ($friends_fav_all as $friends_fav) {
                $friends_fav['video_thumb_url'] = $servers[$friends_fav['video_thumb_server_id']];
                $friends_fav['video_keywords_array'] = preg_split('/\s+/', $friends_fav['video_keywords']);
                $friend_videos[] = $friends_fav;
                $favorite_video_id[] = $friends_fav['video_id'];
            }
        }
        $start_num = $start_from + 1;
        $end_num = $start_from + count($friend_videos);
    }

    $page_links = Paginate::getLinks2($total, $config['items_per_page'], './', $page);
    $smarty->assign('page_links', $page_links);

    /*
     * find favorited user name
     */

    $favorited_by = array();

    for ($i = 0; $i < count($favorite_video_id); $i ++) {
        $sql = "SELECT f.*,u.user_name FROM
               `favourite` AS f,
               `users` AS u WHERE
                f.favourite_video_id=" . (int) $favorite_video_id[$i] . " AND
                f.favourite_user_id=u.user_id";
        $result = DB::fetch($sql);

        foreach ($result as $tmp) {
            if (array_key_exists($tmp['favourite_video_id'], $favorited_by)) {
                $favorited_by[$tmp['favourite_video_id']] .= ' <a href=' . FREETUBESITE_URL . '/' . $tmp['user_name'] . '>' . $tmp['user_name'] . '</a>';
            } else {
                $favorited_by[$tmp['favourite_video_id']] = '<a href=' . FREETUBESITE_URL . '/' . $tmp['user_name'] . '>' . $tmp['user_name'] . '</a>';
            }
        }
    }

    $smarty->assign('favorited_by', $favorited_by);

    $_REQUEST['UID'] = isset($_REQUEST['UID']) ? $_REQUEST['UID'] : '';
    $_REQUEST['type'] = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
}

if (count($friend_videos) > 0) {
    $smarty->assign('start_num', $start_num);
    $smarty->assign('end_num', $end_num);
    $smarty->assign('total', $total);
    $smarty->assign('answers', $friend_videos);
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('page', $page);
$smarty->assign('sub_menu', 'menu_friends.tpl');
$smarty->display('header.tpl');
$smarty->display('user_friends_favourites.tpl');
$smarty->display('footer.tpl');
DB::close();
