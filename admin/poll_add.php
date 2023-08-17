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
require '../include/language/' . LANG . '/admin/poll.php';
Admin::auth();

if (isset($_POST['submit'])) {
    $date_end = $_POST['end_date_year'] . '-' . $_POST['end_date_month'] . '-' . $_POST['end_date_day'];
    $date_start = $_POST['start_date_year'] . '-' . $_POST['start_date_month'] . '-' . $_POST['start_date_day'];
    $poll_start_time = strtotime($date_start);
    $poll_end_time = strtotime($date_end);
    if ($poll_start_time > $poll_end_time) {
        $err = $lang['poll_invalid_date'];
    }
    $num_answers = isset($_POST['num_answers']) ? (int) $_POST['num_answers'] : 0;
    if ($num_answers == 0) {
        $err = 'You must enter poll answers';
    }

    if ($err == '') {
        $poll_answers = '';
        for ($i = $num_answers - 1; $i >= 0; $i --) {
            if ($poll_answers != '') {
                $poll_answers .= '|';
            }
            $xx = 'poll_answer_' . $i;
            $poll_answers .= $_POST[$xx];
        }

        $sql = "INSERT INTO `poll_question` SET
           `poll_qty`='" . DB::quote($_POST['poll_question']) . "',
           `poll_answer`='" . DB::quote($poll_answers) . "',
           `start_date`='$date_start',
           `end_date`='$date_end'";
        DB::query($sql);

        set_message($lang['poll_submit'], 'success');
        $redirect_url = FREETUBESITE_URL . '/admin/poll_list.php';
        Http::redirect($redirect_url);
    }
}

$current_year = date('Y');
$current_month = date('m');
$current_day = date('j');
$month = months($current_month);
$days = days($current_day);
$year = cc_year($current_year);
$smarty->assign('err', $err);
$smarty->assign('msg', $msg);
$smarty->assign('days', $days);
$smarty->assign('month', $month);
$smarty->assign('year', $year);
$smarty->display('admin/header.tpl');
$smarty->display('admin/poll_add.tpl');
$smarty->display('admin/footer.tpl');
DB::close();
