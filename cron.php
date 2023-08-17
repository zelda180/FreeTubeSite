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
$current_folder = dirname(__FILE__);
chdir("$current_folder");
require $current_folder . '/include/config.php';

$sql = "SELECT * FROM `process_queue` WHERE
       `status`='0'";
$download = DB::fetch1($sql);

$sql = "SELECT * FROM `process_queue` WHERE
       `status`='2'";
$process = DB::fetch1($sql);
$cron = Config::get('cron');
echo 'cronjob started<br />';

if ($cron == 1) {
    $cron = 0;

    if ($download) {
        $video_id = $download['id'];
        Upload::downloadVideo($video_id);
    } else if ($process) {
        $video_id = $process['id'];
        Upload::processVideo($video_id);
    }
} else {
    $cron = 1;

    if ($process) {
        $video_id = $process['id'];
        Upload::processVideo($video_id);
    } else if ($download) {
        $video_id = $download['id'];
        Upload::downloadVideo($video_id);
    }
}

$sql = "UPDATE `config` SET
       `config_value`='" . (int) $cron . "' WHERE
       `config_name`='cron'";
DB::query($sql);
DB::close();
