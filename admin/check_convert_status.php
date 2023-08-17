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
$vid = $_GET['id'];
$smarty->assign('vid', $vid);
# Check If The ID Is A Number
if (! is_numeric($vid)) {
    $id_not_numeric = 1;
    $smarty->assign('id_not_numeric', $id_not_numeric);
}
# Check If The convert output file from FFMPEG exists
 if (!file_exists(FREETUBESITE_DIR . '/flvideo/ffmpeg_output/'. $vid . '.txt')) {
	$vid_file_exists = 0;
    $smarty->assign('vid_file_exists', $vid_file_exists);
}

$ffmpeg_output = @file_get_contents('../flvideo/ffmpeg_output/'. $vid . '.txt');
if ($ffmpeg_output) {
require FREETUBESITE_DIR . '/include/ffmpeg_output.php';
$progresst = 1;
}
	$smarty->assign('vid', $vid);
	$smarty->assign('progresst', $progresst);
	$smarty->display('admin/header.tpl'); 
	$smarty->display('admin/check_convert_status.tpl');
	$smarty->display('admin/footer.tpl');
