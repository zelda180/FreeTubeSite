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
    $user_info = User::getByName($_POST['video_user']);

    if (! $user_info) {
        $err = $lang['user_not_found'];
    } else if (strlen($_POST['video_title']) < 4) {
        $err = $lang['title_too_short'];
    } else if (strlen($_POST['video_description']) < 4) {
        $err = $lang['description_too_short'];
    } else if (strlen($_POST['video_keywords']) < 4) {
        $err = $lang['tags_too_short'];
    } else if (! is_numeric($_POST['channel'])) {
        $err = $lang['channel_not_selected'];
    } else if (strlen($_POST['embed_code']) < 10) {
        $err = $lang['embed_code_null'];
    } else if (empty($_POST['embedded_code_image'][0]) && empty($_FILES['embedded_code_image_local']['name'][0])) {
        $err = $lang['image_null'];
    }

    if ($err == '') {
        $video_user_id = $user_info['user_id'];
        $video_title = $_POST['video_title'];
        $video_description = $_POST['video_description'];
        $video_keywords = $_POST['video_keywords'];
        $video_channels = $_POST['channel'];
        $video_type = $_POST['video_privacy'];
        $embed_code = $_POST['embed_code'];
        $vtype = 6;
        $video_duration = 60;
        $video_length = '1.00';

        $sql = "INSERT INTO `videos` SET
               `video_user_id`=" . (int) $video_user_id . ",
               `video_title`='" . DB::quote($video_title) . "',
               `video_description`='" . DB::quote($video_description) . "',
               `video_keywords`='" . DB::quote($video_keywords) . "',
               `video_seo_name`='" . Url::seoName($video_title) . "',
               `video_embed_code`='" . DB::quote($embed_code) . "',
               `video_channels`='0|$video_channels|0',
               `video_type`='$video_type',
               `video_vtype`=$vtype,
               `video_duration`='" . (int) $video_duration . "',
               `video_length`='" . DB::quote($video_length) . "',
               `video_add_time`='" . time() . "',
               `video_add_date`='" . date("Y-m-d") . "',
               `video_active`='1',
               `video_approve`='$config[approve]',
               `video_name`='',
               `video_location`='',
               `video_country`='',
               `video_view_time`='" . DB::quote($_SERVER['REQUEST_TIME']) . "',
               `video_voter_id`='',
               `video_folder`=''";
        $video_id = DB::insertGetId($sql);

        if ($video_type == 'public' && $config['approve'] == 1) {
            $current_keyword = DB::quote($video_keywords);
            $tags = new Tag($current_keyword, $video_id, $video_user_id, $video_channels);
            $tags->add();
        }

        if (! empty($_POST['embedded_code_image'][0])) {
            // Make player image
            $embedded_image = $_POST['embedded_code_image'];
            $filename = basename($embedded_image[0]);

            $destination_tmp = FREETUBESITE_DIR . '/templates_c/' . $filename;
            $source = $embedded_image[0];
            if (! Http::download($source, $destination_tmp)) {
                $err = $lang['image_download_failed'];
                set_message($err, 'error');
                Http::redirect('import_embed_video.php');
            }

            $destination = FREETUBESITE_DIR . '/thumb/' . $video_id . '.jpg';
            $source = $destination_tmp;
            $maxwidth = 500;
            $maxheight = 300;
            Image::createThumb($source, $destination, $maxwidth, $maxheight);

            unlink($destination_tmp);

            // Make thumbs
            $j = 0;

            for ($i = 0; $i < 3; $i ++) {
                $j ++;
                if (! empty($embedded_image[$i])) {
                    $filename = basename($embedded_image[$i]);
                    $destination_tmp = FREETUBESITE_DIR . '/templates_c/' . $filename;
                    $source = $embedded_image[$i];
                    Http::download($source, $destination_tmp);

                    $destination = FREETUBESITE_DIR . '/thumb/' . $j . '_' . $video_id . '.jpg';
                    $source = $destination_tmp;
                    Image::createThumb($source, $destination, $config['img_max_width'], $config['img_max_height']);

                    unlink($destination_tmp);
                } else {
                    $destination = FREETUBESITE_DIR . '/thumb/' . $j . '_' . $video_id . '.jpg';
                    copy(FREETUBESITE_DIR . '/themes/default/images/no_thumbnail.gif', $destination);
                }
            }
        } else if (! empty($_FILES['embedded_code_image_local']['tmp_name'][0])) {

            if (! is_uploaded_file($_FILES['embedded_code_image_local']['tmp_name'][0])) {
                $err = $lang['image_download_failed'];
                set_message($err, 'error');
                Http::redirect('import_embed_video.php');
            }

            // Make player image
            $destination = FREETUBESITE_DIR . '/thumb/' . $video_id . '.jpg';
            $source = $_FILES['embedded_code_image_local']['tmp_name'][0];
            $maxwidth = 500;
            $maxheight = 300;
            Image::createThumb($source, $destination, $maxwidth, $maxheight);

            // Make thumbs
            $j = 0;
            for ($i = 0; $i < 3; $i ++) {
                $j ++;
                if (! empty($_FILES['embedded_code_image_local']['name'][$i])) {
                    $destination = FREETUBESITE_DIR . '/thumb/' . $j . '_' . $video_id . '.jpg';
                    Image::createThumb($_FILES['embedded_code_image_local']['tmp_name'][$i], $destination, $config['img_max_width'], $config['img_max_height']);
                } else {
                    copy(FREETUBESITE_DIR . '/themes/default/images/no_thumbnail.gif', FREETUBESITE_DIR . '/thumb/' . $j . '_' . $video_id . '.jpg');
                }
            }
        }

        $msg = $lang['video_added'];
        set_message($msg, 'success');
        Http::redirect('import_embed_video.php');
    }
}

$smarty->assign('channels', Channel::get());
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/import_embed_video.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
