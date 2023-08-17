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
require 'include/language/' . LANG . '/lang_add_video.php';

User::is_logged_in();

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1)
{
    $page = 1;
}

$group_url = isset($_GET['group_url']) ? trim($_GET['group_url']) : '';

if (empty($group_url))
{
    Http::redirect(FREETUBESITE_URL);
}

$sql = "SELECT * FROM `groups` WHERE
       `group_url`='" . DB::quote($group_url) . "'";
$group_info = DB::fetch1($sql);

if (! $group_info)
{
    Http::redirect(FREETUBESITE_URL);
}

$smarty->assign('group_info', $group_info);

if (isset($_POST['add_video']))
{
    $sql = "SELECT * FROM `group_members` WHERE
           `group_member_group_id`='" . (int) $group_info['group_id'] . "' AND
           `group_member_user_id`='" . (int) $_SESSION['UID'] . "'";
    $group_member = DB::fetch1($sql);

    if ($group_member)
    {
        $approved = 'no';

        if ($_SESSION['UID'] == $group_info['group_owner_id'])
        {
            $approved = 'yes';
        }

        if ($group_info['group_upload'] == 'immediate')
        {
            $approved = 'yes';
        }

        $sql = "INSERT INTO `group_videos` SET
               `group_video_group_id`='" . (int) $group_info['group_id'] . "',
               `group_video_video_id`='" . (int) $_POST['video_id'] . "',
               `group_video_member_id`='" . (int) $_SESSION['UID'] . "',
               `group_video_approved`='" . DB::quote($approved) . "'";
        DB::query($sql);

        if ($approved == 'no')
        {
            $msg = $lang['group_video_approve'];
        }
        else
        {
            $msg = $lang['group_video_added'];
        }

        set_message($msg, 'success');
        $redirect_url = FREETUBESITE_URL . '/group/' . $group_url . '/add/' . $page;
        Http::redirect($redirect_url);
    }
    else
    {
        set_message($lang['group_not_member'], 'error');
        $redirect_url = FREETUBESITE_URL . '/group/' . $group_url . '/add/' . $page;
        Http::redirect($redirect_url);
    }
}

$sql = "SELECT COUNT(*) AS `total` FROM `videos` WHERE
       `video_user_id`='" . (int) $_SESSION['UID'] . "' AND
       `video_approve`='1' AND
       `video_active`='1'";
$total = DB::getTotal($sql);

$start_from = ($page - 1) * $config['items_per_page'];

$sql = "SELECT * FROM `videos` WHERE
       `video_user_id`=" . (int) $_SESSION['UID'] . " AND
       `video_approve`='1' AND
       `video_active`='1'
        ORDER BY `video_add_time` DESC
        LIMIT $start_from, $config[items_per_page]";
$user_videos = DB::fetch($sql);
$num_result = count($user_videos);

if ($num_result > 0)
{
    $group_add_video_keywords_all = '';

    foreach ($user_videos as $video_row)
    {
        $sql = "SELECT * FROM `group_videos` WHERE
               `group_video_group_id`='" . (int) $group_info['group_id'] . "' AND
               `group_video_video_id`='" . (int) $video_row['video_id'] . "'";
        $tmp = DB::fetch1($sql);

        if (! $tmp)
        {
            $video_row['in_group'] = 0;
        }
        else
        {
            $video_row['in_group'] = 1;
        }

        $video_row['video_thumb_url'] = $servers[$video_row['video_thumb_server_id']];
        $video_row['video_keywords_array'] = explode(' ', $video_row['video_keywords']);
        $group_add_video_keywords_all .= $video_row['video_keywords'] . ' ';
        $videos[] = $video_row;
    }

    $group_video_keywords_array = explode(' ', $group_add_video_keywords_all);
    $group_video_keywords_array_new = array_remove_duplicate($group_video_keywords_array);

    $view = array();
    $view['group_add_video_keywords_array'] = $group_video_keywords_array_new;
    $smarty->assign('view', $view);
}
else
{
    $msg = $lang['no_videos'];
}

$start_num = $start_from + 1;
$end_num = $start_from + $num_result;

$page_links = Paginate::getLinks2($total, $config['items_per_page'], './', $page);

$smarty->assign('page', $page);
$smarty->assign('start_num', $start_num);
$smarty->assign('end_num', $end_num);
$smarty->assign('page_links', $page_links);
$smarty->assign('total', $total);

if (isset($videos))
{
    $smarty->assign('videos', $videos);
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('sub_menu', 'menu_add_video.tpl');
$smarty->display('header.tpl');
$smarty->display('group_add_videos.tpl');
$smarty->display('footer.tpl');
DB::close();
