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
require '../include/language/' . LANG . '/admin/video_search.php';

Admin::auth();

if (isset($_GET['search'])) {

    if (isset($_GET['id']) && $_GET['id'] != '') {
        if (is_numeric($_GET['id'])) {
            $redirect_url = FREETUBESITE_URL . '/admin/video_details.php?id=' . $_GET['id'];
            Http::redirect($redirect_url);
        } else {
            $err = $lang['invalid_id'];
        }
    } else if ((isset($_GET['video_flv_name'])) && ($_GET['video_flv_name'] != '')) {
        $sql = "SELECT * FROM `videos` WHERE
               `video_flv_name`='" . DB::quote($_GET['video_flv_name']) . "'";
        $video_info = DB::fetch1($sql);

        if ($video_info) {
            $redirect_url = FREETUBESITE_URL . '/admin/video_details.php?id=' . $video_info['video_id'];
            Http::redirect($redirect_url);
        } else {
            $err = $lang['video_search_not_found'];
        }
    } else if ((isset($_GET['video_name'])) && ($_GET['video_name'] != '')) {
        $sql = "SELECT * FROM `videos` WHERE
               `video_name`='" . DB::quote($_GET['video_name']) . "'";
        $video_info = DB::fetch1($sql);

        if ($video_info) {
            $video_info = mysql_fetch_assoc($result);
            $redirect_url = FREETUBESITE_URL . '/admin/video_details.php?id=' . $video_info['video_id'];
            Http::redirect($redirect_url);
        } else {
            $err = $lang['video_search_not_found'];
        }
    } else if ((isset($_GET['video_title']) && $_GET['video_title'] != '') || (isset($_GET['video_description']) && $_GET['video_description'] != '')) {

        $allowed_sort = array(
            'video_id asc',
            'video_id desc',
            'video_title asc',
            'video_title desc',
            'video_type asc',
            'video_type desc',
            'video_duration asc',
            'video_duration desc',
            'video_featured asc',
            'video_featured desc',
            'video_add_date asc',
            'video_add_date desc'
        );

        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

        if (in_array($sort, $allowed_sort)) {
            $sql_sort = ' ORDER BY ' . $sort;
        } else {
            $sql_sort = ' ORDER BY `video_id` DESC';
        }

        if ($_GET['video_title'] != '') {

            $search_query = 'video_title=' . $_GET['video_title'];
            $smarty->assign('search_query', $search_query);
            $smarty->assign('search_string', $_GET['video_title']);

            $sql = "SELECT * FROM `videos` WHERE
                   `video_title` LIKE '%" . DB::quote($_GET['video_title']) . "%'
                    $sql_sort";
        } else if ($_GET['video_description'] != '') {
            $search_query = 'video_description=' . $_GET['video_description'];
            $smarty->assign('search_query', $search_query);
            $smarty->assign('search_string', $_GET['video_description']);

            $sql = "SELECT * FROM `videos` WHERE
                   `video_description` LIKE '%" . DB::quote($_GET['video_description']) . "%'
                    $sql_sort";
        }

        $search_result_videos = DB::fetch($sql);

        if ($search_result_videos) {
            $smarty->assign('total', count($search_result_videos));
            $smarty->assign('video_info', $search_result_videos);
        } else {
            $err = $lang['video_search_not_found'];
        }
    } else {
        $err = $lang['search_fields_empty'];
    }
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/video_search.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
