<h1>Process Queue</h1>

<table class="table table-striped">

    <tr>
        <td>
            <b>Id</b>
        </td>
        <td>
            <b>User</b>
        </td>
        <td>
            <b>Status</b>
        </td>
        <td>
            <b>url</b>
        </td>
        <td>
            <b>File Location</b>
        </td>
        <td width="120px">
            <b>Action</b>
        </td>
    </tr>

    {section name=i loop=$process_queue}
    <tr>
        <td align="left">{$process_queue[i].id}</td>
        <td><a href="{$baseurl}/admin/user_view.php?user_id={$process_queue[i].user_id}">{$process_queue[i].user}</a></td>
        <td>
            {if $process_queue[i].status == "0"}
                Added to Queue
            {elseif $process_queue[i].status == "1"}
                Download Started
            {elseif $process_queue[i].status == "2"}
                Download Finished <br>(<a href="{$baseurl}/admin/convert.php?id={$process_queue[i].id}">Convert now</a>)
            {elseif $process_queue[i].status == "3"}
                Download Error
            {elseif $process_queue[i].status == "4"}
                Convert Started<br>
                (<a href="{$baseurl}/admin/check_convert_status.php?id={$process_queue[i].id}" target="_blank">Check Status</a>)
            {elseif $process_queue[i].status == "5"}
                <a href="{$baseurl}/admin/video_details.php?id={$process_queue[i].vid}">Convert Finished</a><br> (<a href="{$baseurl}/admin/convert.php?id={$process_queue[i].id}&reconvert=1">Convert again</a>)
            {elseif $process_queue[i].status == "6"}
                Convert Error (<a href="{$baseurl}/admin/convert.php?id={$process_queue[i].id}&reconvert=1">Convert now</a>)
            {elseif $process_queue[i].status == "7"}
                Convert Done, Finishing The Job<br>
                (<a href="{$baseurl}/admin/video_details.php?id={$process_queue[i].id}" target="_blank">Check Status</a>)
            {elseif $process_queue[i].status == "8"}
                Reconverting Video<br>
                (<a href="{$baseurl}/admin/check_convert_status.php?id={$process_queue[i].id}" target="_blank">Check Status</a>)


            {/if}
        </td>

        <td>{$process_queue[i].url}</td>

        <td>{$process_queue[i].file}</td>

        <td>
            <a href="{$base_url}/templates_c/convert_log_{$process_queue[i].id}.html" target="_blank" data-toggle="tooltip" data-placement="bottom" title="View Log">
                <span class="glyphicon glyphicon-file"></span>
            </a> &nbsp;
            <a href="{$baseurl}/admin/process_status_edit.php?id={$process_queue[i].id}&page={$page}" data-toggle="tooltip" data-placement="bottom" title="Edit Status">
                <span class="glyphicon glyphicon-edit"></span>
            </a> &nbsp;
            <a href="{$baseurl}/admin/process_queue.php?action=delete&id={$process_queue[i].id}" onclick="return confirm('Are you sure you want to remove ?');" data-toggle="tooltip" data-placement="bottom" title="Remove">
                <span class="glyphicon glyphicon-remove-circle"></span>
            </a>
        </td>

    </tr>

    {/section}

</table>

<div class="row">
    <div class="col-md-9">{$links}</div>
    <div class="col-md-3"><a href="{$baseurl}/admin/process_queue.php?action=delete_all" onclick="return confirm('Are you sure you want to remove ALL Process Queues? THIS CAN NOT BE UNDONE!');" data-toggle="tooltip" data-placement="bottom" title="Remove" class="btn btn-default btn-lg">Clear ALL From Queue</a></div>
</div>

{literal}
<script type="text/javascript">
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
{/literal}
