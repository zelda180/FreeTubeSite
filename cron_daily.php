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
$current_folder = dirname(__FILE__);
chdir("$current_folder");
require $current_folder . '/include/config.php';

$sql = "SELECT * FROM `import_auto`";
$auto_import_all = DB::fetch($sql);

if (! $auto_import_all) {
    exit();
}

foreach ($auto_import_all as $import_auto_info) {
    if ($import_auto_info['import_auto_keywords'] != '') {
        $search_string = $import_auto_info['import_auto_keywords'];
        $videos_info = Youtube::getVideos($search_string, 10);
        $videos = $videos_info['videos'];
        $video_count = count($videos);

        for ($i = 0; $i < $video_count; $i++) {
            if (! BulkImport::checkImported($videos[$i]['video_id'], 'youtube')) {
                $sql = "INSERT INTO `import_track` SET
					   `import_track_unique_id`='" . DB::quote($videos[$i]['video_id']) . "' ,
					   `import_track_site`='" . DB::quote('youtube') . "'";
                DB::query($sql);

                $video_url = 'http://www.youtube.com/watch?v=' . $videos[$i]['video_id'];
                $video_info = Youtube::getVideoInfo($videos[$i]['video_id']);
                $user_name = $import_auto_info['import_auto_user'];
                $user_info = User::getByName($user_name);
                $channel_id = $import_auto_info['import_auto_channel'];

                if ($import_auto_info['import_auto_download'] == 0) {

                    $seo_name = Url::seoName($video_info['video_title']);
                    $type = 'public';

                    $sql = "INSERT INTO `videos` SET
		                   `video_user_id`='" . (int) $user_info['user_id'] . "',
		                   `video_title`='" . DB::quote($video_info['video_title']) . "',
		                   `video_description`='" . DB::quote($video_info['video_description']) . "',
		                   `video_keywords`='" . DB::quote($video_info['video_keywords']) . "',
		                   `video_seo_name`='" . DB::quote($seo_name) . "',
		                   `video_channels`='0|" . DB::quote($channel_id) . "|0',
		                   `video_type`='" . DB::quote($type) . "',
		                   `video_duration`='" . (int) $video_info['video_duration'] . "',
		                   `video_length`='" . DB::quote($video_info['video_length']) . "',
		                   `video_add_time`='" . $_SERVER['REQUEST_TIME'] . "',
		                   `video_add_date`='" . date('Y-m-d') . "',
		                   `video_active`='1',
		                   `video_approve`='$config[approve]'";

                    $vid = DB::insertGetId($sql);

                    $upload = new UploadRemote();
                    $upload->vid = $vid;
                    $upload->url = $video_url;
                    $upload->debug = 1;

                    if ($type == 'public' && $config['approve'] == 1) {
                        $current_keyword = DB::quote($video_info['video_keywords']);
                        $tags = new Tag($video_info['video_keywords'], $vid, $user_info['user_id'], "0|$channel_id|0");
                        $tags->add();
                    }
                    $upload->youtube();

                } else if ($import_auto_info['import_auto_download'] == 1) {

                    $qid = ProcessQueue::create(array(
                        'title' => $video_info['video_title'],
                        'description' => $video_info['video_description'],
                        'keywords' => $video_info['video_keywords'],
                        'channels' => '0|' . $channel_id . '|0',
                        'type' => 'public',
                        'user' => $user_info['user_name'],
                        'status' => 0,
                        'url' => $video_url
                    ));
                }
            }
        }
        unset($videos);
    }
}
