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

User::is_logged_in();

$mail_folder = isset($_GET['folder']) ? $_GET['folder'] : 'inbox';

$mail_folder_types = array(
    'inbox',
    'outbox',
    'compose'
);

if (! in_array($mail_folder, $mail_folder_types)) {
    $mail_folder = 'inbox';
}

$mail_to = isset($_GET['receiver']) ? $_GET['receiver'] : '';
$mail_subject = isset($_GET['subject']) ? $_GET['subject'] : '';

$html_extra = '<script type="text/javascript" src="' . FREETUBESITE_URL . '/js/mail.js"></script>';

if ($mail_folder != 'compose') {
    $html_extra .= '
    <script type="text/javascript">
    var mail = new Mail();
    mail.showbox("' . $mail_folder . '");
    </script>
    ';
} else {
    $html_extra .= '
    <script type="text/javascript">
    var mail = new Mail();
    mail.compose("' . $mail_to . '","' . $mail_subject . '");
    </script>
    ';
}

$smarty->assign('html_extra', $html_extra);
$smarty->display('header.tpl');
$smarty->display('mail.tpl');
$smarty->display('footer.tpl');
DB::close();
