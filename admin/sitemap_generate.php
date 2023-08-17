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

$sitemap = new Sitemap();
$create = isset($_GET['create']) ? $_GET['create'] : 1;
$sitemap_name = isset($_GET['sitemap']) ? $_GET['sitemap'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$video_count = isset($_GET['video_count']) ? $_GET['video_count'] : 0;
$last_video_id = isset($_GET['last_video_id']) ? $_GET['last_video_id'] : 0;
$sql_where = '';
$items_per_page = 200;

if (! empty($last_video_id)) {
    $sql_where = 'AND `video_id`>' . $last_video_id;
}

$start = ($page - 1) * $items_per_page;
$smarty->display('admin/header.tpl');
$sitemap_xml = '';

echo '<h1>Creating Sitemap : ' . $sitemap_name . '</h1>
        <table width="100%" cellspacing="1" cellpadding="3" border="0">
            <tr class="tabletitle">
                <td>
                    <b>Sitemap Video Url No</b>
                </td>
                <td>
                    <b>Sitemap Video Id</b>
                </td>
                <td>
                    <b>Sitemap Video Title</b>
                </td>
                <td>
                    <b>Action</b>
                </td>
            </tr>';

if ($create == 1) {
    if (empty($last_video_id)) {
        $sitemap->deleteSitemap();
    }
    $sitemap_name = $sitemap->createNewSitemapName();
    $fp = fopen(FREETUBESITE_DIR . '/sitemap/' . $sitemap_name, 'w');
    fwrite($fp, $sitemap->sitemap_xml_header . $sitemap->sitemap_urlset_open);
    $create = 0;
} else {
    if (empty($sitemap_name)) {
        $sitemap_name = $sitemap->createNewSitemapName();
    }
    $fp = fopen(FREETUBESITE_DIR . '/sitemap/' . $sitemap_name, 'a');
}

$sql = "SELECT * FROM `videos` WHERE
       `video_approve`='1' AND
       `video_active`='1'
        $sql_where
        ORDER BY `video_id` DESC
        LIMIT $start, $items_per_page";
$videos_all = DB::fetch($sql);

if ($videos_all) {

    $sitemap_size = filesize(FREETUBESITE_DIR . '/sitemap/' . $sitemap_name);

    if ($sitemap_size > $sitemap->sitemap_size_limit) {
        fwrite($fp, $sitemap->sitemap_urlset_close);
        $sitemap->insert_sitemap($video_count, $sitemap_name);
        $sitemap->xml_to_gz($sitemap_name);
        $sitemap_name = $sitemap->createNewSitemapName();
        $fp = fopen(FREETUBESITE_DIR . '/sitemap/' . $sitemap_name, 'w');
        fwrite($fp, $sitemap->sitemap_xml_header . $sitemap->sitemap_urlset_open);
        $video_count = 0;
    }

    foreach ($videos_all as $video_info) {
        $video_count ++;
        $tr_class = 'class="tablerow1"';
        if ($video_count % 2 == 0) {
            $tr_class = 'class="tablerow2"';
        }

        echo '<tr ' . $tr_class . '>
                <td>' . $video_count . ' </td>
                <td>' . $video_info['video_id'] . '</td>
                <td>' . $video_info['video_title'] . '</td>
                <td>Added to sitemap</td>
            </tr>';

        $video_info['video_title'] = $sitemap->cleanSitemap($video_info['video_title']);
        $video_info['video_description'] = $sitemap->cleanSitemap($video_info['video_description']);

        $sitemap_xml .= '
        <url>
            <loc>' . FREETUBESITE_URL . '/view/' . $video_info['video_id'] . '/' . $video_info['video_seo_name'] . '/</loc>
            <video:video>
                <video:player_loc allow_embed="yes" autoplay="ap=1">' . FREETUBESITE_URL . '/v/' . $video_info['video_id'] . '&amp;hl=en_US&amp;fs=1</video:player_loc>
                <video:thumbnail_loc>' . $servers[$video_info['video_server_id']] . '/thumb/' . $video_info['video_folder'] . '1_' . $video_info['video_id'] . '.jpg</video:thumbnail_loc>
                <video:title>' . $video_info['video_title'] . '</video:title>
                <video:description>' . $video_info['video_description'] . '</video:description>
                <video:duration>' . $video_info['video_duration'] . '</video:duration>
            </video:video>
        </url>';

        if ($video_count >= $sitemap->sitemap_url_limit) {
            $last_video_id = $video_info['video_id'];
            $sitemap_xml .= $sitemap->sitemap_urlset_close;
            $sitemap->insert_sitemap($video_count, $sitemap_name);
            $sitemap->xml_to_gz($sitemap_name);
            $create = 1;
            $video_count = 0;
            $page = 0;
            break;
        }
    }

    fwrite($fp, $sitemap_xml);
} else {
    fwrite($fp, $sitemap->sitemap_urlset_close);
    $sitemap->insert_sitemap($video_count, $sitemap_name);
    $sitemap->xml_to_gz($sitemap_name);
    $_SESSION['freetubesite_message'] = $sitemap->createSitemapIndex();
    $redirect_url = FREETUBESITE_URL . '/admin/sitemap.php';
    $_SESSION['freetubesite_message_type'] = 'success';
    Http::redirect($redirect_url);
}

echo '</table>';
$page ++;

?>

<meta http-equiv="refresh" content="2;url=<?php echo FREETUBESITE_URL;?>/admin/sitemap_generate.php?create=<?php echo $create;?>&sitemap=<?php echo $sitemap_name;?>&page=<?php echo $page;?>&video_count=<?php echo $video_count;?>&last_video_id=<?php echo $last_video_id;?>">
