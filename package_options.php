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

$package = Package::find($_GET['package_id']);

$smarty->assign('package', $package);

$period_ops = '';

if ($package['package_period'] == 'Year') {
    for ($i = 1; $i <= 5; $i ++) {
        $period_ops .= '<option value="' . $i . '">' . $i . '</option>';
    }
} else if ($package['package_period'] == 'Month') {
    for ($i = 1; $i <= 12; $i ++) {
        $period_ops .= '<option value="' . $i . '">' . $i . '</option>';
    }
}

$smarty->assign('period_ops', $period_ops);

if ($config['payment_method'] != '') {
    $method = explode('|', $config['payment_method']);
    $payment_method_ops = '';
    while (list($k, $v) = each($method)) {
        $payment_method_ops .= '<option value="' . $v . '">' . $v . '</option>';
    }
    $smarty->assign('payment_method_ops', $payment_method_ops);
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('package_options.tpl');
$smarty->display('footer.tpl');
DB::close();
