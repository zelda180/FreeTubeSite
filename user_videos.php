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
require 'include/language/' . LANG . '/lang_user_videos.php';

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'public';

$allowed_types = array(
    'public',
    'private'
);

if (! in_array($type, $allowed_types)) {
    $type = 'public';
}

$user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : '';

if ($user_name == '') {
    set_message($lang['user_not_found'], 'error');
    $redirect_url = FREETUBESITE_URL . '/';
}

$user_info = User::getByName($user_name);

if (! $user_info) {
    set_message($lang['user_not_found'], 'error');
    $redirect_url = FREETUBESITE_URL . '/';
    Http::redirect($redirect_url);
}

if (isset($_POST['remove_video']) && is_numeric($_POST['VID'])) {
    $sql = "SELECT * FROM `videos` WHERE
           `video_id`='" . (int) $_POST['VID'] . "' AND
           `video_user_id`='" . (int) $_SESSION['UID'] . "'";
    $user_videos = DB::fetch($sql);
    $total = count($user_videos);

    if ($total == 1) {
        $msg = Video::delete($_POST['VID'], $_SESSION['UID'], 0);
        set_message($lang['video_deleted'], 'success');
        $redirect_url = FREETUBESITE_URL . '/' . $_SESSION['USERNAME'] . '/' . $type . '/';
        Http::redirect($redirect_url);
    }
}

$show_video = 0;

if ($type == 'private') {
    if (isset($_SESSION['USERNAME'])) {
        if ($user_name == $_SESSION['USERNAME']) {
            $show_video = 1;
        } else {
            $sql = "SELECT COUNT(*) AS `total` FROM `friends` WHERE
                   `friend_user_id`='" . (int) $_SESSION['UID'] . "' AND
                   `friend_name`='" . DB::quote($user_name) . "' AND
                   `friend_status`='Confirmed'";
            $is_friend = DB::getTotal($sql);

            if ($is_friend < 1) {
                $msg = $lang['friends_only'];
            } else {
                $show_video = 1;
            }
        }
    } else {
        $msg = $lang['friends_only'];
    }
} else {
    $show_video = 1;
}

if ($show_video) {

    $sql = "SELECT COUNT(*) AS `total` FROM `videos` WHERE
           `video_user_id`='" . (int) $user_info['user_id'] . "' AND
           `video_type`='" . DB::quote($type) . "' AND
           `video_active`='1' AND
           `video_approve`='1'";
    $total = DB::getTotal($sql);
    $smarty->assign('total', $total);

    $start_from = ($page - 1) * $config['items_per_page'];

    $sql = "SELECT * FROM `videos` WHERE
           `video_user_id`='" . (int) $user_info['user_id'] . "' AND
           `video_type`='" . DB::quote($type) . "' AND
           `video_active`='1' AND
           `video_approve`='1'
            ORDER BY `video_id` DESC
            LIMIT $start_from, $config[items_per_page]";
    $user_videos_all = DB::fetch($sql);
    $videos = array();
    $video_keywords_all = '';

    foreach ($user_videos_all as $user_video) {
        $user_video['video_thumb_url'] = $servers[$user_video['video_thumb_server_id']];
        $user_video['video_keywords_array'] = explode(' ', $user_video['video_keywords']);
        $video_keywords_all .= $user_video['video_keywords'] . ' ';
        $videos[] = $user_video;
    }

    $view = array();
    $video_keywords_array_all = explode(' ', $video_keywords_all);
    $view['video_keywords_array_all'] = array_remove_duplicate($video_keywords_array_all);
    $view['videos'] = $videos;

    $start_num = $start_from + 1;
    $end_num = $start_from + count($videos);
    $smarty->assign('start_num', $start_num);
    $smarty->assign('end_num', $end_num);

    $page_links = Paginate::getLinks2($total, $config['items_per_page'], './', $page);
    $smarty->assign('page_links', $page_links);
    $smarty->assign('view', $view);
}

$html_title = $user_info['user_name'] . '\'s videos page ' . $page;

$smarty->assign(array(
    'html_title' => $html_title,
    'html_description' => $html_title,
    'html_keywords' => $html_title
));

$allow_playlist = $user_info['user_playlist_public'];
$allow_favorite = $user_info['user_favourite_public'];

if (isset($_SESSION['UID'])) {
    if ($_SESSION['UID'] == $user_info['user_id']) {
        $allow_playlist = $allow_favorite = 1;
    }
}

$smarty->assign('allow_playlist', $allow_playlist);
$smarty->assign('allow_favorite', $allow_favorite);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('page', $page);
$smarty->assign('type', $type);
$smarty->assign('user_info', $user_info);
$smarty->assign('sub_menu', 'menu_user.tpl');
$smarty->display('header.tpl');
if ($show_video) $smarty->display('user_videos.tpl');
$smarty->display('footer.tpl');
DB::close();
