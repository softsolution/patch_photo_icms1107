$(function(){
  photos = {
    publishPhoto: function(photo_id) {
      $.post('/photos/publish'+photo_id+'.html', {}, function(data){
		if(data == 'ok'){
			$('#pub_photo_link').hide();
			$('#pub_photo_wait').hide();
			$('#pub_photo_date').fadeIn();
		} else {
			core.alert(LANG_NO_PUBLISH, LANG_ERROR);
		}
      });
    },
    movePhoto: function(photo_id) {
      core.message(LANG_MOVE_PHOTO);
      $.post('/photos/movephoto'+photo_id+'.html', {}, function(data){
		if(data.error == false){
			$('#popup_message').html(data.html);
			$('#popup_progress').hide();
			$('#popup_ok').show();
			$('#popup_ok').click(function(){
				$('#popup_ok').prop('disabled', true);
				$('#popup_progress').show();
				var options = {
					success: photos.domovePhoto
				};
				$('#move_photo_form').ajaxSubmit(options);
			});
		} else {
			core.alert(data.text, LANG_ERROR);
		}
      }, 'json');
    },
    domovePhoto: function(result, statusText, xhr, $form){
		$('#popup_progress').hide();
		if(statusText == 'success'){
			if(result.error == false){
				window.location.href = result.redirect;
			} else {
				core.alert(result.text, LANG_ERROR);
			}
		} else {
			core.alert(statusText, LANG_ERROR);
		}
    },
    editPhoto: function(photo_id) {
      core.message(LANG_EDIT_PHOTO);
      $.post('/photos/editphoto'+photo_id+'.html', {}, function(data){
		if(data.error == false){
			$('#popup_message').html(data.html);
			$('#popup_progress').hide();
			$('#popup_ok').val(LANG_SAVE).show();
			$('#popup_ok').click(function(){
				$('#popup_ok').prop('disabled', true);
				$('#popup_progress').show();
				var options = {
					success: photos.doeditPhoto
				};
				$('#edit_photo_form').ajaxSubmit(options);
			});
		} else {
			core.alert(data.text, LANG_ERROR);
		}
      }, 'json');
    },
    doeditPhoto: function(result, statusText, xhr, $form){
		$('#popup_progress').hide();
		if(statusText == 'success'){
			if(result.error == false){
				window.location.href = result.redirect;
			}
		} else {
			core.alert(statusText, LANG_ERROR);
		}
    },
    deletePhoto: function(photo_id, csrf_token) {
		core.confirm(LANG_YOU_REALLY_DELETE_PHOTO, null, function(){
			$.post('/photos/delphoto'+photo_id+'.html', {csrf_token: csrf_token}, function(result){
				if(result.error == false){
					window.location.href = result.redirect;
				}
			}, 'json');
		});
    },
	removePhoto: function(id) {
		var widget = $('#album-photos-widget');
		var url = '/photos/delphoto/' + id;
		$.post(url, {}, function(result){
		  if (!result.success) { return; }
		  $('.preview[rel='+id+']', widget).fadeOut(400, function() { $(this).remove(); });
		}, 'json');
		return false;
	}
  }
});

function createUploader(upload_url, sess_id, upload_complete_url){
	uploader = new qq.FileUploader({
		element: document.getElementById('photos-uploader'),
		action: upload_url,
		debug: false,
		params: {sess_id: sess_id},
		onComplete: function(id, file_name, result) {

			if (!result.success) { return; }
			var widget = $('#album-photos-widget');
			var preview_block = $('.preview_template', widget).clone().removeClass('preview_template').addClass('preview').attr('rel', result.id).show();

			$('img', preview_block).attr('src', result.url);
			$('a', preview_block).click(function() {
				photos.removePhoto(result.id);
			});

			$('.previews_list', widget).append(preview_block);

			if(upload_complete_url && result.is_limit){
				window.location.href = upload_complete_url;
			}

		}

	});
}