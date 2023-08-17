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

$advertisement_id = isset($_GET['adv_id']) ? $_GET['adv_id'] : 0;

$sql = "SELECT * FROM `adv` WHERE
       `adv_id`='" . (int) $advertisement_id . "'";
$advertisement_info = DB::fetch1($sql);

if (! $advertisement_info) {
    $redirect_url = FREETUBESITE_URL . '/admin/advertisements.php';
} else {
    if ($advertisement_info['adv_status'] == 'Active') {
        $new_adv_status = 'Inactive';
    } else {
        $new_adv_status = 'Active';
    }

    $sql = "UPDATE `adv` SET
           `adv_status`='" . $new_adv_status . "' WHERE
           `adv_id`='" . (int) $advertisement_id . "'";
    DB::query($sql);
    set_message('Advertisement status changed', 'success');
    $redirect_url = FREETUBESITE_URL . '/admin/advertisements.php';
}

DB::close();
Http::redirect($redirect_url);
