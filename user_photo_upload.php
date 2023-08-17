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
require 'include/language/' . LANG . '/lang_user_photo_upload.php';

User::is_logged_in();

$userInfo = User::getById($_SESSION['UID']);

$allowedMimes = array(
    'image/jpeg',
    'image/pjpeg',
    'image/png',
);

if (isset($_FILES['photo']['tmp_name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
    $fileType = $_FILES['photo']['type'];
    $fileTmpName = $_FILES['photo']['tmp_name'];
    if (in_array($fileType, $allowedMimes)) {
        $imageInfo = getimagesize($fileTmpName);
        if ($imageInfo[2] == 2 || $imageInfo[2] == 3) {
            User::upload_photo();
            $msg = $lang['photo_uploaded'];
        }
    } else {
        $err = str_replace('[FILE_TYPE]', $fileType, $lang['invalid_file']);
    }
}

$photoUrl = User::get_photo($userInfo['user_photo'], $userInfo['user_id']);

$smarty->assign('freetubesite_rand', $_SERVER['REQUEST_TIME']);
$smarty->assign('photo_url', $photoUrl);
$smarty->assign('uid', $_SESSION['UID']);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('user_photo_upload.tpl');
$smarty->display('footer.tpl');
DB::close();
