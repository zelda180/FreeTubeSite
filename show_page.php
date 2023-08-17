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

require './include/config.php';

$sql = "SELECT * FROM `pages` WHERE
       `page_name`='" . DB::quote($_GET['name']) . "'";
$page_info = DB::fetch1($sql);

if (! $page_info) {
    require '404.php';
    exit;
}

if ($page_info['page_members_only'] == 1) {
    User::is_logged_in();
}

$smarty->assign('err', $err);
$smarty->assign('html_title', $page_info['page_title']);
$smarty->assign('content', $page_info['page_content']);
$smarty->assign('html_description', $page_info['page_description']);
$smarty->assign('html_keywords', $page_info['page_keywords']);
$smarty->display('header.tpl');
$smarty->display('show_page.tpl');
$smarty->display('footer.tpl');
DB::close();
