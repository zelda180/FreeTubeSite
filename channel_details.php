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

if (! is_numeric($_GET['id']) || $_GET['id'] < 1)
{
    $redirect_url = FREETUBESITE_URL . '/';
    Http::redirect($redirect_url);
}

$channel = Channel::getById($_GET['id']);

if ($channel) {

    $num_channel_videos = Config::get('num_channel_video');

    $sql = "SELECT count(video_id) AS `total`, `video_user_id` FROM `videos` WHERE
           `video_channels` LIKE '%|" . (int) $_GET['id'] . "|%' AND
           `video_approve`='1' AND
           `video_active`='1' AND
           `video_type`='public' AND
           `video_user_id` > '0'
            GROUP BY `video_user_id`
            ORDER BY `total` DESC
            LIMIT 5";
    $most_active_users = DB::fetch($sql);
    $smarty->assign('most_active_users', $most_active_users);

    $sql = "SELECT * FROM `videos` WHERE
           `video_channels` LIKE '%|" . (int) $_GET['id'] . "|%' AND
           `video_approve`='1' AND
           `video_active`='1' AND
           `video_type`='public'
            ORDER BY `video_add_time` DESC
            LIMIT $num_channel_videos";
    $recent_channel_videos_all = DB::fetch($sql);

    $recent_channel_videos = array();

    foreach ($recent_channel_videos_all AS $recent_channel_video) {
        $recent_channel_video['video_thumb_url'] = $servers[$recent_channel_video['video_thumb_server_id']];
        $recent_channel_videos[] = $recent_channel_video;
    }

    $smarty->assign('recent_channel_videos', $recent_channel_videos);

    # find popular videos on channel

    $sql = "SELECT * FROM `videos` WHERE
           `video_channels` LIKE '%|" . (int) $_GET['id'] . "|%' AND
           `video_approve`='1' AND
           `video_active`='1' AND
           `video_type`='public'
            ORDER BY `video_view_number` DESC
            LIMIT $num_channel_videos";
    $channel_videos_all = DB::fetch($sql);
    $total = count($recent_channel_videos_all);

    $mostview = array();

    foreach ($channel_videos_all as $video)
    {
        $video['video_thumb_url'] = $servers[$video['video_thumb_server_id']];
        $mostview[] = $video;
    }

    $smarty->assign('mostview', $mostview);
    $smarty->assign('total', $total);
    $smarty->assign('channel', $channel);
}

$channels = Channel::get();
$smarty->assign('channels', $channels);
$smarty->assign('html_title', $channel['channel_name']);
$smarty->assign('html_keywords', $channel['channel_name']);
$smarty->assign('html_description', $channel['channel_description']);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('channel_details.tpl');
$smarty->display('footer.tpl');
DB::close();
