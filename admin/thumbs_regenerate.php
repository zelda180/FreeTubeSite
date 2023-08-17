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
$result_per_page = 10;
if (isset($_GET['thumbs_regenerate'])) {
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

    if ($page < 1) {
        $page = 1;
    }

    $items_per_page = isset($_GET['items_per_page']) ? (int) $_GET['items_per_page'] : $result_per_page;
    $items_per_page = ($items_per_page < 1) ? $result_per_page : $items_per_page;
    $start = ($page - 1) * $items_per_page;
    $page++;

    $last_video_id = 0;
    $thumb_last_video_info_file = FREETUBESITE_DIR . '/templates_c/thumbs_regenerate.txt';
    if (file_exists($thumb_last_video_info_file)) {
        $last_video_id = file_get_contents($thumb_last_video_info_file);
        $last_video_id = (int) $last_video_id;
    }

    $sql = "SELECT `video_id`,`video_name`,`video_flv_name`,`video_duration`,`video_folder`,`video_vtype`
            FROM `videos` WHERE
           `video_vtype` IN (0, 1)";
    if ($last_video_id > 0) {
        $sql .= " AND `video_id`<'$last_video_id'";
    }
    $sql .= " ORDER BY `video_id` DESC
             LIMIT $start, $items_per_page";
    $videos = DB::fetch($sql);

    if (! empty($videos)) {
        $tool_video_thumb = Config::get('tool_video_thumb');

        foreach ($videos as $video) {
            if ($video['video_vtype'] == 0) {
                $video_src = FREETUBESITE_DIR . '/video/' . $video['video_name'];
                if (! file_exists($video_src)) {
                    $video_src = FREETUBESITE_DIR . '/flvideo/' . $video['video_folder'] . $video['video_flv_name'];
                    if (! file_exists($video_src)) {
                        continue;
                    }
                }
                $t_info = array();
                $t_info['src'] = $video_src;
                $t_info['vid'] = $video['video_id'];
                $t_info['duration'] = $video['video_duration'];
                $t_info['video_folder'] = $video['video_folder'];
                $t_info['debug'] = 1;
                $t_info['tool'] = $tool_video_thumb;
                $tmp = VideoThumb::make($t_info);
            } elseif ($video['video_vtype'] == 1) {
                echo "<h2>Thumbnail creating from Youtube</h2>";
                for ($i = 1; $i <= 3; $i ++) {
                    if ($i == 1) {
                        $thumb_name = 'mqdefault.jpg';
                    } else {
                        $thumb_name = $i . '.jpg';
                    }
                    $source = 'http://img.youtube.com/vi/' . $video['video_name'] . '/' . $thumb_name;
                    $desination = FREETUBESITE_DIR . '/thumb/' . $i . '_' . $video['video_id'] . '.jpg';
                    Http::download($source, $desination);
                }
                #Create Main Image
                $source = 'http://img.youtube.com/vi/' . $video['video_name'] . '/hqdefault.jpg';
                $desination = FREETUBESITE_DIR . '/thumb/' . $video['video_id'] . '.jpg';
                Http::download($source, $desination);
            }
            $last_video_id = $video['video_id'];
        }

        if ($last_video_id > 0) {
            $fh = fopen($thumb_last_video_info_file, 'w');
            fwrite($fh, $last_video_id);
            fclose($fh);
        }

        echo "<p>Please wait...</p>";
        echo '<meta http-equiv="refresh" content="3;url=' . FREETUBESITE_URL . '/admin/thumbs_regenerate.php?thumbs_regenerate&items_per_page=' . $items_per_page . '&page=' . $page . '">';
    } else {
        set_message('Thumbs created successfully');
        $redirect_url = FREETUBESITE_URL . '/admin/thumbs_regenerate.php';
        Http::redirect($redirect_url);
    }

} else {
    $smarty->assign('result_per_page', $result_per_page);
    $smarty->assign('err', $err);
    $smarty->assign('msg', $msg);
    $smarty->display('admin/header.tpl');
    $smarty->display('admin/thumbs_regenerate.tpl');
    $smarty->display('admin/footer.tpl');
}

DB::close();
