{if !empty($video)}

<div class="page-header">
    <h1>{$video.video_title}</h1>
</div>

<div class="col-md-8">
    <div class="row">
        <div class="embed-responsive embed-responsive-16by9">{$FREETUBESITE_PLAYER}</div>
    </div>
</div>
<div class="clearfix">&nbsp;</div>

<p class="video-link">
    <a href ="{$base_url}/view/{$video.video_id}/{$video.video_seo_name}/" target="_blank">
        {$base_url}/view/{$video.video_id}/{$video.video_seo_name}/
    </a>
</p>

<div class="row">
    <div class="col-md-8">

<table class="table table-hover table-striped">

<tr>
    <td width="160px">Video ID</td>
    <td>{$video.video_id}</td>
</tr>

<tr>
    <td>Server ID</td>
    <td>{$video.video_server_id}</td>
</tr>

<tr>
    <td>Title</td>
    <td>{$video.video_title}</td>
</tr>

<tr>
    <td>Added by</td>
    <td>
    {if $video.video_user_id eq 0}
        Deleted Video
    {else}
        {insert name=id_to_name assign=uname un=$video.video_user_id}
        <a href="{$baseurl}/admin/user_view.php?user_id={$video.video_user_id}">{$uname}</a>
    {/if}
    </td>
</tr>

<tr>
     <td>Description</td>
     <td>{$video.video_description}</td>
</tr>

<tr>
     <td>Tags</td>
     <td>{$video.video_keywords}</td>
</tr>

<tr>
    <td>Channel</td>
    <td>
    {insert name=video_channel assign=channel vid=$video.video_id}
    {section name=j loop=$channel}
        {$channel[j].channel_name},
    {/section}
    </td>
</tr>

<tr>
   <td>Duration</td>
   <td>{$video.video_length}</td>
</tr>

<tr>
   <td>Type (public/private)</td>
   <td>{$video.video_type}</td>
</tr>

<tr>
   <td>vType (Our Server, Embeded, YouTube)</td>
   <td>{$video.video_vtype}</td>
</tr>

<tr>
   <td>Rating</td>
   <td>{$video.video_rate}</td>
</tr>

<tr>
   <td>Is Video Active? (1.Yes 2.No)</td>
   <td>{$video.video_active}</td>
</tr>

<tr>
   <td>Is Video Featured?</td>
   <td>{$video.video_featured}</td>
</tr>

<tr>
   <td>Can Be Commented?</td>
   <td>{$video.video_allow_comment}</td>
</tr>

<tr>
    <td>Can Be Rated?</td>
    <td>{$video.video_allow_rated}</td>
</tr>

<tr>
    <td>Can Be eEmbaded?</td>
    <td>{if $video.video_allow_embed eq "enabled"}Yes{else}No{/if}</td>
</tr>

{if $family_filter eq "1"}
<tr>
    <td>Adult Video?</td>
    <td>{if $video.video_adult eq "1"}Yes{else}No{/if}</td>
</tr>
{/if}

</table>

    </div>
</div>

<div class="btn-group">
    {if $video_type eq "0"}
    <a href="{$baseurl}/admin/video_rename_flv.php?id={$video.video_id}" class="btn btn-default btn-lg">Rename Video</a>
    <a href="{$baseurl}/admin/video_thumb.php?id={$video.video_id}" class="btn btn-default btn-lg">Create Thumb</a>
    {if $reprocess eq "1"}
    <a href="./convert.php?id={$reprocess_id}&reconvert=1" class="btn btn-default btn-lg">Convert Again</a>
    {/if}
    {/if}
    <a href="{$baseurl}/admin/video_edit.php?video_id={$video.video_id}&a={$a}&page={$smarty.request.page}" class="btn btn-default btn-lg">Edit</a>
    <a href="{$baseurl}/admin/video_delete.php?id={$video.video_id}" onclick="Javascript:return confirm('Are you sure you want to delete?');" class="btn btn-danger btn-lg">Delete</a>
</div>

{/if}
