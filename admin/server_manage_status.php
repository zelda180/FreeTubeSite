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

$server_id = isset($_GET['server_id']) ? $_GET['server_id'] : '';

$sql = "SELECT * FROM `servers` WHERE
       `id`='" . (int) $server_id . "'";
$server_info = DB::fetch1($sql);

if ($server_info) {
    if ($server_info['status'] == 1) {
        $new_status = 0;
        $enabled_or_disabled = 'Disabled';
    } else {
        $new_status = 1;
        $enabled_or_disabled = 'Enabled';
    }

    $sql = "UPDATE `servers` SET
           `status`='" . (int) $new_status . "' WHERE
           `id`='" . (int) $server_id . "'";
    DB::query($sql);

    $msg = 'Server ' . $server_info['url'] . ' ' . $enabled_or_disabled;
    set_message($msg, 'success');
} else {
    set_message('Invalid server id', 'error');
}

DB::close();
$redirect_url = FREETUBESITE_URL . '/admin/server_manage.php';
Http::redirect($redirect_url);
