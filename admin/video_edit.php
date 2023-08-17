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
require '../include/language/' . LANG . '/admin/video_edit.php';
Admin::auth();
$video_id = isset($_GET['video_id']) ? $_GET['video_id'] : 0;

if (! is_numeric($video_id)) {
    echo $lang['vid_invalid'];
    exit(0);
}

$num_max_channels = Config::get('num_max_channels');

if (isset($_POST['submit'])) {
    $video = new Video();
    $video->video_id = $video_id;
    $video->video_title = $_POST['video_title'];
    $video->video_description = $_POST['video_description'];
    $video->video_keywords = $_POST['video_keywords'];
    $video->video_duration = (int) $_POST['video_duration'];
    $video->video_channels = $_POST['video_channels'];
    $video->video_type = $_POST['video_type'];
    $video->video_featured = $_POST['video_featured'];
    $video->video_active = (int) $_POST['video_active'];
    $video->video_allow_comment = $_POST['video_allow_comment'];
    $video->video_allow_rated = $_POST['video_allow_rated'];
    $video->video_allow_embed = $_POST['video_allow_embed'];
    $video->video_embeded_code = isset($_POST['video_embed_code']) ? $_POST['video_embed_code'] : '';
    $video->video_adult = (int) $_POST['video_adult'];
    $video->is_admin = 1;
    $save = $video->video_update();

    if ($save == 1) {
        set_message($lang['video_edit_ok'], 'success');
        $redirect_url = FREETUBESITE_URL . '/admin/video_details.php?id=' . $video_id;
        Http::redirect($redirect_url);
    } else {
        $err = $save;
    }
}

$video = Video::getById($video_id);
$mych = explode('|', $video['video_channels']);
$ch = Channel::get();
$ch_checkbox = '';

for ($i = 0; $i < count($ch); $i ++) {
    if (in_array($ch[$i]['channel_id'], $mych)) {
        $checked = 'checked="checked"';
    } else {
        $checked = '';
    }

    $ch_checkbox .= '<div class="checkbox">' .
                    '<label for="channel-' . $ch[$i]['channel_id'] . '">' .
                    '<input type="checkbox" name="video_channels[]" value=' . $ch[$i]['channel_id'] . ' ' . $checked . ' id="channel-' . $ch[$i]['channel_id'] . '">'
                    . htmlspecialchars_uni($ch[$i]['channel_name']) .
                    '</label>' .
                    '</div>';
}
$smarty->assign('ch_checkbox', $ch_checkbox);

if ($video['video_type'] == 'public') {
    $public_select = 'selected="selected"';
    $private_select = '';
} else {
    $private_select = 'selected="selected"';
    $public_select = '';
}

$type_box = "
<option value='public' $public_select>Public</option>
<option value='private' $private_select>Private</option>
";
$smarty->assign('type_box', $type_box);

if ($video['video_featured'] == 'yes') {
    $featured_yes = 'selected="selected"';
    $featured_no = '';
} else {
    $featured_no = 'selected="selected"';
    $featured_yes = '';
}

$featured_box = "
<option value='yes' $featured_yes>Yes</option>
<option value='no' $featured_no>No</option>
";
$smarty->assign('featured_box', $featured_box);

if ($video['video_active'] == 1) {
    $active_yes = 'selected="selected"';
    $active_no = '';
} else {
    $active_no = 'selected="selected"';
    $active_yes = '';
}

$active_box = "
<option value='1' $active_yes>Yes</option>
<option value='0' $active_no>No</option>
";
$smarty->assign('active_box', $active_box);

if ($video['video_allow_comment'] == 'yes') {
    $video_allow_comment_yes = 'selected="selected"';
    $video_allow_comment_no = '';
} else {
    $video_allow_comment_no = 'selected="selected"';
    $video_allow_comment_yes = '';
}

$comment_box = "
<option value='yes' $video_allow_comment_yes>Yes</option>
<option value='no' $video_allow_comment_no>No</option>
";
$smarty->assign('comment_box', $comment_box);

if ($video['video_allow_rated'] == 'yes') {
    $be_rated_yes = 'selected="selected"';
    $be_rated_no = '';
} else {
    $be_rated_no = 'selected="selected"';
    $be_rated_yes = '';
}

$rate_box = "
<option value='yes' $be_rated_yes>Yes</option>
<option value='no' $be_rated_no>No</option>
";
$smarty->assign('rate_box', $rate_box);

if ($video['video_allow_embed'] == 'enabled') {
    $embed_yes = 'selected="selected"';
    $embed_no = '';
} else {
    $embed_no = 'selected="selected"';
    $embed_yes = '';
}

$embed_box = "
<option value='enabled' $embed_yes>Yes</option>
<option value='disabled' $embed_no>No</option>
";

$smarty->assign('embed_box', $embed_box);
$smarty->assign('video', $video);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('a', $_GET['a']);
$smarty->display('admin/header.tpl');
$smarty->display('admin/video_edit.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
