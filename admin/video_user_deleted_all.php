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

$num_videos_per_refresh = 10;

$sql = "SELECT * FROM `videos` WHERE
	   `video_user_id`=0
	    ORDER BY `video_id` ASC
	    LIMIT $num_videos_per_refresh";
$deleted_videos_all = DB::fetch($sql);

if ($deleted_videos_all) {

    foreach ($deleted_videos_all as $deleted_video) {
        Video::delete($deleted_video['video_id'], $deleted_video['video_user_id'], 1);
    }

    echo "<script language=\"JavaScript\">
         <!--
         var randomnumber = Math.random() * 1000000;
         document.write('Files Deleting...');
         setTimeout('window.location.href = \"?x=' + randomnumber + '\"',2000);
         -->
         </script>";
} else {
    echo "All Files Deleted.";
}
