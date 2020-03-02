<h1 class="con_heading">{$LANG.ADD_PHOTOS}</h1>
{if $total_no_pub}
<p class="usr_photos_add_limit">{$LANG.NO_PUBLISHED_PHOTO}: <a href="/users/{$user_login}/photos/submit">{$total_no_pub|spellcount:$LANG.PHOTO:$LANG.PHOTO2:$LANG.PHOTO10}</a></p>
{/if}
{if !$stop_photo}
	{if $uload_type == 'multi'}

        {add_js file='includes/fileuploader/fileuploader.js'}

    {if $max_limit}
    <p class="usr_photos_add_limit">{$LANG.YOU_CAN_UPLOAD} <strong>{$max_files}</strong> {$LANG.PHOTO_SHORT}</p>
    {/if}

        <div id="album-photos-widget">

            <div class="previews_list"></div>

            <div class="preview_template" style="display:none">
                <div class="thumb">
                    <img class="img-thumbnail" src="" border="0" />
        </div>
            </div>

            <div id="photos-uploader"></div>

            {literal}
            <script>
                $(function() {
                    createUploader('{/literal}{$upload_url}', '{$sess_id}', '{$upload_complete_url}'{literal});
                });
            </script>
            {/literal}
        </div>

        <div id="divStatus" style="display:none">
            <a href="{$upload_complete_url}" id="continue">{$LANG.CONTINUE}</a>
        </div>

        <p class="text-danger">{$LANG.TEXT_TO_NO_FLASH} <a href="/users/addphotosingle.html">{$LANG.PHOTO_ST_UPLOAD}.</a></p>
    
{elseif $uload_type == 'single'}
        {if $max_limit}
         <p class="text-danger">{$LANG.YOU_CAN_UPLOAD} <strong>{$max_files}</strong> {$LANG.PHOTO_SHORT}</p>
        {/if}

        <form id="usr_photos_upload_form" enctype="multipart/form-data" action="/users/photos/upload" method="POST">
            <p>{$LANG.SELECT_UPLOAD_FILE}: </p>
            <input name="Filedata" type="file" id="picture" size="30" />
            <input name="upload" type="hidden" value="1"/>
            <div style="margin-top:5px">
                <strong>{$LANG.TYPE_FILE}:</strong> gif, jpg, jpeg, png
            </div>

            <div>
                <input type="submit" value="{$LANG.UPLOAD}">
                <input type="button" onclick="window.history.go(-1);" value="{$LANG.CANCEL}"/>
            </div>
        </form>
		<p class="text-danger">{$LANG.TEXT_TO_TO_FLASH} <a href="/users/addphoto.html">{$LANG.PHOTO_FL_UPLOAD}.</a></p>
    {/if}
{else}
<p class="text-danger">{$LANG.FOR_ADD_PHOTO_TEXT}</p>
{/if}