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
require '../include/language/' . LANG . '/admin/page_add.php';

Admin::auth();

if (isset($_POST['submit'])) {
    $name_old = $_POST['page_name'];
    $name = trim($name_old);
    $name = strtolower($name);
    $name = preg_replace('/[^a-z0-9]/', "-", $name);

    if ($name_old != $name) {
        $err = $lang['invalid_page_name'];
        $err = str_replace('[NAME]', $name, $err);
        $err = str_replace('[NAME_OLD]', $name_old, $err);
    } else if (strlen($_POST['page_name']) < 3) {
        $err = $lang['name_too_short'];
    } else if (strlen($_POST['title']) < 6) {
        $err = $lang['title_too_short'];
    } else if (strlen($_POST['content']) < 20) {
        $err = $lang['content_too_short'];
    } else if (strlen($_POST['description']) < 6) {
        $err = $lang['description_too_short'];
    } else if (strlen($_POST['keywords']) < 6) {
        $err = $lang['keyword_too_short'];
    }

    if ($err == '') {
        $sql = "SELECT * FROM `pages` WHERE
               `page_name`='" . DB::quote($name) . "'";
        $page = DB::fetch1($sql);
        if ($page) {
            $err = $lang['duplicate_name'];
        }
    }

    if ($err == '') {
        $sql = "INSERT INTO `pages` SET
               `page_name`='" . DB::quote($name) . "',
               `page_title`='" . DB::quote($_POST['title']) . "',
               `page_keywords`='" . DB::quote($_POST['keywords']) . "',
               `page_description`='" . DB::quote($_POST['description']) . "',
               `page_content`='" . DB::quote($_POST['content']) . "',
               `page_counter`='1',
               `page_members_only`='" . DB::quote($_POST['members_only']) . "'";
        DB::query($sql);
        set_message($lang['page_created'], 'success');
        $redirect_url = FREETUBESITE_URL . '/admin/page.php?name=' . $name;
        Http::redirect($redirect_url);
    }
    $smarty->assign('name', $name);
}

$smarty->assign('editor_wysiwyg_admin', Config::get('editor_wysiwyg_admin'));
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/page_add.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
