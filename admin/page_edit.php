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
    $sql = "UPDATE `pages` SET
           `page_content`='" . DB::quote($_POST['content']) . "',
           `page_title`='" . DB::quote($_POST['title']) . "',
           `page_description`='" . DB::quote($_POST['description']) . "',
           `page_keywords`='" . DB::quote($_POST['keywords']) . "',
           `page_members_only`='" . DB::quote($_POST['members_only']) . "' WHERE
           `page_id`='" . (int) $_POST['page_id'] . "'";
    DB::query($sql);
    $redirect_url = FREETUBESITE_URL . '/admin/page.php?name=' . $_POST['page_name'];
    Http::redirect($redirect_url);
}

$sql = "SELECT * FROM `pages` WHERE
       `page_id`='" . (int) $_GET['id'] . "'";
$page_edit = DB::fetch1($sql);

$page_edit['page_content'] = htmlspecialchars($page_edit['page_content'], ENT_QUOTES, 'UTF-8');

$smarty->assign('page_edit', $page_edit);
$smarty->assign('editor_wysiwyg_admin', Config::get('editor_wysiwyg_admin'));
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/page_edit.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
