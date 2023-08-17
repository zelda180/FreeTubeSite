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

$result_per_page = 50;

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $page = ($page < 1) ? 1 : $page;
    $items_per_page = isset($_GET['items_per_page']) ? (int) $_GET['items_per_page'] : $result_per_page;
    $items_per_page = ($items_per_page < 1) ? $result_per_page : $items_per_page;
    $start = ($page - 1) * $items_per_page;
    $page ++;

    $time_before_30_days = strtotime('-30 days');

    $sql = "SELECT `user_id`,`user_name` FROM `users` WHERE
           `user_account_status`='Inactive' AND
           `user_videos`='0' AND
           `user_join_time`<'$time_before_30_days'
            ORDER BY `user_id` ASC
            LIMIT $start, $items_per_page";
    $users = DB::fetch($sql);

    if ($users) {
        echo "<h2>Deleting inactive users. Please wait...</h2>";
        foreach ($users as $user) {
            $sql = "SELECT COUNT(`comment_id`) AS `total`
                    FROM `comments` WHERE
                   `comment_user_id`='" . $user['user_id'] . "'";
            $comments = DB::getTotal($sql);

            if ($comments == 0) {
                User::delete($user['user_id']);
                echo "<p>User " . $user['user_name'] . " deleted.</p>";
            }
        }
        DB::close();
        echo '<meta http-equiv="refresh" content="3;url=' . FREETUBESITE_URL . '/admin/users_inactive_delete.php?action=delete&items_per_page=' . $items_per_page . '&page=' . $page . '">';
    } else {
        DB::close();
        set_message('All inactive users have been deleted.', 'success');
        $redirect_url = FREETUBESITE_URL . '/admin/users_inactive_delete.php';
        Http::redirect($redirect_url);
    }
} else {
    $smarty->assign('result_per_page', $result_per_page);
    $smarty->assign('err', $err);
    $smarty->assign('msg', $msg);
    $smarty->display('admin/header.tpl');
    $smarty->display('admin/users_inactive_delete.tpl');
    $smarty->display('admin/footer.tpl');
    DB::close();
}
