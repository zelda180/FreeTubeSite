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
require '../include/language/' . LANG . '/admin/convert.php';
Admin::auth();
$qid = $_GET['id'];

$smarty->display('admin/header.tpl');

if (! is_numeric($qid)) {
    echo $lang['id_not_numeric'];
    exit();
}
$re_convert = isset($_GET['reconvert']) ? $_GET['reconvert'] : 0;
if ($re_convert == 1) {
    $sql = "UPDATE `process_queue` SET
           `status`='8' WHERE
           `id`='" . (int) $qid . "'";
    DB::query($sql);
}

ob_start();
$video_id = Upload::processVideo($qid, 1);
$debug_log = ob_get_contents();
ob_end_clean();

DB::close();

echo $debug_log;
$url = FREETUBESITE_URL . '/admin/process_queue.php';
echo "<p><a href=\"$url\">Back To Admin Process Queue</a></p>";
	$smarty->display('admin/footer.tpl');
