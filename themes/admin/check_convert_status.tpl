{if $id_not_numeric eq "1"}
<h1>Video Convert Progress For Process Queue</h1><br>
    <div class="alert alert-warning">
        <span class="glyphicon glyphicon-warning-sign"></span>
            ID Must Be A Valid Number.
    </div>
{/if}

{if $vid_file_exists eq "0" && $id_not_numeric eq ""}
<h1>Video Convert Progress For Process Queue</h1><br>
    <div class="alert alert-danger">
        <span class="glyphicon glyphicon-warning-sign"></span>
            VIDEO CONVERT FILE DOSE NOT EXIST. Is Your Video Convert Command Running?
    </div>
{/if}

{if $progresst eq "1"}
<h1>Video Convert Progress For Process Queue {$vid} </h1><br>

<script type="text/javascript">
var myTimer = null;
//executes the script
    startRefresh();
//Shows progress bar
function refreshData() {
  $("#progress").load("{$baseurl}/admin/convert_progress.php?id={$vid}");
}
//function to stop the javascript refresh when stopRefresh() is sent
function stopRefresh() {
  clearInterval(myTimer);
}
//function to start the javascript refresh
function startRefresh() {
 myTimer = setInterval(refreshData, 1000);
}
</script>

<center>
    <div id="progress" class="alert alert-info">
        <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>
    </div></center>
{/if}
