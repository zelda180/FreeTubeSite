<div class="page-header">
    <h1>Player Settings</h1>
</div>

<form method="post" action="" class="form-horizontal" role="form">
    <fieldset>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="player_autostart">Auto Play Video:</label>
        <div class="col-sm-5">
            <div class="input-group">
            <select class="form-control" name="player_autostart" id="player_autostart">
                <option value="1" {if $player_autostart =='1'}selected="selected"{/if}>Yes</option>
                <option value="0" {if $player_autostart =='0'}selected="selected"{/if}>No</option>
            </select>
                <div class="input-group-addon">
                    <a href="https://github.com/zelda180/FreeTubeSite/wiki/Player-Settings#player_autostart" target="_blank"><span class="glyphicon glyphicon-question-sign"></span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="player_bufferlength">Video buffer time in seconds:</label>
        <div class="col-sm-5">
            <div class="input-group">
                <input class="form-control" type="text" name="player_bufferlength" id="player_bufferlength" value="{$player_bufferlength}" size="5" />
                <div class="input-group-addon">
                    <a href="https://github.com/zelda180/FreeTubeSite/wiki/Player-Settings#player_bufferlength" target="_blank"><span class="glyphicon glyphicon-question-sign"></span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="player_width">Player Width:</label>
        <div class="col-sm-5">
            <div class="input-group">
                <input class="form-control" type="text" name="player_width" id="player_width" value="{$player_width}" size="5" />
                <div class="input-group-addon">
                    <a href="https://github.com/zelda180/FreeTubeSite/wiki/Player-Settings#player_width" target="_blank"><span class="glyphicon glyphicon-question-sign"></span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="player_height">Player Height:</label>
        <div class="col-sm-5">
            <div class="input-group">
                <input class="form-control" type="text" name="player_height" id="player_height" value="{$player_height}" size="5" />
                <div class="input-group-addon">
                    <a href="https://github.com/zelda180/FreeTubeSite/wiki/Player-Settings#player_height" target="_blank"><span class="glyphicon glyphicon-question-sign"></span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="freetubesite_player">Video player:</label>
        <div class="col-sm-5">
            <div class="input-group">
            <select class="form-control" name="freetubesite_player" id="freetubesite_player">
                <option value="videojs" {if $freetubesite_player == 'videojs'}selected="selected"{/if}>VideoJS Player</option>
                <option value="JW Player" {if $freetubesite_player == 'JW Player'}selected="selected"{/if}>JW Player</option>
                <option value="StrobeMediaPlayback" {if $freetubesite_player == 'StrobeMediaPlayback'}selected="selected"{/if}>StrobeMediaPlayback</option>
            </select>
                <div class="input-group-addon">
                    <a href="https://github.com/zelda180/FreeTubeSite/wiki/Player-Settings#freetubesite_player" target="_blank"><span class="glyphicon glyphicon-question-sign"></span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="youtube_player">Youtube video player:</label>
        <div class="col-sm-5">
            <div class="input-group">
            <select class="form-control" name="youtube_player" id="youtube_player">
                <option value="youtube" {if $youtube_player =='youtube'}selected="selected"{/if}>Youtube Player</option>
                <option value="freetubesite" {if $youtube_player =='freetubesite'}selected="selected"{/if}>FreeTubeSite Player</option>
            </select>
                <div class="input-group-addon">
                    <a href="https://github.com/zelda180/FreeTubeSite/wiki/Player-Settings#youtube_player" target="_blank"><span class="glyphicon glyphicon-question-sign"></span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="watermark_url">Watermark URL:</label>
        <div class="col-sm-5">
            <div class="input-group">
                <input class="form-control" type="text" name="watermark_url" id="watermark_url" value="{$watermark_url}">
                <div class="input-group-addon">
                    <a href="https://github.com/zelda180/FreeTubeSite/wiki/Player-Settings#watermark_url" target="_blank"><span class="glyphicon glyphicon-question-sign"></span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="watermark_image_url" class="col-sm-3 control-label">Watermark Image Location:</label>
        <div class="col-sm-5">
            <div class="input-group">
                <input class="form-control" type="text" name="watermark_image_url" id="watermark_image_url" value="{$watermark_image_url}">
                <div class="input-group-addon">
                    <a href="https://github.com/zelda180/FreeTubeSite/wiki/Player-Settings#watermark_image_location" target="_blank"><span class="glyphicon glyphicon-question-sign"></span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-5">
            <button type="submit" name="submit" class="btn btn-default btn-lg">Update</button>
        </div>
    </div>

    </fieldset>

</form>
