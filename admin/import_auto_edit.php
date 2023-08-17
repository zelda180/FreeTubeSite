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

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['import_auto_id'];

$sql = "SELECT * FROM `import_auto` WHERE
        `import_auto_id`='" . (int) $id . "'";
$import_auto_info = DB::fetch1($sql);

if (! $import_auto_info) {
    $msg = 'Keyword not found';
    set_message($msg, 'success');
    $redirect_url = FREETUBESITE_URL . '/admin/import_auto.php';
    Http::redirect($redirect_url);
}

if (isset($_POST['submit'])) {
    if ($_POST['video_keywords'] != '') {
        $video_keywords = htmlspecialchars($_POST['video_keywords'], ENT_QUOTES, 'UTF-8');
        $user_name = isset($_POST['video_user_name']) ? $_POST['video_user_name'] : '';
        $channel_id = isset($_POST['video_channel']) ? $_POST['video_channel'] : 0;
        $import_auto_download = isset($_POST['import_auto_download']) ? $_POST['import_auto_download'] : 0;
        if (! check_field_exists($user_name, 'user_name', 'users')) {
            $err = 'User not found - ' . $_POST['video_user_name'];
        }

        if (! check_field_exists($channel_id, 'channel_id', 'channels')) {
            $err = 'Select a channel';
        }

        if ($video_keywords != $import_auto_info['import_auto_keywords']) {
            if (check_field_exists($video_keywords, 'import_auto_keywords', 'import_auto')) {
                $err = 'This keyword already exist';
            }
        }

        if ($err == '') {
            $sql = "UPDATE `import_auto` SET
                    `import_auto_keywords`='" . DB::quote($video_keywords) . "',
                    `import_auto_user`='" . DB::quote($user_name) . "',
                    `import_auto_channel`='" . (int) $channel_id . "',
                    `import_auto_download`='" . (int) $_POST['import_auto_download'] . "' WHERE
                    `import_auto_id`='" . (int) $id . "'";
            DB::query($sql);
            $msg = 'Keyword updated successfully';
            set_message($msg, 'success');
            $redirect_url = FREETUBESITE_URL . '/admin/import_auto.php';
            Http::redirect($redirect_url);
        }
    } else {
        $msg = 'Please enter keyword';
    }
}

$smarty->assign('import_auto_info', $import_auto_info);
$smarty->assign('import_auto_id', $id);
$smarty->assign('channel_info', Channel::get());
$smarty->assign('msg', $msg);
$smarty->assign('err', $err);
$smarty->display('admin/header.tpl');
$smarty->display('admin/import_auto_edit.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
