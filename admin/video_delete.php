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
require '../include/language/' . LANG . '/admin/video_delete.php';

Admin::auth();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $video_id = $_GET['id'];
    $video_info = Video::getById($video_id);
    if (! $video_info) {
        $err = str_replace('[VIDEO_ID]', $video_id, $lang['video_not_found']);
    } else {
        $video_uid = $video_info['video_user_id'];
        if (Video::delete($video_id, $video_uid)) {
            $msg = $lang['video_deleted'];
        } else {
            $err = $tmp;
        }
    }
} else {
    $err = $lang['video_id_empty'];
}

$smarty->assign('msg', $msg);
$smarty->assign('err', $err);
$smarty->display('admin/header.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
