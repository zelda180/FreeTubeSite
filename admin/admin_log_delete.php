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
if (isset($_POST['submit'])) {
    for ($i = 0; $i < count($_POST['delete_log']); $i ++) {
        $sql = "DELETE FROM `admin_log` WHERE
			   `admin_log_id`='" . (int) $_POST['delete_log'][$i] . "'";
        DB::query($sql);
    }
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if (isset($_GET['delete_all'])) {
    $sql = "DELETE FROM `admin_log`";
    DB::query($sql);
    $page = 1;
}

DB::close();

if ($page < 1) {
    $page = 1;
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$redirect_url = FREETUBESITE_URL . '/admin/admin_log.php?page=' . $page . '&sort=' . $sort;
Http::redirect($redirect_url);
