<h3 style="border-bottom: solid 1px gray">
	<strong>{$LANG.STEP_2}</strong>: {$LANG.FILE_UPLOAD}
</h3>
{if !$stop_photo}
{if $uload_type == 'multi'}

    {add_js file='includes/fileuploader/fileuploader.js'}

    {if $max_limit}
        <p class="usr_photos_add_limit">{$LANG.YOU_CAN_UPLOAD} <strong>{$max_files}</strong> {$LANG.PHOTO}</p>
    {/if}

    <div id="album-photos-widget">

        <div class="previews_list"></div>

        <div class="preview_template" style="display:none">
            <div class="thumb">
                <img class="img-thumbnail" src="" border="0" />
            </div>
            <div class="control">
                <a href="javascript:" class="del-link">Удалить</a>
            </div>
        </div>

        <div id="photos-uploader"></div>

        <div id="savenext">
            После загрузки файлов <a href="/photos/{$album.id}">продолжить</a>
        </div>

        {literal}
        <script>
            $(function() {
                createUploader('{/literal}{$upload_url}/{$album.id}', '{$sess_id}'{literal});
            });
        </script>
        {/literal}
    </div>

{elseif $uload_type == 'single'}
        {if $max_limit}
         <p class="usr_photos_add_limit">{$LANG.YOU_CAN_UPLOAD} <strong>{$max_files}</strong> {$LANG.PHOTO}</p>
        {/if}

        <form enctype="multipart/form-data" action="{$upload_url}" method="POST">

            <p>{$LANG.SELECT_FILE_TO_UPLOAD}: </p>
                    <input name="Filedata" type="file" id="picture" size="30" />
                    <input name="upload" type="hidden" value="1"/>
                    <input name="album_id" type="hidden" value="{$album.id}"/>
                    <input name="sess_id" type="hidden" value="{$sess_id}"/>

            <div style="margin:5px 0">
                <strong>{$LANG.ALLOW_FILE_TYPE}:</strong> gif, jpg, jpeg, png
            </div>

            <p>
                <input type="submit" value="{$LANG.LOAD}">
                <input type="button" onclick="window.history.go(-1);" value="{$LANG.CANCEL}"/>
            </p>
        </form>
{/if}
{else}
<p class="usr_photos_add_limit">{$LANG.MAX_UPLOAD_IN_DAY}</p>
{/if}