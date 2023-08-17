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
require 'include/language/' . LANG . '/lang_group_new.php';

User::is_logged_in();

if (isset($_POST['submit'])) {

    $group_name = htmlspecialchars_uni($_POST['group_name']);
    $group_name = trim($group_name);

    $tags = strip_tags($_POST['tags']);
    $tags = trim($tags);
    $tags = preg_replace('/[\,\s]+/', ' ', $tags);

    $description = htmlspecialchars_uni($_POST['description']);
    $description = trim($description);

    $short_name = strip_tags($_POST['short_name']);
    $short_name = trim($short_name);
    $short_name = Url::seoName($short_name);

    $smarty->assign('group_name', $group_name);
    $smarty->assign('tags', $tags);
    $smarty->assign('description', $description);
    $smarty->assign('short_name', $short_name);

    $chlist = $_POST['chlist'];

    if (strlen_uni($group_name) < 4) {
        $err = $lang['group_name_empty'];
    } else if (strlen_uni($tags) < 4) {
        $err = $lang['tags_empty'];
    } else if ($description == '') {
        $err = $lang['description_empty'];
    } else if ($short_name == '' || mb_strlen($short_name) < 3) {
        $err = $lang['group_url_invalid'];
    } else if (check_field_exists($short_name, 'group_url', 'groups') == 1) {
        $err = $lang['group_url_exist'];
    } else if (! isset($chlist)) {
        $err = $lang['channel_select'];
    } else if (count($chlist) < 1 || count($chlist) > 3) {
        $err = $lang['channel_select'];
    }

    if ($err == '') {
        $group_type_arr = array(
            'public',
            'private',
            'protected'
        );

        $group_type = $_POST['group_type'];

        if (! in_array($_POST['group_type'], $group_type_arr)) {
            $group_type = 'public';
        }

        $sql = "SELECT * FROM `videos` WHERE
			   `video_user_id`='" . (int) $_SESSION['UID'] . "'
				ORDER BY `video_id` DESC LIMIT 1";
        $user_new_video = DB::fetch1($sql);

        if ($user_new_video) {
            $group_image_video = $user_new_video['video_id'];
        } else {
            $group_image_video = 0;
        }

        $listch = implode('|', $chlist);

        $sql = "INSERT INTO `groups` SET
			   `group_owner_id`='" . (int) $_SESSION['UID'] . "',
			   `group_name`='" . DB::quote($group_name) . "',
			   `group_keyword`='" . DB::quote($tags) . "',
			   `group_description`='" . DB::quote($description) . "',
			   `group_url`='" . DB::quote($short_name) . "',
			   `group_channels`='0|" . DB::quote($listch) . "|0',
			   `group_type`='" . DB::quote($group_type) . "',
			   `group_upload`='" . DB::quote($_POST['video_upload_type']) . "',
			   `group_posting`='" . DB::quote($_POST['forum_upload_type']) . "',
			   `group_image`='" . DB::quote($_POST['group_icon']) . "',
			   `group_image_video`= '" . (int) $group_image_video . "',
			   `group_create_time`='" . DB::quote($_SERVER['REQUEST_TIME']) . "'";
        $id = DB::insertGetId($sql);

        $sql = "INSERT INTO `group_members` SET
			   `group_member_group_id`='" . (int) $id . "',
			   `group_member_user_id`='" . (int) $_SESSION['UID'] . "',
			   `group_member_since`='" . date("Y-m-d") . "'";
        DB::query($sql);

        $redirect_url = FREETUBESITE_URL . '/group/' . $short_name . '/';
        Http::redirect($redirect_url);
    }
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('chinfo', Channel::get());
$smarty->assign('sub_menu', 'menu_groups.tpl');
$smarty->display('header.tpl');
$smarty->display('group_new.tpl');
$smarty->display('footer.tpl');
DB::close();
