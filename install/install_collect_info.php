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
$html_title = 'FREETUBESITE INSTALLATION';
$error = '';
$folder = $_POST['folder'];

if (isset($_POST['connect_info']))
{
    $site_url = $_POST['site_url'];
    if (strlen($site_url) < 10) {
        $error .= '<li><span class="glyphicon glyphicon-remove"></span> <strong>Site url invalid</strong>.</li>';	
    }
    $site_urlm = $_POST['site_urlm'];
    if (strlen($site_urlm) < 12) {
        $error .= '<li><span class="glyphicon glyphicon-remove"></span> <strong>Mobile Site url invalid</strong>.</li>';
    }
    if (! is_dir($folder)) {
        $error .= '<li><span class="glyphicon glyphicon-remove"></span> <strong>Site Path not found</strong> ' . $folder . '</li>';
    }
    $ffmpeg_path = $_POST['ffmpeg_path'];
    if (! file_exists($ffmpeg_path)) {
        $error .= '<li><span class="glyphicon glyphicon-remove"></span> <strong>ffmpeg file not found</strong> ' . $ffmpeg_path . '</li>';
    }
    $qtfaststart_path = $_POST['qtfaststart_path'];
    if (! file_exists($qtfaststart_path)) {
        $error .= '<li><span class="glyphicon glyphicon-remove"></span> <strong>qt-faststart file not found</strong> ' . $qtfaststart_path . '</li>';
	}
    $ffprobe_path = $_POST['ffprobe_path'];
    if (! file_exists($ffprobe_path)) {
        $error .= '<li><span class="glyphicon glyphicon-remove"></span> <strong>ffprobe file not found</strong> ' . $ffprobe_path . '</li>';
    }

    $db_server = $_POST['db_server'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $link = @mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    if (! $link) {
        $error .= '<li><span class="glyphicon glyphicon-remove"></span><strong> Failed to connect to database server. Check Database User Name and Database Password fields. Also make sure you created the Database Name on your mysql server. </strong></li>';
    } else {
        $_SESSION['FREETUBESITE_INSTALL']['DB_NAME'] = $db_name;
        $_SESSION['FREETUBESITE_INSTALL']['DB_USER'] = $db_user;
        $_SESSION['FREETUBESITE_INSTALL']['DB_PASSWORD'] = $db_pass;
        $_SESSION['FREETUBESITE_INSTALL']['DB_HOST'] = $db_server;
    }
    if ($error == '')
    {

      $fp = fopen('../include/config.php', 'w');
      fputs($fp, '<?php');
      
# FreeTubeSite Config file 
$freetubesite_config = <<<EOT

error_reporting(~E_NOTICE ^ E_DEPRECATED ^ E_WARNING ^ E_STRICT);
session_start();
# mysql database info
\$db_host     = '$db_server';
\$db_name     = '$db_name';
\$db_user     = '$db_user';
\$db_pass     = '$db_pass';

\$language = 'en';
# Server paths to video converting software and sites urls.
\$config = array();
\$config['ffmpeg']          =  '$_POST[ffmpeg_path]'; #Required
\$config['qt-faststart']   =  '$_POST[qtfaststart_path]'; #Required
\$config['ffprobe']          =  '$_POST[ffprobe_path]'; #Required
\$config['mplayer']          =  '$_POST[mplayer_path]'; #Optional
\$config['mencoder']          =  '$_POST[mencoder_path]'; #Optional
\$config['basedir']        =  '$_POST[folder]'; #Server path to this site
\$config['baseurl']        =  '$_POST[site_url]'; #Sites URL
\$config['baseurlm']        =  '$_POST[site_urlm]'; #Sites Mobile URL
\$config['theme']          =  'default'; #Sites Theme

include(\$config['basedir'] . '/include/freetubesite.php');

EOT;

        fputs($fp, $freetubesite_config);
        fclose($fp);
        require './tpl/header.php';
        ?>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-success">
            <strong>Configuration file created</strong>
            (include/config.php)
        </div>
        
<!--Let's Make The Database After Submit -->
        <form method="post" action="install_create_tables.php"><input
        type="submit" class="col-md-4 btn btn-primary btn-lg" name=submit value="Continue Installation">
        <input type="hidden" name="db_host" value="<?php
        echo $db_server;
        ?>"> <input
        type="hidden" name="db_name" value="<?php
        echo $db_name;
        ?>"> <input
        type="hidden" name="db_user" value="<?php
        echo $db_user;
        ?>"> <input
        type="hidden" name="db_pass" value="<?php
        echo $db_pass;
        ?>"> <input
        type="hidden" name="action" value="create_tables">
        </form>
    </div>
</div>
<?php
        require 'tpl/footer.php';
        exit(0);
    }
}
else
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $site_url = str_replace('/install/install_collect_info.php', '', $url);
    $urlm = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $urlm = str_replace('/install/install_collect_info.php', '', $urlm);
    $site_urlm = str_replace('http://', 'http://m.', $urlm);

    $ffmpeg_path = '';
    $ffmpeg_dir= str_replace('install/install_collect_info.php', 'ffmpeg/ffmpeg', $_SERVER['SCRIPT_FILENAME']);
    if (file_exists($ffmpeg_dir))
    {
        $ffmpeg_path = $ffmpeg_dir;
    }    
    else if (file_exists('/usr/bin/ffmpeg'))
    {
        $ffmpeg_path = '/usr/bin/ffmpeg';
    }
    else if (file_exists('/usr/local/bin/ffmpeg'))
    {
        $ffmpeg_path = '/usr/local/bin/ffmpeg';
    }
    $qtfaststart_path = '';
    $qtfaststart_dir= str_replace('install/install_collect_info.php', 'ffmpeg/qt-faststart', $_SERVER['SCRIPT_FILENAME']);
    if (file_exists($qtfaststart_dir))
    {
        $qtfaststart_path = $qtfaststart_dir;
    }    
    else if (file_exists('/usr/bin/qt-faststart'))
    {
        $qtfaststart_path = '/usr/bin/qt-faststart';
    }
    else if (file_exists('/usr/local/bin/qt-faststart'))
    {
        $qtfaststart_path = '/usr/local/bin/qt-faststart';
    }
    $ffprobe_path = '';
    $ffprobe_dir= str_replace('install/install_collect_info.php', 'ffmpeg/ffprobe', $_SERVER['SCRIPT_FILENAME']);
    if (file_exists($ffprobe_dir))
    {
        $ffprobe_path = $ffprobe_dir;
    }    
    else if (file_exists('/usr/bin/ffprobe'))
    {
        $ffprobe_path = '/usr/bin/ffprobe';
    }
    else if (file_exists('/usr/local/bin/ffprobe'))
    {
        $ffprobe_path = '/usr/local/bin/ffprobe';
    }
    $mplayer_path = '';
    if (file_exists('/usr/bin/mplayer'))
    {
        $mplayer_path = '/usr/bin/mplayer';
    }
    else if (file_exists('/usr/local/bin/mplayer'))
    {
        $mplayer_path = '/usr/local/bin/mplayer';
    }
    $mencoder_path = '';
    if (file_exists('/usr/bin/mencoder'))
    {
        $mencoder_path = '/usr/bin/mencoder';
    }
    else if (file_exists('/usr/local/bin/mencoder'))
    {
        $mencoder_path = '/usr/local/bin/mencoder';
    }

    $db_name = 'FreeTubeSite';
    $db_user = $db_pass = '';
    $db_server = 'localhost';
}
require 'tpl/header.php';

