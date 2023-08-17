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
require 'include/language/' . LANG . '/lang_search_group.php';

$t1 = $_SERVER['REQUEST_TIME'];

if (isset($_GET['search'])) {
    $_GET['search'] = htmlspecialchars_uni($_GET['search']);
    $_GET['search'] = str_replace("<", ' ', $_GET['search']);
    $_GET['search'] = str_replace(">", ' ', $_GET['search']);
    $_GET['search'] = str_replace("(", ' ', $_GET['search']);
    $_GET['search'] = str_replace(")", ' ', $_GET['search']);
    $_GET['search'] = str_replace('"', ' ', $_GET['search']);
} else {
    $err = $lang['search_empty'];
}

if ($_GET['search'] == '') {
    $err = $lang['search_empty'];
}

if ($err == '') {

    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

    if ($page < 1) {
        $page = 1;
    }

    $search_string = DB::quote($_GET['search']);

    $sql = "SELECT count(*) AS `total` FROM `groups` WHERE (
           `group_name` LIKE '%$search_string%' OR
           `group_keyword` LIKE '%$search_string%' OR
           `group_description` LIKE '%$search_string%')";
    $total = DB::getTotal($sql);

    if ($total > 0) {
        $start_from = ($page - 1) * $config['items_per_page'];

        $sql = "SELECT * FROM `groups` WHERE (
               `group_name` LIKE '%$search_string%' OR
               `group_keyword` LIKE '%$search_string%' OR
               `group_description` LIKE '%$search_string%')
                LIMIT $start_from, $config[items_per_page]";
        $groups = DB::fetch($sql);
        $group_keywords_all = '';

        foreach ($groups as $group) {
            $group['group_keywords_array'] = explode(' ', $group['group_keyword']);
            $group_keywords_all .= $group['group_keyword'] . ' ';
            $group_info[] = $group;
        }

        $view = array();
        $group_keywords_array_all = explode(' ', $group_keywords_all);
        $view['group_keywords_array_all'] = array_remove_duplicate($group_keywords_array_all);

        $start_num = $start_from + 1;
        $end_num = $start_from + count($groups);

        $page_link = '';
        $total_page = $total / $config['items_per_page'];

        for ($k = 1; $k <= $total_page; $k ++) {
            $page_link .= "<a href=\"search_group.php?page=$k&search=$_GET[search]\">$k</a>&nbsp;&nbsp;";
        }

        $view['groups'] = $group_info;

        $smarty->assign('view', $view);
        $smarty->assign('page', $page);
        $smarty->assign('total', $total);
        $smarty->assign('start_num', $start_num);
        $smarty->assign('end_num', $end_num);
        $smarty->assign('page_link', $page_link);
    } else {
        $err = $lang['group_not_found'];
    }
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('sub_menu', "menu_groups.tpl");
$smarty->display('header.tpl');
$t2 = time();
$smarty->assign('ttime', ($t2 - $t1));
if ($err == '') $smarty->display('search_group.tpl');
$smarty->display('footer.tpl');
DB::close();
