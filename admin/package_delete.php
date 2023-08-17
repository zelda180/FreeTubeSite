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

Admin::auth();

$package_id = isset($_GET['package_id']) ? (int) $_GET['package_id'] : 0;

$sql = "SELECT * FROM `packages` WHERE
       `package_id`='$package_id'";
$package_info = DB::fetch1($sql);

if (! $package_info) {
    Http::redirect('packages.php');
}

$sql = "SELECT `UID` FROM `subscriber` WHERE
       `pack_id`='" . $package_info['package_id'] . "'";
$subscribers = DB::fetch($sql);
$user_ids = array();

foreach ($subscribers as $subscriber) {
    $user_ids[] = $subscriber['UID'];
}

array_unique($user_ids);
$subscriber_count = count($user_ids);

if (isset($_POST['submit'])) {
    if ($subscriber_count > 0) {
        foreach ($user_ids as $user_id) {
            $sql = "UPDATE `subscriber` SET
                   `pack_id`='" . (int) $_POST['package_id'] . "' WHERE
                   `UID`='" . $user_id . "'";
            DB::query($sql);
        }
    }

    $sql = "DELETE FROM `packages` WHERE
           `package_id`='" . $package_id . "'";
    DB::query($sql);

    set_message('Package has been deleted', 'success');
    Http::redirect('packages.php');
}

$sql = "SELECT * FROM `packages` WHERE
       `package_id`!='" . $package_info['package_id'] . "'
        ORDER BY `package_name` ASC";
$packages = DB::fetch($sql);

$smarty->assign('package_info', $package_info);
$smarty->assign('packages', $packages);
$smarty->assign('subscriber_count', $subscriber_count);
$smarty->display('admin/header.tpl');
$smarty->display('admin/package_delete.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
