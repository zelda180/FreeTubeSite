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
require '../include/config.php';
require '../include/language/' . LANG . '/admin/convert.php';
Admin::auth_no_log();
$vid = $_GET['id'];

if (! is_numeric($vid)) {
    echo $lang['id_not_numeric'];
    exit();
}
$ffmpeg_output = @file_get_contents('../flvideo/ffmpeg_output/'. $vid . '.txt');
if ($ffmpeg_output) {
require FREETUBESITE_DIR . '/include/ffmpeg_output.php';

	echo "<b>Video Total Time (in seconds):</b> " . $duration . "<br>";
    echo "<b>Current Encoding Time (in seconds):</b> " . $encode_at_time . "<br>";
    echo "<b>Video Encoding Progress:</b> " . $progress . "%<br><br>";

if ($progress == 100) {
	echo "<script>";
	echo "stopRefresh();";
	echo "</script>";
	echo "<b>If Page Dose Not continue <a href=\"./convert.php?id=$vid\">Just Click Here To Finish The Video Conversion.</a></b>";
        $sql = "UPDATE `process_queue` SET
           `status`='7' WHERE
           `id`='$vid'";
        DB::query($sql);
	echo "<meta http-equiv=\"Refresh\" content=\"0; url=./convert.php?id=$vid\" />";
	$smarty->assign('vid', $vid);
}}
