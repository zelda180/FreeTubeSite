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
require '../include/language/' . LANG . '/admin/package_add.php';
Admin::auth();

if (isset($_POST['add_package'])) {
    if ($_POST['pack_name'] == '') {
        $err = $lang['package_name_empty'];
    } else if ($_POST['pack_desc'] == '') {
        $err = $lang['package_description_empty'];
    } else if ($_POST['space'] == '') {
        $err = $lang['package_space_empty'];
    } else if ($_POST['price'] == '') {
        $err = $lang['package_price_empty'];
    }

    if ($err == '') {
        $sql = "INSERT INTO `packages` SET
               `package_name`='" . DB::quote($_POST['pack_name']) . "',
               `package_description`='" . DB::quote($_POST['pack_desc']) . "',
               `package_space`='" . DB::quote($_POST['space']) . "',
               `package_price`='" . DB::quote($_POST['price']) . "',
               `package_videos`='" . DB::quote($_POST['video_limit']) . "',
               `package_period`='" . DB::quote($_POST['period']) . "',
               `package_status`='" . DB::quote($_POST['status']) . "'";
        DB::query($sql);
    }

    if ($err == '') {
        set_message($lang['package_added'], 'success');
        $redirect_url = FREETUBESITE_URL . '/admin/packages.php';
        Http::redirect($redirect_url);
    }
}

$period_ops = "
<option value='Month'>Month</option>
<option value='Year'>Year</option>";

$status_ops = "
<option value='Active'>Active</option>
<option value='Inactive'>Inactive</option>";

$smarty->assign('period_ops', $period_ops);
$smarty->assign('status_ops', $status_ops);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/package_add.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
