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

if ($config['allow_download'] != 1) {
    echo 'Video download disabled';
    exit();
}

User::is_logged_in();
$video_info = Video::getById($_GET['video_id']);
$video_name = $video_info['video_name'];
$file_path = FREETUBESITE_DIR . '/video/' . $video_name;

if (($video_name == '') || (! is_file($file_path)) || (! file_exists($file_path))) {
    echo 'File not found.';
    exit();
}

if (ini_get('zlib.output_compression')) {
    ini_set('zlib.output_compression', 'Off');
}

header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($video_name) . '";');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file_path));
readfile("$file_path");
exit();
