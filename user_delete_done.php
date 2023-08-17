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
require 'include/language/' . LANG . '/lang_user_delete_done.php';

if (isset($_GET['k']) && isset($_GET['i'])) {

    $data1 = "DELETE_USER";
    $sql = "SELECT * FROM `verify_code` WHERE
           `data1`='" . DB::quote($data1) . "' AND
           `vkey`='" . DB::quote($_GET['k']) . "' AND
           `id`='" . DB::quote($_GET['i']) . "'";
    $verify_info = DB::fetch1($sql);

    if ($verify_info) {

        $verify_id = $verify_info['id'];
        $verify_key = $verify_info['vkey'];
        $user_id = $verify_info['data2'];

        User::delete($user_id, 0); // 0 for soft del

        $sql = "DELETE FROM `verify_code` WHERE
               `id`='" . (int) $_GET['i'] . "'";
        DB::query($sql);

        if (isset($_SESSION['UID'])) {
            User::logout();
        }

        DB::close();
        set_message($lang['account_deleted'], 'success');
        $redirect_url = FREETUBESITE_URL . '/';
        Http::redirect($redirect_url);
    }
    else
    {
        $err = $lang['invalid_vkey'];
    }
}

$smarty->assign('msg', $msg);
$smarty->assign('err', $err);
$smarty->display('header.tpl');
$smarty->display('footer.tpl');
DB::close();
