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
require 'include/language/' . LANG . '/lang_password_change.php';
User::is_logged_in();
$user_info = User::get_user_by_id($_SESSION['UID']);

if (isset($_POST['submit'])) {
    if ($_POST['user_password'] == '') {
        $err = $lang['password_current_null'];
    } else if ($_POST['password_new'] == '') {
        $err = $lang['password_new_null'];
    } else if ($_POST['password_confirm'] == '') {
        $err = $lang['password_confirm_null'];
    } else if (mb_strlen($_POST['password_new']) < 4) {
        $err = $lang['password_length_error'];
    } else if ($_POST['password_new'] != $_POST['password_confirm']) {
        $err = $lang['password_match_error'];
    }

    if ($err == '') {
        if (! User::validate($user_info['user_name'], $_POST['user_password'])) {
            $err = $lang['password_invalid'];
        } else {
            User::changePassword($user_info['user_name'], $_POST['password_new']);

            set_message($lang['password_success'], 'success');
            $redirect_url = FREETUBESITE_URL . '/' . $user_info['user_name'];
            Http::redirect($redirect_url);
        }
    }
}

$smarty->assign(array(
    'err' => $err,
    'msg' => $msg
));

$smarty->display('header.tpl');
$smarty->display('password_change.tpl');
$smarty->display('footer.tpl');
DB::close();
