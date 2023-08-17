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
header('Content-type: text/html; charset=UTF-8');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . date('r'));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
$sql_adult_filter = '';

if (getFamilyFilter()) {
    $sql_adult_filter = "AND `video_adult`='0'";
}

$sql = "SELECT * FROM `videos` WHERE
       `video_type`='public' AND
       `video_active`='1' AND
       `video_approve`='1'
        $sql_adult_filter
        ORDER BY `video_view_time` DESC
        LIMIT 0, 5";
$videos = DB::fetch($sql);

echo '<freetubesite><video_list>';

foreach ($videos as $video) {
    $video_url = FREETUBESITE_URL . '/view/' . $video['video_id'] . '/' . $video['video_seo_name'] . '/';
    $thumb_url = $servers[$video['video_thumb_server_id']] . '/thumb/' . $video['video_folder'] . '1_' . $video['video_id'] . '.jpg';
    echo '<video>' .
         '<title>' . $video['video_title'] . '</title>' .
         '<run_time>' . $video['video_length'] . '</run_time>' .
         '<url>' . $video_url . '</url>' .
         '<thumbnail_url>' . $thumb_url . '</thumbnail_url>' .
         '</video>';
}

echo '</video_list></freetubesite>';

DB::close();
