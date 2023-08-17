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
require 'include/language/' . LANG . '/lang_renew_account.php';

if ($config['enable_package'] == 'yes') {
    if (isset($_POST['submit'])) {
        $package_id = isset($_POST['package_id']) ? (int) $_POST['package_id'] : 0;

        $sql = "SELECT * FROM `packages` WHERE
               `package_id`='$package_id' AND
               `package_trial`='no'";
        $package_info = DB::fetch1($sql);

        if (! $package_info) {
            $err = $lang['select_package'];
        } else {

            if ($package_info['package_price'] == 0) {
                $subscribe_date = date('Y-m-d H:i:s', time());
                if ($package_info['package_trial'] == 'no') {
                    $expire_time = strtotime("+1 $package_info[package_period]");
                } else {
                    $expire_time = strtotime("+$package_info[package_trial_period] days");
                }
                $expire_date = date('Y-m-d H:i:s', $expire_time);
                $pack_id = $package_info['package_id'];

                $sql = "UPDATE `subscriber` SET `pack_id`='" . $package_info['package_id'] . "',
                       `subscribe_time`='$subscribe_date',
                       `expired_time`='$expire_date',
                       `used_space`='0',
                       `total_video`='0' WHERE
                       `UID`='" . (int) $_GET['uid'] . "'";
                DB::query($sql);

                set_message('Subscription has been upgraded.', 'success');
                Http::redirect(FREETUBESITE_URL);
            }

            $redirect_url = FREETUBESITE_URL . '/package_options.php?package_id=' . (int) $package_id . '&user_id=' . $_GET['uid'];
            Http::redirect($redirect_url);
        }
    }

    $sql = "SELECT * FROM `packages` WHERE
           `package_status`='Active' AND
           `package_trial`='no'
            ORDER BY `package_price` DESC";
    $package = DB::fetch($sql);
    $smarty->assign('package', $package);
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('renew_account.tpl');
$smarty->display('footer.tpl');
DB::close();
