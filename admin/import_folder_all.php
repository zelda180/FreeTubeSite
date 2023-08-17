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
require '../include/settings/upload.php';
require '../include/language/' . LANG . '/admin/import_video.php';

Admin::auth();

$import_folder = FREETUBESITE_DIR . '/templates_c/import';

$videos = array();

$todo = '';

if (is_dir($import_folder)) {
    $import_video = dir($import_folder);
    while (false !== ($video = $import_video->read())) {
        if ($video != '.' && $video != '..') {
            $pos = strrpos($video, '.');
            $upload_file_extn = strtolower(substr($video, $pos + 1, strlen($video) - $pos));
            for ($i = 0; $i < count($file_types); $i ++) {
                if ($upload_file_extn == $file_types[$i]) {
                    $videos[] = $video;
                }
            }
        }
    }
}

if (isset($_POST['submit'])) {
    $err = '';
    $user = $_POST['video_user'];
    $video_title = $_POST['video_title'];
    $type = $_POST['video_privacy'];
    $video_description = $_POST['video_description'];
    $tags = $_POST['video_keywords'];
    $channel = $_POST['channel'];

    if ($user == '') {
        $err = $lang['user_name_null'];
    } else if (strlen($video_title) < 4) {
        $err = $lang['title_too_short'];
    } else if (strlen($video_description) < 4) {
        $err = $lang['description_too_short'];
    } else if ($tags == '') {
        $err = $lang['tags_too_short'];
    } else if (! is_numeric($channel)) {
        $err = $lang['channel_not_selected'];
    }

    if ($err == '') {
        $user_info = User::getByName($user);

        if (! $user_info) {
            $err = $lang['user_not_found'];

        } else {
            $user_id = $user_info['user_id'];
        }

        if ($err == '') {
            for ($j = 0; $j < count($videos); $j ++) {
                $file_name = basename($videos[$j]);
                $pos = strrpos($file_name, '.');
                $file_extn = strtolower(substr($file_name, $pos + 1, strlen($file_name) - $pos));
                $file_no_extn = basename($file_name, ".$file_extn");
                $file_no_extn = preg_replace("/[&$#]+/", ' ', $file_no_extn);
                $file_no_extn = preg_replace("/[ ]+/", '-', $file_no_extn);
                $file_name = $file_no_extn . '.' . $file_extn;
                $file_path = FREETUBESITE_DIR . '/video/' . $file_name;
                $i = 0;

                while (file_exists($file_path)) {
                    $i ++;
                    $file_name = $file_no_extn . '_' . $i . '.' . $file_extn;
                    $file_path = FREETUBESITE_DIR . '/video/' . $file_name;
                }

                $source = FREETUBESITE_DIR . '/templates_c/import/' . $videos[$j];

                if (file_exists($source)) {

                    copy($source, $file_path);
                    unlink($source);

                    $qid = ProcessQueue::create(array(
                        'file' => $file_name,
                        'title' => $video_title,
                        'description' => $video_description,
                        'keywords' => $tags,
                        'channels' => $channel,
                        'type' => $type,
                        'user' => $user,
                        'status' => 2
                    ));

                    $todo = 'finished';
                }
            }

            $msg = $lang['video_process'];
        }
    }
}

if (empty($videos)) {
    $todo = 'folder_empty';
    $err = $lang['video_not_exists'];
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('todo', $todo);
$smarty->assign('channels', Channel::get());
$smarty->display('admin/header.tpl');
$smarty->display('admin/import_folder_all.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
