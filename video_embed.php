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

$video_id = isset($_GET['video_id']) ? (int) $_GET['video_id'] : 0;

if ($video_id == 0) {
    echo "invalid video id";
    exit();
}

$video_info = Video::getById($video_id);

$video_thumb_url = $servers[$video_info['video_thumb_server_id']];
$logo = $config['watermark_image_url'];
$image = $video_thumb_url . '/thumb/' . $video_info['video_folder'] . '/' . $video_id . '.jpg';
$freetubesite_player = Config::get('freetubesite_player');

if ($freetubesite_player == 'StrobeMediaPlayback') {
    $file_url = 'src=' . FREETUBESITE_URL . '/flvideo/' . $video_info['video_folder'] . $video_info['video_flv_name'];
    $video_flv_player = FREETUBESITE_URL . '/player/player_adobe.swf?';
    $poster = '&poster=' . $image;
    $video_flv_player .= $file_url . $poster;
} else {
    $file_url = 'file=' . FREETUBESITE_URL . '/xml_playlist.php?id=' . $video_info['video_id'];
    $video_flv_player = FREETUBESITE_URL . '/player/player.swf?';
    $video_flv_player .= $file_url;
    $video_flv_player .= '&logo=' . $logo;
}

$sql = "UPDATE `videos` SET `video_view_number`=`video_view_number`+1 WHERE `video_id`=$video_id";
$result = DB::query($sql);

header('Content-Type: video/flv');
Header("Location: $video_flv_player");
