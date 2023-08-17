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
require 'include/config.php';
require 'include/language/' . LANG . '/lang_email_unsubscribe.php';
$vkey = isset($_GET['vkey']) ? $_GET['vkey'] : 0;
$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';
$unsubscribed_success = 0;
$user_info = User::getByName($user_name);

if (! $user_info) {
    echo $lang['user_not_found'];
    exit;
}
$data1 = 'UNSUBSCRIBE_' . $user_info['user_id'];

$sql = "SELECT * FROM `verify_code` WHERE
	   `vkey`='" . DB::quote($vkey) . "' AND
	   `data1`='" . DB::quote($data1) . "'";
$verify_info = DB::fetch1($sql);

if (! $verify_info) {
    echo $lang['invalid_auth_key'];
    exit;
}

    if (isset($_POST['unsubscribe'])) {
    	$sql = "UPDATE `users` SET `user_subscribe_admin_mail`='0' WHERE
    		   `user_id`='" . $user_info['user_id'] . "'";
    	DB::query($sql);

    	$unsubscribe_txt = str_replace('[PRIVACY_SETTINGS_URL]', FREETUBESITE_URL . '/privacy/', $lang['unsubscribed_success']);
    	$unsubscribe_txt = str_replace('[SITE_NAME]', $config['site_name'], $unsubscribe_txt);
    	$smarty->assign('unsubscribe_txt',$unsubscribe_txt);
    	$unsubscribed_success = 1;
    } elseif (isset($_POST['cancel'])) {
        Http::redirect(FREETUBESITE_URL);
    }

$smarty->assign('unsubscribed_success', $unsubscribed_success);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('email_unsubscribe.tpl');
$smarty->display('footer.tpl');
DB::close();
