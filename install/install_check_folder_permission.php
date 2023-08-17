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
if (strlen($_POST['folder']) < 2)
{
    echo '<br><div class="alert alert-warning"><strong>You must enter Directory Location </strong></div>';
    exit();
}
?>
<div class="page-header">
    <h1>Checking Directories And File Permissions</h1>
</div>
<?php
$error = 0;
echo '<ul id="check-default-folders" class=list-group>';
while (list($f, $t) = each($writable_folders))
{
    $dir = $_POST['folder'] . '/' . $f;
    $class = '';
    if (! is_writable($dir))
    {
        $status = '<span class="pull-right text-danger"><span class="glyphicon glyphicon-remove"></span><strong> Error
        </strong></span><!--  $dir  -->';
        $class = " class=\"error\"";
        $error = 1;
    }
    else
    {
        $status = '<span class="pull-right text-success"><span class="glyphicon glyphicon-ok"></span><strong> Success</strong></span>';
    }
    echo '<li class=list-group-item>Is permission for ' . $t . ' <span class="text-primary"><b>' . $f . '</b></span> 755 or 777 ' . $status . '</li>';
}
while (list($f, $t) = each($executable_files))
{
    $file = $_POST['folder'] . '/' . $f;
    $class = '';
    if (! is_executable($file))
    {
        $status = '<span class="pull-right text-danger"><span class="glyphicon glyphicon-remove"></span><strong> Error
        </strong></span><!--  $dir  -->';
        $class = " class=\"error\"";
        $error = 1;
    }
    else
    {
        $status = '<span class="pull-right text-success"><span class="glyphicon glyphicon-ok"></span><strong> Success</strong></span>';
    }
    echo '<li class=list-group-item>Is ' . $t . ' <span class="text-primary"><b>' . $f . '</b></span> executable' . $status . '</li>';
}
echo '</ul>';

if ($error == 1)
{
    echo "<div class=row><div class=col-md-12><form method='POST' action=''>
    <div class=form-group>
        <input type='submit' class='col-md-4 btn btn-primary btn-lg' name='submit' value='check Again'>
        <input type='hidden' name='folder' value='{$_POST['folder']}'></div>
        </form></div></div>";
}
else
{
    echo "<div class=row><div class=col-md-12><form method='POST' action='./install_collect_info.php'>
        <div class=form-group><input type='submit' class='col-md-4 btn btn-primary btn-lg' name='submit' value='Continue Installation'>
        <input type='hidden' name='folder' value='{$_POST['folder']}'></div>
        </form></div></div>";
}
require './tpl/footer.php';
