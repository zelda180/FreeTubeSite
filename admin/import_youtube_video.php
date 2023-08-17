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

if (isset($_POST['submit'])) {
    $video_url = trim($_POST['url']);
    $user = trim($_POST['user']);
    $privacy = trim($_POST['privacy']);
    $privacy = ($privacy == 'private') ? 'private' : 'public';
    $channel = isset($_POST['channel']) ? $_POST['channel'] : '';

    if (filter_var($video_url, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED) === false) {
        $err = $lang['youtube_url_null'];
    } else {
        $youtube_video_id = BulkImport::getYoutubeVideoId($video_url);
        if (BulkImport::checkImported($youtube_video_id, 'youtube')) {
            $err = $lang['import_duplicate'];
        }
    }

    if ($err == '') {
        if (! check_field_exists($user, 'user_name', 'users')) {
            $err = $lang['user_not_found'];
        } else if (! is_numeric($channel)) {
            $err = $lang['channel_not_selected'];
        }
    }

    if ($err == '') {
        $video_info = Youtube::getVideoInfo($youtube_video_id);
        $user_info = User::getByName($user);
        $seo_name = Url::seoName($video_info['video_title']);

        $sql = "INSERT INTO `videos` SET
               `video_user_id`='" . (int) $user_info['user_id'] . "',
               `video_title`='" . DB::quote($video_info['video_title']) . "',
               `video_description`='" . DB::quote($video_info['video_description']) . "',
               `video_keywords`='" . DB::quote($video_info['video_keywords']) . "',
               `video_seo_name`='" . DB::quote($seo_name) . "',
               `video_channels`='0|" . DB::quote($channel) . "|0',
               `video_type`='" . DB::quote($privacy) . "',
               `video_duration`='" . (int) $video_info['video_duration'] . "',
               `video_length`='" . DB::quote($video_info['video_length']) . "',
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
        $vid = DB::insertGetId($sql);

        $upload = new UploadRemote();
        $upload->vid = $vid;
        $upload->url = $video_url;
        $upload->debug = 1;
        $upload->youtube();

        if ($config['approve'] == 1) {
            if (! empty($video_info['video_keywords'])) {
                $tags = new Tag($video_info['video_keywords'], $vid, $user_id, "0|$channel|0");
                $tags->add();
                $video_tags = $tags->get_tags();
                $sql = "UPDATE `videos` SET
                       `video_keywords`='" . DB::quote(implode(' ', $video_tags)) . "' WHERE
                       `video_id`='" . (int) $vid . "'";
                DB::query($sql);
            }
        }

        $sql = "INSERT INTO `import_track` SET
               `import_track_unique_id`='" . DB::quote($youtube_video_id) . "' ,
               `import_track_video_id`=" . (int) $vid . ",
               `import_track_site`='youtube'";
        $import_track_id = DB::insertGetId($sql);

        User::updateVideoCount($user_info['user_id']);

        $msg = $lang['video_added'];
        set_message($msg, 'success');
        DB::close();
        Http::redirect('video_details.php?id=' . $vid);
    }

} else {
    $video_url = '';
    $user = '';
}

$smarty->assign(array(
    'err' => $err,
    'msg' => $msg,
    'video_url' => $video_url,
    'user' => $user,
));
$smarty->display('admin/header.tpl');
$smarty->display('admin/import_youtube_video.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
