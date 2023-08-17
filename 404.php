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

require_once 'include/config.php';

header($_SERVER["SERVER_PROTOCOL"].' 404 Not Found');

$sql = "SELECT * FROM `videos` WHERE
       `video_featured`='yes' AND
       `video_type`='public' AND
       `video_approve`='1' AND
       `video_active`='1'
       LIMIT 0,4";
$videos = DB::fetch($sql);

if (!$videos) {
    $sql = "SELECT * FROM `videos` WHERE
           `video_type`='public' AND
           `video_approve`='1' AND
           `video_active`='1'
           LIMIT 0,4";
    $videos = DB::fetch($sql);
}

$video_info = array();

foreach($videos as $video) {
    $video['video_thumb_url'] = $servers[$video['video_thumb_server_id']];
    $video_info[] = $video;
}

$smarty->assign('video_info', $video_info);
$smarty->assign('msg_404', 'We\'re sorry, the page you requested cannot be found.');
$smarty->assign('html_title', '404 Not Found');
$smarty->display('header.tpl');
$smarty->display('404.tpl');
$smarty->display('footer.tpl');
DB::close();
