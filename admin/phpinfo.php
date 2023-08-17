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

ob_start();
phpinfo();
$phpinfo_txt = ob_get_contents();
ob_end_clean();

preg_match ('%<style type="text/css">(.*?)</style>.*?<body>(.*?)</body>%s', $phpinfo_txt, $matches);
$phpinfo_no_css = '<div id="freetubesite-phpinfo">' . $matches[2] . '</div>';

$phpinfo_css = "<style>
#freetubesite-phpinfo { font-family: verdana; font-size: 12pt; }
#freetubesite-phpinfo td,
#freetubesite-phpinfo th,
#freetubesite-phpinfo h1,
#freetubesite-phpinfo h2 {font-family: sans-serif;}
#freetubesite-phpinfo pre {margin: 0px; font-family: monospace;}
#freetubesite-phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
#freetubesite-phpinfo a:hover {text-decoration: underline;}
#freetubesite-phpinfo table {border-collapse: collapse;}
#freetubesite-phpinfo .center {text-align: center;}
#freetubesite-phpinfo .center table { margin-left: auto; margin-right: auto; text-align: left;}
#freetubesite-phpinfo .center th { text-align: center !important; }
#freetubesite-phpinfo td, #freetubesite-phpinfo th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
#freetubesite-phpinfo h1 {font-size: 150%;}
#freetubesite-phpinfo h2 {font-size: 125%;}
#freetubesite-phpinfo .p {text-align: left;}
#freetubesite-phpinfo .e {background-color: #ccccff; font-weight: bold; color: #000000;}
#freetubesite-phpinfo .h {background-color: #9999cc; font-weight: bold; color: #000000;}
#freetubesite-phpinfo .v {background-color: #cccccc; color: #000000;}
#freetubesite-phpinfo .vr {background-color: #cccccc; text-align: right; color: #000000;}
#freetubesite-phpinfo img {float: right; border: 0px;}
#freetubesite-phpinfo hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
</style>";

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
echo $phpinfo_no_css;
echo $phpinfo_css;
$smarty->display('admin/footer.tpl');
DB::close();
