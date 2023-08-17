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
$version='0.1.0-ALPHA';
if (file_exists('../include/config.php') && filesize('../include/config.php') > 0) {
    $freetubesite_installed = 1;
    $html_title = 'FREETUBESITE UPGRADE';
} else {
    $freetubesite_installed = 0;
    $html_title = 'FREETUBESITE INSTALLATION';
}

require './tpl/header.php';
if ($freetubesite_installed == 1) {
    require '../include/config.php';
?>

<div class="page-header">
    <h1>FreeTubeSite <?php echo $version; ?> Upgrade</h1>
</div>

<div class="row">
    <div class="col-md-12">
    <p class="text-success"><strong>FreeTubeSite <?php echo $config['version']; ?> is already installed...</strong></p>

    <p>(If you want to re-install FreeTubeSite delete "include/config.php")</p>

    <p class="text-danger lead"><span class="glyphicon glyphicon-warning-sign"></span> Before you continue with upgrade, you must make a backup of your database and files.</p>
    <a href="./upgrade_start.php" class="col-md-4 btn btn-primary btn-lg">Upgrade Now</a>
    </div>
</div>

<?php
} else {
?>
<div class="page-header">
    <h1><strong>FreeTubeSite <?php echo $version; ?></strong><span class="text-muted"> Installation</span></h1>
</div>

<div class="row">
    <div class="col-md-12">
        <p class="lead">FreeTubeSite is the free and open source YouTube Clone Script. FreeTubeSite has many of the features as the popular video sharing site YouTube.com. 
The FreeTubeSite Script allows you to run your own video sharing portal. Visitors will be able to upload videos to your web site, view existing videos,
comment on video, share videos with others, view videos on their Android, Apple or other mobile devices and many more features.</p>
    </div>
    <div class="col-md-4">
        <br>
         <a href="./install.php" class="btn btn-primary btn-lg btn-block">Install FreeTubeSite</a>
        <br>
    </div>
</div>
<?php } ?>

<?php
require './tpl/footer.php';
