{if $todo eq "folder_empty"}

<div class="page-header">
    <h1>
        Multi Import
        <br>
        <small>To Import ALL videos from folder, do the following</small>
    </h1>
</div>

<ol class="txt">
	<li>Create a folder "import" inside templates_c folder (If Folder Is Not There).</li>
	<li>chmod 755 (or 777) import folder you just created.</li>
	<li>Upload the videos to import folder with FTP.</li>
	<li>chmod 755 (or 777) all uploaded videos.</li>
</ol>

<p><b>Import Folder Path:</b> {$base_dir}/templates_c/import</p>

{elseif $todo eq ""}

<div class="page-header">
    <h1>
        Multi Import
        <br>
        <small>Multi import will import all videos uploaded to folder <strong>templates_c/import</strong></small>
    </h1>
</div>

<form method="post" action="" id="multi-import" name="multi-import" class="form-horizontal">

    <input type="hidden" name="video_name" value="{$video_name}" />

    {include file="admin/import_form.tpl"}

    <div  class="form-group">
        <div class="col-md-2 col-md-offset-2">
            <input type="submit" name="submit" value="Import Videos" class="btn btn-default btn-lg">
        </div>
    </div>

</form>

{/if}
