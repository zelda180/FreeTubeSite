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

Admin::auth();

$import_folder = FREETUBESITE_DIR . '/templates_c/import';

$videos = array();

$folder_empty = 0;

if (is_dir($import_folder)) {
    $import_video = dir($import_folder);
    while (false !== ($video = $import_video->read())) {
        if ($video != '.' && $video != '..') {
            $pos = strrpos($video, '.');
            $upload_file_extn = strtolower(substr($video, $pos + 1, strlen($video) - $pos));
            for ($i = 0; $i < count($file_types); $i ++) {
                if ($upload_file_extn == $file_types[$i]) {
                    $video_details[0] = $video;
                    $video_details[1] = urlencode($video);
                    $videos[] = $video_details;
                }
            }
        }
    }

} else {
    $folder_empty = 1;
}

$smarty->assign('folder_empty', $folder_empty);
$smarty->assign('import_video', $videos);
$smarty->display('admin/header.tpl');
$smarty->display('admin/import_folder.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
