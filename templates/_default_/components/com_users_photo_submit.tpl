<h1 class="con_heading">{$LANG.PHOTOS_CONFIG}</h1>

<script type="text/javascript">
    function togglePhoto(id){
        if ($('#delete'+id).prop('checked')){
            $('#photo'+id+' .text-input').prop('disabled', true);
            $('#photo'+id+' select').prop('disabled', true);
        } else {
            $('#photo'+id+' .text-input').prop('disabled', false);
            $('#photo'+id+' select').prop('disabled', false);
        }
    }
</script>

<form action="" method="post">

    <div id="usr_photos_upload_album" class="table-responsive">
        <table class="table table-striped">
            {if $albums}
            <tr>
                <td width="30%"><div class="checkbox"><label for="new_album_0"><input type="radio" name="new_album" id="new_album_0" value="0" checked="checked" onclick="$('#description').hide();" /> {$LANG.SAVE_TO_ALBUM}:</label></div></td>
                <td style="padding-left: 10px" colspan="3">
                    <select name="album_id" class="select-input" style="width:100%;">
                        {foreach key=ak item=album from=$albums}
                            <option value="{$album.id}" {if $album_id == $album.id} selected="selected"{/if}>{$album.title}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            {/if}
            <tr>
                <td width="30%"><div class="checkbox"><label for="new_album_1"><input type="radio" name="new_album" id="new_album_1" value="1" {if !$albums}checked="checked"{/if} onclick="$('#description').show();" /> {$LANG.CREATE_NEW_ALBUM}:</label></div></td>
                <td style="padding:0px 10px">
                    <input style="width:100%;" type="text" class="text-input" name="album_title" onclick="$('#description').show();$('#new_album_1').prop('checked', true);" />
                </td>
                <td>
                    <select style="width:100%;" name="album_allow_who" id="album_allow_who">
                        <option value="all">{$LANG.SHOW} {$LANG.TO_ALL}</option>
                        <option value="registered">{$LANG.SHOW} {$LANG.TO_REGISTERED}</option>
                        <option value="friends">{$LANG.SHOW} {$LANG.TO_MY_FRIEND}</option>
                    </select>
                </td>
            </tr>
            <tr id="description" {if $albums}style="display:none;"{/if} >
                <td style="padding-left:45px;">{$LANG.ALBUM_DESCRIPTION}</td>
                <td colspan="2">
					<textarea name="description" class="text-input" style="width:100%;height:50px;margin-top:4px;"></textarea>
                </td>
            </tr>
        </table>
    </div>

    <div class="usr_photos_submit_list">
        {foreach key=pk item=photo from=$photos}
		<div class="row {cycle values="rowa2,rowa1"} usr_photos_submit_one" id="photo{$photo.id}">
            <div class="col-sm-2 col-xs-4 media-gird" align="center">
                <img src="/images/users/photos/small/{$photo.imageurl}" class="media-object" />
	            <input type="checkbox" name="delete[]" value="{$photo.id}" id="delete{$photo.id}" onclick="togglePhoto({$photo.id})"/> <label for="delete{$photo.id}">{$LANG.DELETE}</label>
			</div>
            <div class="col-sm-10 col-xs-8">
<input type="text" name="title[{$photo.id}]" value="{$photo.title|escape:'html'}" class="text-input" style="width:100%;" placeholder="{$LANG.TITLE}" /><br />
<input style="width:100%;" type="text" name="desc[{$photo.id}]" value="{$photo.description|escape:'html'}" class="text-input" placeholder="{$LANG.DESCRIPTION}" /><br />
<select style="width:100%;" name="allow[{$photo.id}]">
    <option value="all" {if $photo.allow_who=='all'}selected="selected"{/if}>{$LANG.SHOW} {$LANG.TO_ALL}</option>
    <option value="registered" {if $photo.allow_who=='registered'}selected="selected"{/if}>{$LANG.SHOW} {$LANG.TO_REGISTERED}</option>
    <option value="friends" {if $photo.allow_who=='friends'}selected="selected"{/if}>{$LANG.SHOW} {$LANG.TO_MY_FRIEND}</option>
                                    </select>
            </div>
        </div>
        {/foreach}
    </div>
    <div id="usr_photos_submit_btn" class="pull-right" style="margin-top:20px;">
    	<input type="hidden" name="is_edit" value="{$is_edit}" />
        <input type="submit" name="submit" value="{$LANG.SAVE}" />&nbsp;&nbsp;&nbsp;{$LANG.AND_GO_TO_ALBUM}
    </div>
</form>
<div style="clear:both;"></div>