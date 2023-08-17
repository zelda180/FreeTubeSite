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
require '../include/language/' . LANG . '/admin/process_status_edit.php';

Admin::auth();

$err = '';

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

if (isset($_POST['submit'])) {
    $sql = "UPDATE `process_queue` SET
           `status`='" . (int) $_POST['status'] . "' WHERE
           `id`='" . (int) $_POST['id'] . "'";
    DB::query($sql);
    set_message($lang['process_status_updated'], 'success');
    $redirect_url = FREETUBESITE_URL . '/admin/process_queue.php?page=' . $page;
    Http::redirect($redirect_url);
}

if (isset($_GET['id'])) {
    $sql = "SELECT * FROM `process_queue` WHERE
          `id`='" . (int) $_GET['id'] . "'";
    $video_info = DB::fetch1($sql);
    $smarty->assign('video_info', $video_info);
}

$smarty->assign('msg', $msg);
$smarty->assign('page', $page);
$smarty->display('admin/header.tpl');
$smarty->display('admin/process_status_edit.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
