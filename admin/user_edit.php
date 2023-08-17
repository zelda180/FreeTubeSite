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
require '../include/language/' . LANG . '/admin/user_edit.php';

Admin::auth();

if (isset($_POST['submit'])) {
    $sql = "UPDATE `users` SET
           `user_email`='" . DB::quote($_POST['email']) . "',
           `user_first_name`='" . DB::quote($_POST['fname']) . "',
           `user_last_name`='" . DB::quote($_POST['lname']) . "',
           `user_about_me`='" . DB::quote($_POST['aboutme']) . "',
           `user_website`='" . DB::quote($_POST['website']) . "',
           `user_town`='" . DB::quote($_POST['hometown']) . "',
           `user_city`='" . DB::quote($_POST['city']) . "',
           `user_country`='" . DB::quote($_POST['country']) . "',
           `user_occupation`='" . DB::quote($_POST['occupation']) . "',
           `user_company`='" . DB::quote($_POST['company']) . "',
           `user_school`='" . DB::quote($_POST['school']) . "',
           `user_interest_hobby`='" . DB::quote($_POST['interest_hobby']) . "',
           `user_fav_movie_show`='" . DB::quote($_POST['fav_movie_show']) . "',
           `user_fav_music`='" . DB::quote($_POST['fav_music']) . "',
           `user_fav_book`='" . DB::quote($_POST['fav_book']) . "',
           `user_email_verified`= '" . DB::quote($_POST['emailverified']) . "',
           `user_account_status`='" . DB::quote($_POST['account_status']) . "' WHERE
           `user_id`='" . (int) $_GET['uid'] . "'";
    DB::query($sql);
    set_message($lang['user_info_updated'], 'success');
    $redirect_url = FREETUBESITE_URL . '/admin/user_view.php?user_id=' . $_GET['uid'];
    Http::redirect($redirect_url);
}

$sql = "SELECT * FROM `users` WHERE
       `user_id`='" . (int) $_GET['uid'] . "'";
$user = DB::fetch1($sql);

$countries = new Country();
$smarty->assign('country_box', $countries->country_options($user['user_country']));

if ($user['user_email_verified'] == 'yes') {
    $emailverified_yes = "selected=\"selected\"";
    $emailverified_no = '';
} else {
    $emailverified_no = "selected=\"selected\"";
    $emailverified_yes = "";
}

$email_ver_box = "
    <option value='yes' $emailverified_yes>Yes</option>
    <option value='no' $emailverified_no>No</option>
";

$smarty->assign('email_ver_box', $email_ver_box);

$account_status_active = '';
$account_status_inactive = '';
$account_status_suspended = '';

if ($user['user_account_status'] == 'Active') {
    $account_status_active = "selected=\"selected\"";
} else if ($user['user_account_status'] == 'Inactive') {
    $account_status_inactive = "selected=\"selected\"";
} else if ($user['user_account_status'] == 'Suspended') {
    $account_status_suspended = "selected=\"selected\"";
}

$account_status_box = "
    <option value='Active' $account_status_active>Active</option>
    <option value='Inactive' $account_status_inactive>Inactive</option>
    <option value='Suspended' $account_status_suspended>Suspended</option>
";

$smarty->assign('account_status_box', $account_status_box);
$smarty->assign('user', $user);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->display('admin/header.tpl');
$smarty->display('admin/user_edit.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
