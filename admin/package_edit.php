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
require '../include/language/' . LANG . '/admin/package_edit.php';
Admin::auth();

if (isset($_POST['submit'])) {
    $package_trail_period = isset($_POST['package_trial_period']) ? $_POST['package_trial_period'] : 0;
    $sql = "UPDATE `packages` SET
         `package_name`='" . DB::quote($_POST['package_name']) . "',
         `package_description`='" . DB::quote($_POST['package_description']) . "',
         `package_space`='" . DB::quote($_POST['package_space']) . "',
         `package_price`= '" . DB::quote($_POST['package_price']) . "',
         `package_videos`='" . DB::quote($_POST['package_videos']) . "',
         `package_trial`='" . DB::quote($_POST['package_trial']) . "',
         `package_period`='" . DB::quote($_POST['package_period']) . "',
         `package_status`='" . DB::quote($_POST['package_status']) . "',
         `package_trial_period`='" . DB::quote($package_trail_period) . "',
         `package_allow_download`='" . (int) $_POST['allow_download'] . "' WHERE
         `package_id`='" . (int) $_POST['package_id'] . "'";
    DB::query($sql);

    if ($err == '') {
        set_message($lang['package_updated'], 'success');
        $redirect_url = FREETUBESITE_URL . '/admin/package_view.php?package_id=' . $_POST['package_id'];
        Http::redirect($redirect_url);
    }
}

$sql = "SELECT * FROM `packages` WHERE
       `package_id`='" . (int) $_GET['package_id'] . "'";
$package = DB::fetch1($sql);
$smarty->assign('package', $package);

if ($package['package_period'] == 'Day') {
    $select_year = '';
    $select_month = '';
    $select_day = "selected=\"selected\"";
} else if ($package['package_period'] == 'Month') {
    $select_year = '';
    $select_month = "selected=\"selected\"";
    $select_day = '';
} else if ($package['package_period'] == 'Year') {
    $select_year = "selected=\"selected\"";
    $select_month = '';
    $select_day = '';
}

if ($package['package_status'] == 'Active') {
    $select_active = "selected=\"selected\"";
    $select_inactive = '';
} else if ($package['package_status'] == 'Inactive') {
    $select_inactive = "selected=\"selected\"";
    $select_active = '';
}

if ((isset($package)) && ($package['package_trial'] == 'yes')) {
    $select_month = '';
    $select_year = '';
}

$period_ops = "
<option value='Month' $select_month>Month</option>
<option value='Year' $select_year>Year</option>";

$status_ops = "
<option value='Active' $select_active>Active</option>
<option value='Inactive' $select_inactive>Inactive</option>";

if ($package['package_allow_download'] == 1) {
    $select_download = "selected=\"selected\"";
} else {
    $select_download = '';
}

$download_ops = "
<option value='0' $select_download>No</option>
<option value='1' $select_download>Yes</option>
";

$smarty->assign('period_ops', $period_ops);
$smarty->assign('status_ops', $status_ops);
$smarty->assign('download_ops', $download_ops);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/package_edit.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
