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
$imported_sites = array(
    'youtube'
);

if (isset($_POST['submit'])) {
    $video_id = isset($_POST['video_id']) ? $_POST['video_id'] : array();
    $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
    $channel_id = isset($_POST['channel_id']) ? (int) $_POST['channel_id'] : 0;

    $sql = "SELECT * FROM `users` AS u,`channels` AS c WHERE
            u.user_name='" . DB::quote($user_name) . "' AND
            c.channel_id='" . (int) $channel_id . "'";
    $tmp = DB::fetch1($sql);

    if ($tmp) {

        $user_id = $tmp['user_id'];
        $user_video_num = 0;

        for ($i = 0; $i < count($video_id); $i ++) {
            if (! BulkImport::checkImported($video_id[$i], $_POST['import_site']) && in_array($_POST['import_site'], $imported_sites)) {
                $sql = "INSERT INTO `import_track` SET
					   `import_track_unique_id`='" . DB::quote($video_id[$i]) . "' ,
					   `import_track_site`='" . DB::quote($_POST['import_site']) . "'";
                $import_track_id = DB::insertGetId($sql);

                if ($_POST['import_site'] == 'youtube') {
                    $video_url = 'http://www.youtube.com/watch?v=' . $video_id[$i];
                    $video_info['video_id'] = $video_id[$i];
                    $video_info['video_title'] = $_POST['video_title'][$video_id[$i]];
                    $video_info['video_description'] = $_POST['video_description'][$video_id[$i]];
                    $video_info['video_keywords'] = $_POST['video_keywords'][$video_id[$i]];
                    if (empty($video_info['video_keywords'])) {
                        $video_info['video_keywords'] = $video_info['video_title'];
                    }
                    $video_info['video_duration'] = $_POST['video_duration'][$video_id[$i]];
                }

                if ($_POST['import_method'] == 'embed') {
                    $video_length = sec2hms($video_info['video_duration']);
                    $seo_name = Url::seoName($video_info['video_title']);

                    $sql = "INSERT INTO `videos` SET
		                   `video_user_id`='" . (int) $user_id . "',
		                   `video_title`='" . DB::quote($video_info['video_title']) . "',
		                   `video_description`='" . DB::quote($video_info['video_description']) . "',
		                   `video_keywords`='" . DB::quote($video_info['video_keywords']) . "',
		                   `video_seo_name`='" . DB::quote($seo_name) . "',
		                   `video_channels`='0|" . DB::quote($channel_id) . "|0',
		                   `video_type`='" . DB::quote('public') . "',
		                   `video_duration`='" . (int) $video_info['video_duration'] . "',
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
                           `video_voter_id`='',
                           `video_folder`=''";
                    $vid = DB::insertGetId($sql);

                    $upload = new UploadRemote();
                    $upload->vid = $vid;
                    $upload->url = $video_url;
                    $upload->debug = 1;

                    if ($config['approve'] == 1) {
                        $current_keyword = DB::quote($video_info['video_keywords']);
                        $tags = new Tag($video_info['video_keywords'], $vid, $user_id, "0|$channel_id|0");
                        $tags->add();
                        $video_tags = $tags->get_tags();
                        $sql = "UPDATE `videos` SET
                               `video_keywords`='" . DB::quote(implode(' ', $video_tags)) . "' WHERE
                               `video_id`='" . (int) $vid . "'";
                        DB::query($sql);
                    }

                    if ($_POST['import_site'] == 'youtube') {
                        $upload->youtube();
                    }

                    $sql = "UPDATE `import_track` SET `import_track_video_id`=" . (int) $vid . " WHERE
                           `import_track_id`=" . (int) $import_track_id;
                    DB::query($sql);

                    User::updateVideoCount($user_id);

                } else {
                    $qid = ProcessQueue::create(array(
                        'title' => $video_info['video_title'],
                        'description' => $video_info['video_description'],
                        'keywords' => $video_info['video_keywords'],
                        'channels' => '0|' . $channel_id . '|0',
                        'type' => 'public',
                        'user' => $user_name,
                        'status' => 0,
                        'url' => $video_url,
                        'import_track_id' => $import_track_id,
                    ));
                }

                $user_video_num++;
            }
        }

        $sql = "UPDATE `subscriber` SET
               `total_video`=`total_video`+$user_video_num WHERE
               `UID`='" . (int) $user_id . "'";
        DB::query($sql);
    }

    $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
    $page = isset($_POST['page']) ? $_POST['page'] : '';

    $redirect_url = FREETUBESITE_URL . '/admin/import_bulk.php?keyword=' . $keyword . '&user_name=' . $user_name . '&channel=' . $channel_id . '&page=' . $page;
    Http::redirect($redirect_url);
}
