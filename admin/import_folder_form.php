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
require '../include/language/' . LANG . '/admin/import_video.php';
Admin::auth();
$todo = '';

if (isset($_POST['submit'])) {
    $err = '';
    $user = $_POST['video_user'];
    $video_title = $_POST['video_title'];
    $type = $_POST['video_privacy'];
    $video = urldecode($_POST['video_name']);
    $video_description = $_POST['video_description'];
    $channel = $_POST['channel'];

    if ($user == '') {
        $err = $lang['user_name_null'];
    } else if (strlen($video_title) < 4) {
        $err = $lang['title_too_short'];
    } else if (strlen($video_description) < 4) {
        $err = $lang['description_too_short'];
    } else if ($_POST['video_keywords'] == '') {
        $err = $lang['tags_too_short'];
    } else if (! is_numeric($channel)) {
        $err = $lang['channel_not_selected'];
    }

    if ($err == '') {
        $user_info = User::getByName($user);

        if ($user_info) {
            $user_id = $user_info['user_id'];
        } else {
            $err = $lang['user_not_found'];
        }

        if ($err == '') {
            $file_name = basename($video);
            $pos = strrpos($file_name, '.');
            $file_extn = strtolower(substr($file_name, $pos + 1, strlen($file_name) - $pos));
            $file_no_extn = basename($file_name, ".$file_extn");
            $file_no_extn = preg_replace("/[&$#]+/", ' ', $file_no_extn);
            $file_no_extn = preg_replace("/\s+/", '-', $file_no_extn);
            $file_name = $file_no_extn . '.' . $file_extn;
            $file_path = FREETUBESITE_DIR . '/video/' . $file_name;
            $i = 0;

            while (file_exists($file_path)) {
                $i ++;
                $file_name = $file_no_extn . '_' . $i . '.' . $file_extn;
                $file_path = FREETUBESITE_DIR . '/video/' . $file_name;
            }

            $source = FREETUBESITE_DIR . '/templates_c/import/' . $video;
            copy($source, $file_path);
            unlink($source);

            $qid = ProcessQueue::create(array(
                'file' => $file_name,
                'title' => $video_title,
                'description' => $video_description,
                'keywords' => $_POST['video_keywords'],
                'channels' => $channel,
                'type' => $type,
                'user' => $user,
                'status' => 2
            ));

            $msg = $lang['video_process'];
            $todo = 'finished';
        }
    }
}

if (isset($_GET['video'])) {
    $writable = '';
    $video = urldecode($_GET['video']);
    $video_path = FREETUBESITE_DIR . '/templates_c/import/' . $video;

    if (! is_writable($video_path)) {
        $err = $lang['file_not_writable'] . " (chmod 777 $video_path)";
        $todo = 'finished';
    }
}

$smarty->assign('channels', Channel::get());
$smarty->assign('todo', $todo);
$smarty->assign('video_name', $video);
$smarty->assign('msg', $msg);
$smarty->assign('err', $err);
$smarty->display('admin/header.tpl');
$smarty->display('admin/import_folder_form.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