if ($error != '')
{
    echo '<div class="text-danger"><ul class=list-unstyled>' . $error . '</ul></div>';
}

?>
<div class="page-header">
    <h1>Database &amp; Website Settings</h1>
</div>

<p>FreeTubeSite will run if your server supports all of the <a
	href="https://github.com/zelda180/FreeTubeSite/wiki/Requirements" target="_blank">requirements</a>.
If the path(s) are filled in the install script found the path(s). So there is no need to change them unless you have special requirements. 
If you don't know the path to ffmpeg, mencoder etc on your server, just open your terminal and type whereis (SOFTWARE NAME) (whereis ffmpeg) or ask your server provider or admin.</p>
<hr>

<form class="form-horizontal" name="myform2" method="POST" action="">
    <div class="form-group">
        <label class="control-label col-md-3">Site URL (Required)</label>
        <div class="col-md-5">
            <input type="text" class="form-control" name="site_url" size="33"
            value="<?php
            echo $site_url;
            ?>">
        </div>
        <div class="col-md-4">
            (i.e. <i>http://yoursite.com/freetubesite</i>)
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Mobile Site URL (Required)</label>
        <div class="col-md-5">
            <input type="text" class="form-control" name="site_urlm" size="33"
            value="<?php
            echo $site_urlm;
            ?>">
        </div>
        <div class="col-md-4">
            (i.e. <i>http://m.yoursite.com</i>)
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Site Path (Required)</label>
        <div class="col-md-5">
            <input type="text" class="form-control" name="folder" size="33"
            value="<?php
            echo $_POST['folder'];
            ?>">
        </div>
        <div class="col-md-4">
            (i.e. <i>/home/username/public_html</i>)
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">FFMpeg Binary (Required To Convert Videos & Thumbs)</label>
        <div class="col-md-5">
            <input class="form-control" type="text" name="ffmpeg_path" size="33"
            value="<?php
            echo $ffmpeg_path;
            ?>">
        </div>
        <div class="col-md-4">
            (i.e. <i>/usr/bin/ffmpeg</i>)
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Qt-faststart Binary (Required To Make .mp4 Skippable)</label>
        <div class="col-md-5">
            <input class="form-control" type="text" name="qtfaststart_path" size="33"
            value="<?php
            echo $qtfaststart_path;
            ?>">
        </div>
        <div class="col-md-4">
            (i.e. <i>/usr/bin/qt-faststart</i>)
        </div>
    </div>
    
        <div class="form-group">
        <label class="control-label col-md-3">FFprobe Binary (Required For Video Metadata)</label>
        <div class="col-md-5">
            <input class="form-control" type="text" name="ffprobe_path" size="33"
            value="<?php
            echo $ffprobe_path;
            ?>">
        </div>
        <div class="col-md-4">
            (i.e. <i>/usr/bin/ffprobe</i>)
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Mencoder Binary (Optional To Convert Videos & Thumbs)</label>
        <div class="col-md-5">
            <input class="form-control" type="text" name="mencoder_path" size="33"
            value="<?php
            echo $mencoder_path;
            ?>">
        </div>
        <div class="col-md-4">
            (i.e. <i>/usr/bin/mencoder</i>)
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Mplayer Binary (Optional)</label>
        <div class="col-md-5">
            <input class="form-control" type="text" name="mplayer_path" size="33"
            value="<?php
            echo $mplayer_path;
            ?>">
        </div>
        <div class="col-md-4">
            (i.e. <i>/usr/bin/mplayer</i>)
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3">MySQL Database Server (Required)</label>
        <div class="col-md-5">
            <input class="form-control" type="text" name="db_server" size="33"
            value="<?php
            echo $db_server;
            ?>">
        </div>
        <div class="col-md-4">
            usually <i>localhost</i>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Database Name (Required) You must create a new database with phpMyAdmin or command line. </label>
        <div class="col-md-5">
            <input class="form-control" type="text" name="db_name" size="33"
            value="<?php
            echo $db_name;
            ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Database User Name (Required)</label>
        <div class="col-md-5">
            <input class="form-control" type="text" name="db_user" size="33"
            value="<?php
            echo $db_user;
            ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Database Password (Required)</label>
        <div class="col-md-5">
            <input class="form-control" type="text" name="db_pass" size="33"
            value="<?php
            echo $db_pass;
            ?>">
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-3 col-md-5">
            <span class="text-info"><i>(NB : Don't use any ending slash in you path)</i></span>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-3 col-md-4">
            <input type="submit" class='btn-block btn btn-primary btn-lg' name="connect_info"
            value="Continue Installation">
        </div>
    </div>
</form>

<?php
require 'tpl/footer.php';
