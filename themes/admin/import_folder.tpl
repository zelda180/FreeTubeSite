<div class="page-header">
    <h1>Import Folder</h1>
</div>

{if $folder_empty eq "1"}

	<p>To Import videos from folder, you need to do the following</p>

	<ol class="txt">
		<li>Create a folder named "import" inside templates_c folder (If Folder Is Not There).</li>
		<li>chmod 755 (or 777)import folder you just created.</li>
		<li>Upload the videos to import folder with FTP.</li>
		<li>chmod 755 (or 777) all uploaded videos.</li>
	</ol>

	<p><b>Import Folder Path:</b> {$base_dir}/templates_c/import</p>

{else}

    <p>Upload your videos to <b>{$base_dir}/templates_c/import</b> folder to import.</p>
    <p>Make sure folder import have read/write permission (chmod 755 or 777).</p>
    <hr />
    <table class="table table-striped table-hover">

        <tr>
            <td align="left">
                <b>Video Name</b>
            </td>
            <td align="center">
                <b>Action</b>
            </td>
        </tr>
        {section name=i loop=$import_video}
            <tr>
                <td align="left">
                    {$import_video[i][0]}
                </td>
                <td align="center">
                    <a href="{$baseurl}/admin/import_folder_form.php?action=import&video={$import_video[i][1]}">
                        Import
                    </a>
                </td>
            </tr>
        {/section}
    </table>

{/if}

<div>
    <a class="btn btn-default btn-lg" href="{$baseurl}/admin/import_folder_all.php">Import All Videos</a>
</div>
