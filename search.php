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
require 'include/language/' . LANG . '/lang_search.php';

$_GET['search'] = isset($_GET['search']) ? $_GET['search'] : '';

$search_string = htmlspecialchars_uni($_GET['search']);
$search_string = str_replace('=', ' ', $search_string);
$search_string = str_replace('(', ' ', $search_string);
$search_string = str_replace(')', ' ', $search_string);
$search_string = trim($search_string);

$search_type = $_GET['type'];

$search_type_arr = array(
    'video',
    'user',
    'group'
);

if (! in_array($search_type, $search_type_arr))
{
    $search_type = 'video';
}

if ($search_string == '')
{
    $err = $lang['search_empty'];
}

if (get_magic_quotes_gpc())
{
    $search_string = stripslashes($search_string);
}

if ($err == '')
{
    if ($search_type == 'user')
    {
        $sql = "SELECT `user_id` FROM `users` WHERE
               `user_name`='" . DB::quote($search_string) . "'";
        $user = DB::fetch1($sql);

        if ($user)
        {
            $redirect_url = FREETUBESITE_URL . '/' . $search_string;
            Http::redirect($redirect_url);
        }
        else
        {
            $err = $lang['user_not_found'];
        }
    }
    else if ($search_type == 'group')
    {
        $redirect_url = FREETUBESITE_URL . '/search_group.php?search=' . $search_string;
        Http::redirect($redirect_url);
    }
    else
    {
        $search_string = str_replace('/', ' ', $search_string);
        $search_string = preg_replace('!\s+!', '-', $search_string); ## change space chars to dashes.
        $redirect_url = FREETUBESITE_URL . '/search/' . $search_string . '/';
        Http::redirect($redirect_url);
    }
}

DB::close();
set_message($err, 'error');
$redirect_url = FREETUBESITE_URL . '/';
Http::redirect($redirect_url);
