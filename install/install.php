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
$html_title = 'FREETUBESITE INSTALLATION';
require 'tpl/header.php';
require 'inc/folders.php';
$error = '';
echo '<div class="page-header"><h1>Installation Instruction</h1><br>
<li><b>Upload all the Files in the FreeTubeSite folder to your video site Home/Root Directory On Your Server.<br>
<li><b>Unzip ffmpeg.zip AND ffprobe.zip in the ffmpeg folder AND make them executable on your video site /ffmpeg Directory On Your Server.</b></div>';
echo '<ul class=list-group>';

while (list($f, $t) = each($writable_folders))
{
    if ($t == 'DIR')
    {
        echo '<li class=list-group-item>Upload The Directory <span class="text-primary"><b>' . $f . '</b></span> To Your Server. <span class="badge">chmod 755 or 777</span>';
    }
    else if ($t == 'FILE')
    {
        echo '<li class=list-group-item>Upload & Set The Permission Of <span class="text-primary"><b>' . $f . '</b></span> To Writable, <span class="badge">chmod 755 or 777</span>';
    }
}
while (list($f, $t) = each($executable_files))
{

        echo '<li class=list-group-item>Unzip, Upload & Set The Permission Of <span class="text-primary"><b>' . $f . '</b></span> To Executable. <span class="badge">chmod +x</span>';
}

echo '</ul>';

?>
<div class="row">
    <div class="col-md-8 clearfix">
        <form name="myform1" id="foler-input-form" method="POST"
        action="./install_check_folder_permission.php"
        onsubmit="return check_folder();"><input type="hidden" name="step"
        value="1" /><p> Enter Directory Location below:</p>
            <div class="form-group">
                <input type="text" class="form-control" name="folder" value="<?php echo str_replace('/install/install.php', '', $_SERVER['SCRIPT_FILENAME']); ?>" />
            </div>
            <div class="form-group">
             <input type="submit" name="submit" class="col-md-6 btn btn-primary btn-lg" value="Start Installation" />
            </div>
        </form>
    </div>
</div>
<?php

require 'tpl/footer.php';
