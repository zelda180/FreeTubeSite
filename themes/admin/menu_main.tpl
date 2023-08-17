<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#freetubesite-admin-main-menu">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="home.php">
        <img class="brand" src="{$img_css_url}/images/logo_admin.png">
      </a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="freetubesite-admin-main-menu">
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-wrench"></span> Configuration <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{$baseurl}/admin/settings.php">Site Settings</a></li>
                    <li><a href="{$baseurl}/admin/settings_video.php">Video Settings</a></li>
                    <li><a href="{$baseurl}/admin/settings_signup.php">Signup Settings</a></li>
                    <li><a href="{$baseurl}/admin/settings_player.php">Player Settings</a></li>
                    <li><a href="{$baseurl}/admin/settings_miscellaneous.php">Miscellaneous</a></li>
                    <li><a href="{$baseurl}/admin/settings_home.php">Home Page</a></li>
                    <li class="divider"></li>
                    <li><a href="{$baseurl}/admin/advertisements.php">Advertisements</a></li>
                    <li><a href="{$baseurl}/admin/email_templates.php">Email Templates</a></li>
                    <li class="divider"></li>
                    <li><a href="{$baseurl}/admin/server_manage.php">List Server</a></li>
                    <li><a href="{$baseurl}/admin/server_add.php">Add Server</a></li>
               </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span>  Users <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{$baseurl}/admin/users.php">All Users</a></li>
                    <li><a href="{$baseurl}/admin/users.php?a=Active">Active Users</a></li>
                    <li><a href="{$baseurl}/admin/users.php?a=Inactive">Inactive Users</a></li>
                    <li><a href="{$baseurl}/admin/users.php?a=Suspended">Suspend Users</a></li>
                    <li><a href="{$baseurl}/admin/user_search.php">Search Users</a></li>
                    <li><a href="{$baseurl}/admin/mail_users.php?a=user">Email Users</a></li>
                    <li><a href="{$baseurl}/admin/user_add.php">Add User</a></li>
               </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-facetime-video"></span>  Videos <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{$baseurl}/admin/videos.php">All Videos</a></li>
                    <li><a href="{$baseurl}/admin/video_approve.php">Approve Videos</a></li>
                    <li><a href="{$baseurl}/admin/videos.php?a=public">Public Videos</a></li>
                    <li><a href="{$baseurl}/admin/videos.php?a=private">Private Videos</a></li>
                    {if $family_filter}
                    <li><a href="{$baseurl}/admin/videos.php?a=adult">Adult Videos</a></li>
                    {/if}
                    <li><a href="{$baseurl}/admin/video_inactive.php">Inactive Videos</a></li>
                    <li><a href="{$baseurl}/admin/video_featured.php">Featured Videos</a></li>
                    <li><a href="{$baseurl}/admin/video_feature_requests.php">Feature Requests</a></li>
                    <li><a href="{$baseurl}/admin/videos_inappropriate.php">Flagged Videos</a></li>
                    <li><a href="{$baseurl}/admin/comment.php">Manage Comments</a></li>
                    <li><a href="{$baseurl}/admin/video_user_deleted.php">User Deleted Videos</a></li>
                    <li><a href="{$baseurl}/admin/video_search.php">Search Videos</a></li>
                    <li><a href="{$baseurl}/admin/tags.php">Tags</a></li>
                    <li><a href="{$baseurl}/admin/video_local.php">Local Videos</a></li>
                    <li><a href="{$baseurl}/admin/videos.php?a=embedded">Embedded Videos</a></li>
               </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-save"></span> Add Videos <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{$baseurl}/admin/import_youtube_video.php">Add Youtube Video</a></li>
                    <li><a href="{$baseurl}/admin/import_embed_video.php">Add Embed Video</a></li>
                    <li><a href="{$baseurl}/admin/import_remote_video.php">Add Remote Video (Hotlink)</a></li>
                    <li><a href="{$baseurl}/admin/import_bulk.php">Bulk Import</a></li>
                    <li><a href="{$baseurl}/admin/import_video.php">Import Video</a></li>
                    <li><a href="{$baseurl}/admin/import_folder.php">Import Folder</a></li>
                </ul>
            </li>

            <li><a href="{$baseurl}/admin/process_queue.php"><span class="glyphicon glyphicon-folder-open"></span> Process Queue</a></li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span> Manage <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{$baseurl}/admin/channels.php">View Channels</a></li>
                    <li><a href="{$baseurl}/admin/channel_add.php">Add Channels</a></li>
                    <li><a href="{$baseurl}/admin/channel_search.php">Search Channels</a></li>
                    <li><a href="{$baseurl}/admin/channel_sort.php">Sort Channels</a></li>
                    <li class="divider"></li>
                    <li><a href="{$baseurl}/admin/groups.php">All Groups</a></li>
                    <li><a href="{$baseurl}/admin/groups.php?a=public">Public Groups</a></li>
                    <li><a href="{$baseurl}/admin/groups.php?a=private">Private Groups</a></li>
                    <li><a href="{$baseurl}/admin/groups.php?a=protected">Protected Groups</a></li>
                    <li><a href="{$baseurl}/admin/group_search.php">Search Groups</a></li>
                    <li class="divider"></li>
                    <li><a href="{$baseurl}/admin/poll_list.php">View Polls</a></li>
                    <li><a href="{$baseurl}/admin/poll_add.php">Add New Poll</a></li>
                    <li class="divider"></li>
                    <li><a href="{$baseurl}/admin/page.php">List Pages</a></li>
                    <li><a href="{$baseurl}/admin/page_add.php">Add Page</a></li>
                    <li class="divider"></li>
                    <li><a href="{$baseurl}/admin/bad_words.php">Bad Words</a></li>
                    <li><a href="{$baseurl}/admin/reserve_user_name.php">Reserve User Name</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-plus"></span> More <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{$baseurl}"><span class="glyphicon glyphicon-home"></span> Site Home</a></li>
                    {if $enable_package eq "yes"}
                    <li><a href="{$baseurl}/admin/payments.php">Payments</a></li>
                    <li><a href="{$baseurl}/admin/packages.php">Packages</a></li>
                    <li><a href="{$baseurl}/admin/package_add.php">Add New Package</a></li>
                    <li><a href="{$baseurl}/admin/subscription_extend.php">Extend Subscription</a></li>
                    <li><a href="{$baseurl}/admin/subscription_edit.php">Edit Subscription</a></li>
                    <li class="divider"></li>
                    {/if}
                    <li><a href="{$baseurl}/admin/sitemap.php"><span class="glyphicon glyphicon-search"></span> Site Map</a></li>
                    <li><a href="{$baseurl}/admin/update_counters.php"><span class="glyphicon glyphicon-eye-open"></span> Update Counters</a></li>
                    <li><a href="{$baseurl}/admin/tags_regenerate.php"><span class="glyphicon glyphicon-tags"></span> Regenerate Tags</a></li>
                    <li><a href="{$baseurl}/admin/thumbs_regenerate.php"><span class="glyphicon glyphicon-picture"></span> Regenerate Thumbs</a></li>
                    <li><a href="{$baseurl}/admin/users_inactive_delete.php"><span class="glyphicon glyphicon-remove-sign"></span> Delete Inactive Users</a></li>
                    <li><a href="phpinfo.php"><span class="glyphicon glyphicon-info-sign"></span> View PHP Info</a></li>
                    {if $episode_enable eq '1'}
                    <li class="divider"></li>
                    <li><a href="{$baseurl}/admin/episodes.php"><span class="glyphicon glyphicon-time"></span> Episodes</a></li>
                    {/if}
                </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav pull-right">
            <li class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle">
                    <span class="glyphicon glyphicon-user"></span>
                    Admin <span class="caret"></span>
                </a>
                <ul role="menu" class="dropdown-menu">
                    <li><a href="{$baseurl}/admin/admin_log.php"><span class="glyphicon glyphicon-book"></span> Admin Log</a></li>
                    <li><a href="{$baseurl}/admin/change_password.php"><span class="glyphicon glyphicon-wrench"></span> Admin Password</a></li>
                    <li class="divider"></li>
                    <li><a href="{$baseurl}/admin/logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                </ul>
            </li>
        </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
