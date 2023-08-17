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
require '../include/language/' . LANG . '/admin/video_details.php';
Admin::auth();
$vid = (int) $_GET['id'];

if (is_numeric($vid)) {
    $video_info = Video::getById($vid);

    if ($video_info) {
        $player = new VideoPlayer();
        $smarty->assign('FREETUBESITE_PLAYER', $player->getPlayerCode($vid));
        $smarty->assign('video', $video_info);
        $smarty->assign('video_type', $video_info['video_vtype']);
        $sql = "SELECT * FROM `process_queue` WHERE
               `vid`='" . (int) $vid . "'";
        $process_queue_info = DB::fetch1($sql);

        if ($process_queue_info) {
            $source_video = FREETUBESITE_DIR . '/video/' . $video_info['video_name'];
            if (file_exists($source_video)) {
                $smarty->assign('reprocess', 1);
                $smarty->assign('reprocess_id', $process_queue_info['id']);
            }
        }
    }else{
        $err = str_replace('[VIDEO_ID]', $_GET['id'], $lang['video_not_found']);
    }
}

else{
    $err = $lang['video_id_empty'];
}

if (isset($_REQUEST['a'])) {
    $smarty->assign('a', $_REQUEST['a']);
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/video_details.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
