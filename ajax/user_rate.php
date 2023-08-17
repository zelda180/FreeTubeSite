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
<?php
require '../include/config.php';
require '../include/language/' . LANG . '/lang_user_rate.php';
$voter = isset($_POST['voter']) ? $_POST['voter'] : '';
$candidate = isset($_POST['candidate']) ? $_POST['candidate'] : '';
$rate = isset($_POST['rate']) ? $_POST['rate'] : '';

if (! is_numeric($voter) || ! is_numeric($candidate) || ! is_numeric($rate)) {
    if ($config['debug']) error_log("Hacking attempt \n", 3, FREETUBESITE_DIR . '/templates_c/log_ajax_user_rate.txt');
    Ajax::returnJson('Hacking attempt', 'error');
    exit(0);
}

$sql = "SELECT count(*) AS `total` FROM `uservote` WHERE
       `candate_id`=" . (int) $candidate . " AND
       `voter_id`=" . (int) $voter;
$user_already_voted = DB::getTotal($sql);

if (! $user_already_voted) {
    $sql = "INSERT INTO `uservote` SET
           `candate_id`='" . (int) $candidate . "',
           `voter_id`='" . (int) $voter . "',
           `vote`='" . (int) $rate . "'";
    DB::query($sql);
    if ($config['debug']) error_log("$lang[rated] \n", 3, FREETUBESITE_DIR . '/templates_c/log_ajax_user_rate.txt');
    Ajax::returnJson($lang['rated'], 'success');
} else {
    if ($config['debug']) error_log("$lang[already_rated] \n", 3, FREETUBESITE_DIR . '/templates_c/log_ajax_user_rate.txt');
    Ajax::returnJson($lang['already_rated'], 'success');
}
