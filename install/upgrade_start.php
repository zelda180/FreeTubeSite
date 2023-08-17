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

require '../include/config.php';
require 'tpl/header.php';

if (! isset($config) || ! is_array($config))
{
    echo '<p class=text-danger>ERROR: File include/config.php Missing.</font></p>
          <p>Restore include/config.php and try again.</p>';
    exit(0);
}

$freetubesite_versions = array(
    'v0.4.2018-beta',
);

if (! in_array($config['version'], $freetubesite_versions))
{
    echo <<<EOT
    <p class=lead>
    <strong>This upgrade script can only upgrade from <span class="text-danger">FreeTubeSite Release: v0.4.2018-beta</span><br />
    You are using an older version that requires a manual upgrade, for upgrade instruction please visit </strong> </p>
    <a href="https://github.com/zelda180/FreeTubeSite/releases" target="_blank">https://github.com/zelda180/FreeTubeSite/releases</a>
    <hr />
EOT;
    exit(0);
}

switch ($config['version'])
{
    case 'v0.4.2018-beta':
        $redirect_url = FREETUBESITE_URL . '/install/upgrade_v0.4.2018-beta_to_v1.5.2018.php';
        break;
    default:
        $redirect_url = FREETUBESITE_URL . '/install/upgrade_finished.php';
        break;
}
require './tpl/footer.php';
Http::redirect($redirect_url);
