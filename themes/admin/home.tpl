<h1>Site Statistics</h1>
{if Config::get('youtube_api_key') eq ""}
    <div class="alert alert-info">
        <span class="glyphicon glyphicon-warning-sign"></span>
        <a href="{$baseurl}/admin/settings_miscellaneous.php#youtube_api_key"  class="alert-link">
            You need to set Youtube API Key to add youtube videos.
        </a>
    </div>
{/if}

{if isset($show_inactive_users_warning)}
    <div class="alert alert-warning">
        <span class="glyphicon glyphicon-warning-sign"></span>
        <a href="{$baseurl}/admin/user_inactive_manage.php" class="alert-link">
            You have {$total_users_inactive}
            {if $total_users_inactive eq 1}user{else}users{/if} waiting to be activated.
        </a>
    </div>
{/if}

{if isset($show_recaptcha_warning)}
    <div class="alert alert-danger">
        <span class="glyphicon glyphicon-warning-sign"></span>
        <a href="{$baseurl}/admin/settings_signup.php" class="alert-link">
            Set reCaptcha Site Key and reCaptcha Secret Key to stop spam signups.
        </a>
    </div>
{/if}

<table class="table table-bordered table-striped">
<tr class="tablerow1">
	<td><b>Number of Videos:</b></td>
	<td><b>{$total_video}</b></td>
</tr>

<tr class="tablerow2">
	<td><b>Number of Public Videos:</b></td>
	<td><b>{$total_public_video}</b></td>
</tr>

<tr class="tablerow1">
	<td><b>Number of Private Videos:</b></td>
	<td><b>{$total_private_video}</b></td>
</tr>

<tr class="tablerow2">
	<td><b>Number of Users:</b></td>
	<td><b>{$total_users}</b></td>
</tr>

<tr class="tablerow1">
	<td><b>Number of Channels:</b></td>
	<td><b>{$total_channel}</b></td>
</tr>

<tr class="tablerow2">
	<td><b>Number of Groups:</b></td>
	<td><b>{$total_groups}</b></td>
</tr>
</table>

<h2>Version Information</h2>
{if $freetubesite_status eq "old"}
<div class="alert alert-danger">
You are using old version of FreeTubeSite ({$freetubesite_version})<br />
You must upgrade to FreeTubeSite {$latest_version}<br />
More information on FreeTubeSite {$latest_version} available at <a href="https://github.com/zelda180/FreeTubeSite/releases" target="_blank">https://github.com/zelda180/FreeTubeSite/releases</a>
</div>
{else}
<div class="alert alert-success">
You are using FreeTubeSite version: {$freetubesite_version} (DB Version: {$version})
</div>
{/if}
