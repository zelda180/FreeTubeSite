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

if ($config['family_filter'] == 0) {
    DB::close();
    Http::redirect(FREETUBESITE_URL);
}

if (! isset($_SESSION['REDIRECT']) || empty($_SESSION['REDIRECT'])) {
    if (! preg_match('/family_filter/i', $_SERVER['HTTP_REFERER'])) {
        if (preg_match("/" . preg_quote(FREETUBESITE_URL, '/') . "/i", $_SERVER['HTTP_REFERER'])) {
            $_SESSION['REDIRECT'] = $_SERVER['HTTP_REFERER'];
        } else {
        	$_SESSION['REDIRECT'] = FREETUBESITE_URL;
        }
    } else {
    	$_SESSION['REDIRECT'] = FREETUBESITE_URL;
    }
}

if ($_SESSION['FAMILY_FILTER'] == 0) {
    $_SESSION['FAMILY_FILTER'] = 1;
    if (isset($_SESSION['UID'])) {
        $sql = "UPDATE `users` SET `user_adult`='1' WHERE
               `user_id`='" . (int) $_SESSION['UID'] . "'";
        DB::query($sql);
    }
    $redirect_url = $_SESSION['REDIRECT'];
    unset($_SESSION['REDIRECT']);
    DB::close();
    Http::redirect($redirect_url);
} else {

    if (isset($_POST['submit'])) {
        $_SESSION['FAMILY_FILTER'] = 0;

        if (isset($_SESSION['UID'])) {
            $sql = "UPDATE `users` SET `user_adult`='0' WHERE
                   `user_id`='" . (int) $_SESSION['UID'] . "'";
            DB::query($sql);
        }

        $redirect_url = $_SESSION['REDIRECT'];
        unset($_SESSION['REDIRECT']);

        DB::close();
        Http::redirect($redirect_url);
    }
}

$smarty->assign('age_minimum', Config::get('signup_age_min'));
$smarty->display('header.tpl');
$smarty->display('family_filter.tpl');
$smarty->display('footer.tpl');
DB::close();
