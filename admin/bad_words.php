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
require '../include/language/' . LANG . '/admin/bad_words.php';

Admin::auth();

if (isset($_GET['action']) && $_GET['action'] == 'del') {
    $sql = "DELETE FROM `words` WHERE
           `word_id`='" . (int) $_GET['id'] . "'";
    DB::query($sql);
}

if (isset($_POST["action"]) && $_POST["action"] == 'add') {
    if ($_POST['word'] == '') {
        $err = $lang['bad_word_empty'];
        $smarty->assign('err', $err);
    }
    if ($err == '') {
        $word = trim($_POST['word']);
        $word = mb_strtolower($word);
        $sql = "INSERT INTO `words` SET
               `word`='" . DB::quote($word) . "'";
        DB::query($sql);
        $msg = $_POST['word'] . ' ' . $lang['bad_word_added'];
        $smarty->assign('msg', $msg);
    }
}

$sql = "SELECT * FROM `words`";
$badwords = DB::fetch($sql);

$smarty->assign('badwords', $badwords);
$smarty->display('admin/header.tpl');
$smarty->display('admin/bad_words.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
