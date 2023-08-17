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
require 'HTML/TagCloud.php';

Cache::init();

$latest_tags = Cache::load('latest_tags');

if (! $latest_tags) {
    $sql = "SELECT * FROM `tags` WHERE
	       `active`='1' AND
	       `tag_count` > 0
	        ORDER BY `used_on` DESC
	        LIMIT 100";
    $tags_all = DB::fetch($sql);

    if ($tags_all) {
        $tags = new HTML_TagCloud();
        foreach ($tags_all as $tag) {
            $tag_url = FREETUBESITE_URL . '/tag/' . strtolower($tag['tag']) . '/';
            $tags->addElement($tag['tag'], $tag_url, $tag['tag_count'], $tag['used_on']);
        }
        $latest_tags = $tags->buildHTML();
        unset($tags);
    }

    Cache::save('latest_tags', $latest_tags);
}

$smarty->assign('latest_tags', $latest_tags);

$popular_tags = Cache::load('popular_tags');

if (! $popular_tags) {
    $sql = "SELECT * FROM `tags` WHERE
           `active`='1' AND
           `tag_count` > 0
            ORDER BY `tag_count` DESC
            LIMIT 100";
    $polular_tags = DB::fetch($sql);

    if ($polular_tags) {
        $tags = new HTML_TagCloud();
        foreach ($polular_tags as $tag) {
            $tag_url = FREETUBESITE_URL . '/tag/' . strtolower($tag['tag']) . '/';
            $tags->addElement($tag['tag'], $tag_url, $tag['tag_count'], $tag['used_on']);
        }
        $popular_tags = $tags->buildHTML();
        unset($tags);
    }
    Cache::save('popular_tags', $popular_tags);
}

$smarty->assign(array(
    'popular_tags' => $popular_tags,
    'html_title' => 'Tags',
    'html_description' => 'Tags'
));

$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('header.tpl');
$smarty->display('tags.tpl');
$smarty->display('footer.tpl');
DB::close();
