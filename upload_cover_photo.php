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
require 'include/language/' . LANG . '/lang_upload_cover_photo.php';

User::is_logged_in();

$cover_photo_dir = FREETUBESITE_DIR . '/photo/cover';
$allowed_mimes = array(
    'image/jpg',
    'image/jpeg',
);

if (! is_dir($cover_photo_dir)) {
    @mkdir($cover_photo_dir, 0755, true);
    @touch($cover_photo_dir . '/index.html');
}

if (is_uploaded_file($_FILES['cover_photo']['tmp_name'])) {
    if ($_FILES['cover_photo']['error'] == 0) {
        if (in_array($_FILES['cover_photo']['type'], $allowed_mimes)) {
            $filename = $_SESSION['UID'] . '.jpg';
            move_uploaded_file($_FILES['cover_photo']['tmp_name'], $cover_photo_dir . '/' . $filename);
            Ajax::returnJson($lang['upload_success'], 'success');
            exit();
        }
    }
}

Ajax::returnJson($lang['upload_failed'], 'error');
exit();
