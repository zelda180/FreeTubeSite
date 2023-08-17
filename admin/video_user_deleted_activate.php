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
require '../include/language/' . LANG . '/admin/video_user_deleted_activate.php';

Admin::auth();

if (isset($_POST['activate'])) {

    $user_name = $_POST['user_name'];

    if ($user_name == '') {
        $err = $lang['user_name_empty'];
    }

    if ($err == '') {

        $user_info = User::getByName($_POST['user_name']);

        if ($user_info) {

            $sql = "UPDATE `videos` SET `video_user_id`=" . $user_info['user_id'] . ",
                   `video_active`='1' WHERE
                   `video_id`='" . (int) $_POST['video_id'] . "'";
            DB::query($sql);

            $video_info = Video::getById($_POST['video_id']);

            $flv_size = $video_info['video_space'];

            $sql = "UPDATE `subscriber` SET
                   `used_space`=`used_space`+$flv_size,
                   `total_video`=`total_video`+1 WHERE
                   `UID`='" . (int) $user_info['user_id'] . "'";
            DB::query($sql);

            User::updateVideoCount($user_info['user_id'], 1);

            $tags = new Tag($video_info['video_keywords'], $_POST['video_id'], $video_info['video_user_id'], $video_info['video_channels']);
            $tags->add();
            $msg = str_replace('[USERNAME]', $user_info['user_name'], $lang['video_activated']);
            set_message($msg, 'success');
            $redirect_url = FREETUBESITE_URL . '/admin/video_user_deleted.php';
            Http::redirect($redirect_url);
        } else {
            $err = $lang['user_not_found'];
        }
    }
}

if (is_numeric($_GET['id'])) {
    $video_info = Video::getById($_GET['id']);
    $smarty->assign('video', $video_info);
}

$smarty->assign('msg', $msg);
$smarty->assign('err', $err);
$smarty->display('admin/header.tpl');
$smarty->display('admin/video_user_deleted_activate.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
