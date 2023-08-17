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
$freetubesite_version='0.1.0-ALPHA';
date_default_timezone_set('America/New_York');
set_time_limit(0);
define('FREETUBESITE_DIR', $config['basedir']);
define('FREETUBESITE_URL', $config['baseurl']);
$config['TMB_DIR'] = FREETUBESITE_DIR . '/thumb';

require FREETUBESITE_DIR . '/include/smarty/libs/Smarty.class.php';
$smarty = new Smarty();
if (defined('ADMIN_AREA')) {
    $smarty->template_dir = FREETUBESITE_DIR . '/themes';
    $img_css_url = FREETUBESITE_URL . '/themes/admin';
} else {
    if (! isset($config['theme'])) {
        $smarty->template_dir = FREETUBESITE_DIR . '/templates';
        $img_css_url = FREETUBESITE_URL . '/templates';
    } else {
        $smarty->template_dir = FREETUBESITE_DIR . '/themes/' . $config['theme'];
        $img_css_url = FREETUBESITE_URL . '/themes/' . $config['theme'];
    }
}
$smarty->compile_dir = FREETUBESITE_DIR . '/templates_c';
$smarty->cache_dir = FREETUBESITE_DIR . '/templates_c/cache';
$smarty->caching = 0;
$smarty->error_reporting = E_ALL & ~E_NOTICE;

function freetubesite_autoload ($my_class_name) {
        include(__DIR__ . '/classes/' . $my_class_name . ".php");
}

spl_autoload_register("freetubesite_autoload");
DB::connect($db_host, $db_user, $db_pass, $db_name);
require FREETUBESITE_DIR . '/include/functions.php';

$sql = "SELECT * FROM `sconfig`";
$result = DB::query($sql);

while ($tmp = mysqli_fetch_assoc($result)) {
    $field = $tmp['soption'];
    $config[$field] = $tmp['svalue'];
}

DB::freeResult();
$smarty->assign($config);

$sql = "SELECT * FROM `servers`";
$result = DB::query($sql);
$servers[0] = FREETUBESITE_URL;

if (mysqli_num_rows($result) > 0) {
    while ($tmp = mysqli_fetch_assoc($result)) {
        $tmp_server_id = $tmp['id'];
        $servers[$tmp_server_id] = $tmp['url'];
    }
}

define('IMG_CSS_URL', $img_css_url);

$smarty->assign(array(
    'servers' => $servers,
    'base_url' => FREETUBESITE_URL,
    'base_dir' => FREETUBESITE_DIR,
    'img_css_url' => IMG_CSS_URL,
    'html_head_extra' => '',
    'html_extra' => '',
    'html_title' => '',
    'sub_menu' => '',
    'err' => '',
    'msg' => '',
));

if ($config['approve'] == 1) {
    $active = "and `active`='1'";
}

if (! isset($language)) {
    $language = 'en';
}

set_include_path('.' . PATH_SEPARATOR . FREETUBESITE_DIR . '/include/' . PATH_SEPARATOR . FREETUBESITE_DIR . '/include/PEAR/' . PATH_SEPARATOR . get_include_path());

$result_per_page = 20;
$msg = '';
$err = '';
if (isset($_SESSION['freetubesite_message'])) {
    switch ($_SESSION['freetubesite_message_type']) {
        case 'success':
            $msg = $_SESSION['freetubesite_message'];
            break;
        case 'error':
            $err = $_SESSION['freetubesite_message'];
            break;
        default:
            $msg = $_SESSION['freetubesite_message'];
            break;
    }
    unset($_SESSION['freetubesite_message']);
    unset($_SESSION['freetubesite_message_type']);
}

if (! isset($_SESSION['CSS'])) {
    Css::cookie();
}

if (! isset($_SESSION['LANG'])) {
    Language::cookie();
}

if (! isset($_SESSION['USERNAME']) && isset($_COOKIE['FREETUBESITE_AL_PASSWORD'])) {
    User::login_auto();
}
define('LANG', $_SESSION['LANG']);

if ($config['family_filter'] == 1) {
    if (! isset($_SESSION['FAMILY_FILTER'])) {
        $_SESSION['FAMILY_FILTER'] = 1;
    }
}

function dd($debug_message) {
    echo '<pre>';
    print_r($debug_message);
    exit;
}
