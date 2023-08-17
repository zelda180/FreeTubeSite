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
require '../include/language/' . LANG . '/admin/email_edit.php';

Admin::auth();

$sql = "SELECT * FROM `email_templates` WHERE
       `email_id`='" . DB::quote($_GET['email_id']) . "'";
$email = DB::fetch1($sql);
$email['email_body'] = htmlentities($email['email_body'], ENT_QUOTES, 'UTF-8');
$smarty->assign('email', $email);

if (isset($_POST['submit'])) {
    $sql = "UPDATE `email_templates` SET
           `email_subject`='" . DB::quote($_REQUEST['email_subject']) . "',
           `email_body`='" . DB::quote($_REQUEST['email_body']) . "',
           `comment`='" . DB::quote($_REQUEST['comment']) . "' WHERE
           `email_id`='" . DB::quote($_GET['email_id']) . "'";
    DB::query($sql);
    set_message($lang['email_updated'], 'success');
    $redirect_url = FREETUBESITE_URL . '/admin/email_templates.php';
    Http::redirect($redirect_url);
}

$smarty->assign('editor_wysiwyg_email', Config::get('editor_wysiwyg_email'));
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/email_edit.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
