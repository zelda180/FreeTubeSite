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
require '../include/language/' . LANG . '/admin/advertisement_edit.php';

Admin::auth();

if (isset($_POST['submit'])) {
    if ($_POST['advertisement_text'] == '') {
        $err = $lang['advt_code_empty'];
    } else {
        $sql = "UPDATE `adv` SET
               `adv_text`='" . DB::quote($_POST['advertisement_text']) . "' WHERE
               `adv_id`='" . (int) $_GET['adv_id'] . "'";
        DB::query($sql);
        $msg = $lang['advt_saved'];
    }
}

$advertisement_id = isset($_GET['adv_id']) ? $_GET['adv_id'] : 0;

$sql = "SELECT * FROM `adv` WHERE
       `adv_id`='" . (int) $advertisement_id . "'";
$advertisement_info = DB::fetch1($sql);

$smarty->assign('advertisement_info', $advertisement_info);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/advertisement_edit.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
