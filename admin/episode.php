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

Admin::auth();

$video_id = isset($_GET['video_id']) ? (int) $_GET['video_id'] : 0;

$episode_video_info = EpisodeVideo::getByVideoId($video_id);
$err_top = '';

if ($episode_video_info) {
    $episode_name = Episode::getNameById($episode_video_info['ep_video_eid']);
    $err_top = 'Video exist in episode <strong>' . $episode_name . '</strong>. Please continue, if you want to add this video to another episode (previous will be lost).';
}

if (isset($_POST['submit'])) {
    $episode_new = $_POST['episode_new'];

    if ($episode_new == 'yes') {
        $episode_name = $_POST['episode_name'];

        if (Episode::exists($episode_name)) {
            $err = 'Episode name already exist.';
        } else {
            $episode_id = Episode::add($episode_name);
            $msg = 'New Episode created.';
        }
    } else {
        $episode_id = $_POST['episode_id'];
    }

    if ($err == '') {
        EpisodeVideo::add($episode_id, $video_id);
        $msg .= ' Video added to Episode <strong>' . Episode::getNameById($episode_id) . '</strong>.';
    }
}

$video_info = Video::getById($video_id);

if ($err == '') {
    $err = $err_top;
}
if ($msg != '') {
    $err = '';
}

$smarty->assign(array(
    'err' => $err,
    'msg' => $msg,
    'video_info' => $video_info,
));
$smarty->display('admin/header.tpl');
$smarty->display('admin/episode.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
