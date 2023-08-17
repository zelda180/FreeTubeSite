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
require 'include/language/' . LANG . '/lang_upload_remote.php';

$guest_upload = Config::get('guest_upload');

if ($guest_upload == 0) {
    User::is_logged_in();
    $user_id = $_SESSION['UID'];
} else {
    $user_name = Config::get('guest_upload_user');
    $user_info = User::getByName($user_name);
    $user_id = $user_info['user_id'];
}

$upload_id = $_GET['upload_id'];

/*

--------------------------
VTYPE
--------------------------
LOCAL              0
YOUTUBE            1
FLV FILE           2
STAGE6.COM         3
METACAFE.COM       5
ENBEDDED           6
--------------------------
*/

if (isset($_SESSION["$upload_id"]['keywords'])) {
    $upload_video_keywords = $_SESSION["$upload_id"]['keywords'];
} else {
    $err = 1;
}

if (isset($_SESSION["$upload_id"]['title'])) {
    $upload_video_title = $_SESSION["$upload_id"]['title'];
} else {
    $err = 1;
}

if (isset($_SESSION["$upload_id"]['description'])) {
    $upload_video_descr = $_SESSION["$upload_id"]['description'];
} else {
    $err = 1;
}

if (isset($_SESSION["$upload_id"]['channels'])) {
    $channels = $_SESSION["$upload_id"]['channels'];
} else {
    $err = 1;
}

if (isset($_SESSION["$upload_id"]['field_privacy'])) {
    $type = $_SESSION["$upload_id"]['field_privacy'];
} else {
    $err = 1;
}

if ($err == 1) {
    $redirect_url = FREETUBESITE_URL . '/upload.php';
    Http::redirect($redirect_url);
}

if (isset($_POST['submit'])) {
    $adult = isset($_SESSION["$upload_id"]['adult']) ? $_SESSION["$upload_id"]['adult'] : 0;
    $url = $_POST['url'];

    if ($url == '') {
        $err = $lang['url_empty'];
    }

    if ($err == '' && preg_match("/youtube/i", $url) || preg_match("/metacafe/i", $url) || preg_match("/dailymotion/i", $url)) {
        # TO CREATE SEO NAME
        $seo_name = Url::seoName($upload_video_title);

        $video_duration = '1';
        $video_length = '01:00';

        if (preg_match("/youtube/i", $url)) {

            $youtube_video_id = BulkImport::getYoutubeVideoId($url);

            if (! empty($youtube_video_id)) {
                $video_duration = Youtube::getVideoDuration($youtube_video_id);
                $video_length = sec2hms($video_duration);
            }
        } else if (preg_match("/dailymotion/i", $url)) {
            $dailymotionVideo = new DailymotionVideo();
            $dailymotionVideoId = $dailymotionVideo->getIdFromUrl($url);
            $dailymotionVideo->videoId = $dailymotionVideoId;
            $dailymotionVideoInfo = $dailymotionVideo->videoInfo();
            $video_duration = $dailymotionVideoInfo['duration'];
            $video_length = sec2hms($video_duration);
        }

        if ($config['approve'] == 1 && Config::get('moderate_video_links') == 1) {
            if (preg_match('{\b(?:http://)?(www\.)?([^\s]+)*(\.[a-z]{2,3})\b}mi', $upload_video_descr)) {
                $config['approve'] = 0;
            }
        }

        # INSERT VIDEO TABLE VALUES


        $sql = "INSERT INTO `videos` SET
                   `video_user_id`='" . (int) $user_id . "',
                   `video_title`='" . DB::quote($upload_video_title) . "',
                   `video_description`='" . DB::quote($upload_video_descr) . "',
                   `video_keywords`='" . DB::quote($upload_video_keywords) . "',
                   `video_seo_name`='" . DB::quote($seo_name) . "',
                   `video_channels`='0|" . DB::quote($channels) . "|0',
                   `video_type`='" . DB::quote($type) . "',
                   `video_adult`='" . (int) $adult . "',
                   `video_duration`='" . (int) $video_duration . "',
                   `video_length`='" . DB::quote($video_length) . "',
                   `video_add_time`='" . $_SERVER['REQUEST_TIME'] . "',
                   `video_add_date`='" . date('Y-m-d') . "',
                   `video_active`='1',
                   `video_approve`='$config[approve]',
                   `video_name`='',
                   `video_vtype`='0',
                   `video_location`='',
                   `video_country`='',
                   `video_view_time`='" . DB::quote($_SERVER['REQUEST_TIME']) . "',
                   `video_embed_code`='',
                   `video_voter_id`='',
                   `video_folder`=''";

        $video_id = DB::insertGetId($sql);

        $upload_remote = new UploadRemote();
        $upload_remote->vid = $video_id;
        $upload_remote->url = $url;
        $upload_remote->debug = 1;

        if ($type == 'public' && $config['approve'] == 1) {
            $current_keyword = DB::quote($upload_video_keywords);
            $tags = new Tag($current_keyword, $video_id, $_SESSION['UID'], "0|$channels|0");
            $tags->add();
        }

        # CREATE THUMBNAILS

        if (preg_match("/youtube/i", $url)) {
            $err = $upload_remote->youtube();
        } else if (preg_match("/metacafe/i", $url)) {
            $err = $upload_remote->metacafe();
        } else if (preg_match('/dailymotion/', $url)) {
            $dailymotionVideo->freetubesiteVideoId = $video_id;
            $dailymotionVideo->CreateThumb();
            $err = $upload_remote->dailymotion($dailymotionVideoId);
        } else {
            $err = 'url not supported';
        }

        if ($err == '') {
            $sql = "UPDATE `subscriber` SET
                       `total_video`=`total_video`+1 WHERE
                       `UID`='" . (int) $user_id . "'";
            DB::query($sql);
            $redirect_url = FREETUBESITE_URL . '/upload/success/' . $video_id . '/remote/';
            Http::redirect($redirect_url);
        }
    } else {
        $err = $lang['invalid_url'];
    }
}

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('upload_remote.tpl');
$smarty->display('footer.tpl');
DB::close();
