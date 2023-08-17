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
require 'include/language/' . LANG . '/lang_group_members.php';

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1)
{
    $page = 1;
}

$sql = "SELECT * FROM `groups` WHERE
       `group_url`='" . DB::quote($_GET['group_url']) . "'";
$group_info = DB::fetch1($sql);

if (! $group_info)
{
    Http::redirect(FREETUBESITE_URL);
}

$smarty->assign('group_info', $group_info);

if (isset($_SESSION['UID']) && $_SESSION['UID'] == $group_info['group_owner_id'])
{
    if (isset($_POST['approve_mem']))
    {
        $sql = "UPDATE `group_members` SET
               `group_member_approved`='yes' WHERE
               `AID`=" . (int) $_POST['AID'] . " AND
               `group_member_group_id`='" . (int) $group_info['group_id'] . "'";
        DB::query($sql);
        $msg = str_replace('[GROUP_NAME]', $group_info['group_name'], $lang['user_approved']);
        set_message($msg, 'success');
        $redirect_url = FREETUBESITE_URL . '/group/' . $_GET['group_url'] . '/members/' . $page;
        Http::redirect($redirect_url);
    }

    if (isset($_POST['remove_mem']))
    {
        $sql = "DELETE FROM `group_members` WHERE
               `group_member_user_id`='" . (int) $_POST['member_id'] . "' AND
               `group_member_group_id`='" . (int) $group_info['group_id'] . "'";
        DB::query($sql);
        $msg = str_replace('[GROUP_NAME]', $group_info['group_name'], $lang['user_removed']);
        set_message($msg, 'success');
        $redirect_url = FREETUBESITE_URL . '/group/' . $_GET['group_url'] . '/members/' . $page;
        Http::redirect($redirect_url);
    }
}

if (isset($_SESSION['UID']) && ($_SESSION['UID'] != $group_info['group_owner_id']))
{
    $query = "AND `group_member_approved`='yes'";
}
else
{
    $query = '';
}

$sql = "SELECT count(*) AS `total` FROM `group_members` WHERE
       `group_member_group_id`='" . (int) $group_info['group_id'] . "'
        $query";
$total = DB::getTotal($sql);

$start_from = ($page - 1) * $config['items_per_page'];

$sql = "SELECT * FROM `group_members` WHERE
       `group_member_group_id`='" . (int) $group_info['group_id'] . "'
        $query
        ORDER BY `group_member_since` DESC
        LIMIT $start_from, $config[items_per_page]";
$group_members = DB::fetch($sql);

$start_num = $start_from + 1;
$end_num = $start_from + count($group_members);
$page_links = Paginate::getLinks($total, $config['items_per_page'], '.', '', $page);

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('page', $page);
$smarty->assign('start_num', $start_num);
$smarty->assign('end_num', $end_num);
$smarty->assign('page_link', $page_links);
$smarty->assign('total', $total);
$smarty->assign('group_members', $group_members);
$smarty->assign('sub_menu', 'menu_group_members.tpl');
$smarty->display('header.tpl');
$smarty->display('group_members.tpl');
$smarty->display('footer.tpl');
DB::close();
