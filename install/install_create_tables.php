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
session_start();
require 'inc/functions.php';
require '../include/classes/DB.php';

$html_title = 'FREETUBESITE INSTALLATION';
require 'tpl/header.php';
$tables = array();

DB::connect($_SESSION['FREETUBESITE_INSTALL']['DB_HOST'],$_SESSION['FREETUBESITE_INSTALL']['DB_USER'],$_SESSION['FREETUBESITE_INSTALL']['DB_PASSWORD'], $_SESSION['FREETUBESITE_INSTALL']['DB_NAME']);

$tables = DB::fetch('SHOW TABLES');
if (! empty($tables)) {

    echo "<p>Your database already have tables needed for freetubesite. If you are upgrading, use the upgrade script instead.</p>";
    echo "<p class=\"text-danger lead\">If you are doing fresh install, make sure the database is empty.</p>";
    echo "

    <div class=\"row\">
      <div class=\"col-md-4\">
        <form name='yesgo' method='POST' action=''>
        <input type='submit' class='btn-block btn btn-primary btn-lg' name='submit' value='Retry Installing' />
        <input type='hidden' name='step' value='2' />
        <input type=\"hidden\" name=\"db_host\" value=\"$db_host\" />
        <input type=\"hidden\" name=\"db_name\" value=\"$db_name\" />
        <input type=\"hidden\" name=\"db_user\" value=\"$db_user\" />
        <input type=\"hidden\" name=\"db_pass\" value=\"$db_pass\" />
        <input type=\"hidden\" name=\"action\" value=\"create_tables\" />
        </form>
      </div>
    </div>
    ";

} else {

    require 'inc/class.sql_import.php';
    $sql_import = new Sql2Db('sql/freetubesite.sql');
    $sql_import->debug_filename = 'install';
    $sql_import->import();
    $FreeTubeSite_pass = rand();
    $FreeTubeSite_pass_md5 = md5($salt.$FreeTubeSite_pass);

    require '../include/classes/User.php';

    User::create(array(
        'user_email' => 'you@yourdomain.com',
        'user_name' => 'freetubesite',
        'user_password' => $FreeTubeSite_pass_md5,
        'user_website' => 'https://github.com/zelda180/FreeTubeSite',
        'user_email_verified' => 'yes',
        'user_account_status' => 'Active',
        'user_join_time' => time(),
        'user_last_login_time' => time(),
    ));

    $logo_url_md = $config['baseurl'] . 'themes/default/images/logo.png';
    $logo_url_sm = $config['baseurl'] . 'themes/default/images/logo-small.png';
    $watermark_image_url = $config['baseurl'] . 'themes/default/images/watermark.png';

    $sql = "UPDATE `sconfig` SET
           `svalue`='" . DB::quote($logo_url_md) . "' WHERE
           `soption`='logo_url_md'";
    DB::query($sql);

    $sql = "UPDATE `sconfig` SET
           `svalue`='" . DB::quote($logo_url_sm) . "' WHERE
           `soption`='logo_url_sm'";
    DB::query($sql);

    $sql = "UPDATE `sconfig` SET
           `svalue`='" . DB::quote($watermark_image_url) . "' WHERE
           `soption`='watermark_image_url'";
    DB::query($sql);

    echo "
    <div class=row>
      <div class=col-md-12>
        <div class=\"alert alert-success\">
          <strong>Database tables created</strong>
        </div>
        <form action=\"install_finished.php\" METHOD=\"POST\">
        <input type=\"hidden\" name=\"FreeTubeSite_pass\" value=\"$FreeTubeSite_pass\" />
        <input type=\"submit\" name=\"submit\" value=\"Continue Installation\" class=\"col-md-4 btn btn-primary btn-lg\" />
        </form>
      </div>
    </div>";
}

require './tpl/footer.php';
