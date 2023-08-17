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
require '../include/language/' . LANG . '/admin/server_manage_delete.php';

Admin::auth();

$serverId = isset($_GET['id']) ? $_GET['id'] : 0;

$sql = "SELECT * FROM `servers` WHERE
        `id`='" . (int) $serverId . "'";
$server_info = DB::fetch1($sql);

if (! $server_info) {
    $err = $lang['server_not_found'];
    set_message($err, 'error');
}

if ($err == '') {
    if ($server_info['server_type'] == 1) {
        $sql = "SELECT count(*) AS `total` FROM `videos` WHERE
    		   `video_thumb_server_id`='" . (int) $serverId . "'";
    } else {
        $sql = "SELECT count(*) AS `total` FROM `videos` WHERE
    		   `video_server_id`='" . (int) $serverId . "'";
    }

    $total_medias = DB::getTotal($sql);

    if ($total_medias == 0) {
        $sql = "DELETE FROM `servers` WHERE
                `id`='" . (int) $serverId . "'";
        DB::query($sql);
        $msg = $lang['server_deleted'];
    } else {
        if ($server_info['server_type'] == 1) {
            $msg = $lang['cannot_delete_thumb_server'];
        } else {
            $msg = $lang['cannot_delete_video_server'];
        }
    }
    set_message($msg, 'success');
}

DB::close();
$redirect_url = FREETUBESITE_URL . '/admin/server_manage.php';
Http::redirect($redirect_url);
