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

if (! is_numeric($_GET['id'])) {
    echo "id must be numeric";
    exit(0);
}

$video_id = isset($_GET['id']) ? $_GET['id'] : 0;

$video_info = Video::getById($video_id);

if (! $video_info || $video_info['video_active'] != 1 || $video_info['video_approve'] != 1) {
    $err = 1;
} else {
    $video_video_flv_name = $video_info['video_flv_name'];
    $player = new VideoPlayer();
    $freetubesite_player = $player->getPlayerCode($video_id);
}

$smarty->assign('err', $err);
$smarty->assign('FREETUBESITE_PLAYER', $freetubesite_player);
$smarty->display('show.tpl');
DB::close();
