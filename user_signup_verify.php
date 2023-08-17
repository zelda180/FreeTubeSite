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
require 'include/language/' . LANG . '/lang_activate.php';

if (isset($_GET['k']) && isset($_GET['u']) && isset($_GET['i'])) {

    if (! is_numeric($_GET['u'])) {
        $err = $lang['invalid_vcode'];
    } else if (! is_numeric($_GET['i'])) {
        $err = $lang['invalid_vcode'];
    } else if (strlen($_GET['k']) > 40) {
        $err = $lang['invalid_vcode'];
    } else {
        $data1 = 'SIGNUP' . $_GET['u'];
        $sql = "SELECT * FROM `verify_code` WHERE
               `id`=" . (int) $_GET['i'] . " AND
               `vkey`='" . DB::quote($_GET['k']) . "' AND
               `data1`='" . DB::quote($data1) . "'";
        $verify_info = DB::fetch1($sql);

        if ($verify_info) {
            $sql = "UPDATE `users` SET
                   `user_email_verified`='yes',
                   `user_account_status`='active' WHERE
                   `user_id`='" . (int) $_GET['u'] . "'";
            DB::query($sql);

            $sql = "DELETE FROM `verify_code` WHERE
                   `id`='" . (int) $_GET['i'] . "'";
            DB::query($sql);

            $user_info = User::getById($_GET['u']);
            User::login($user_info['user_name']);

            $redirect_url = FREETUBESITE_URL . '/friends/invite/?welcome=1';
            Http::redirect($redirect_url);

        } else {
            $err = $lang['invalid_vcode'];
        }
    }

} else {
    $err = $lang['invalid_vcode'];
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('footer.tpl');
DB::close();
