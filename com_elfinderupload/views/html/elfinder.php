<?php
/**
 * A view to load the file uploader.
 *
 * @package Components\elfinderupload
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<style type="text/css">
.ui-selectable-helper {z-index: 410000;}
</style>
<script type="text/javascript">
$_(function(){$(".puploader").each(function(){
	var pfile = $(this),
		unique_id = pfile.data("elfinder_unique_id"),
		dialog_open = false;
	if (!unique_id) {
		unique_id = Math.floor(Math.random()*1000001);
		pfile.data("elfinder_unique_id", unique_id);
	}
	var show_uploader = function(){
		dialog_open = true;
		var dialog = $($("#p_muid-temp-uploader").html()).dialog({
			width: "auto",
			resizable: false,
			modal: true,
			zIndex: 400000,
			title: "File Upload",
			close: function(){
				dialog.dialog("destroy").remove();
				dialog_open = false;
			},
			buttons: {
				"Done": function(){
					dialog.dialog('close');
				}
			}
		});
		dialog.dialog("option", "position", "center");

		// Adapted from http://html5demos.com/dnd-upload
		var url = <?php echo json_encode(pines_url('com_elfinderupload', 'tempupload', array('request_id' => '__request_id__'))); ?>.replace("__request_id__", unique_id),
			holder = dialog.find('.p_muid-holder'),
			tests = {
				filereader: typeof FileReader != 'undefined',
				dnd: 'draggable' in document.createElement('span'),
				formdata: !!window.FormData,
				progress: "upload" in new XMLHttpRequest
			},
			support = {
				filereader: dialog.find('.p_muid-filereader'),
				formdata: dialog.find('.p_muid-formdata'),
				progress: dialog.find('.p_muid-progress')
			},
			previewTypes = {
				'image/jpeg': true,
				'image/gif': true,
				'image/png': true,
				'image/tiff': true
			},
			progress = dialog.find('.p_muid-uploadprogress'),
			fileupload = dialog.find('.p_muid-upload');

		$.each(["filereader", "formdata", "progress"], function(i, api){
			support[api].addClass(tests[api] === false ? 'p_muid-fail' : 'p_muid-hidden');
		});

		function previewfile(file) {
			if (tests.filereader === true && previewTypes[file.type] === true) {
				var reader = new FileReader();
				reader.onload = function (event) {
					var image = new Image();
					image.src = event.target.result;
					image.width = 250; // a fake resize
					holder.append(image);
				};

				reader.readAsDataURL(file);
			}  else {
				holder.append('<p>Uploaded '+$_.safe(file.name)+' '+(file.size ? (file.size/1024|0) + 'K' : '')+'</p>');
			}
		}

		function readfiles(files) {
			var formData = tests.formdata ? new FormData() : null;
			holder.html("");
			for (var i = 0; i < (pfile.hasClass("puploader-multiple") ? files.length : 1); i++) {
				if (tests.formdata)
					formData.append('file', files[i]);
				previewfile(files[i]);
			}

			// now post a new XHR request
			if (tests.formdata) {
				var xhr = new XMLHttpRequest();
				xhr.open('POST', url);
				xhr.onload = function(XHRPE){
					progress.val(100).html(100);
					if (XHRPE.target.readyState !== 4)
						return;
					var data = JSON.parse(XHRPE.target.response);
					if (!data)
						return;
					load_file(data);
					pfile.change();
				};

				if (tests.progress) {
					xhr.upload.onprogress = function (event) {
						if (event.lengthComputable) {
							var complete = (event.loaded / event.total * 100 | 0);
							progress.val(complete).html(complete);
						}
					};
				}

				xhr.send(formData);
			}
		}

		if (tests.dnd) {
			holder.get(0).ondragover = function(){ $(this).addClass('p_muid-hover'); return false; };
			holder.get(0).ondragend = function(){ $(this).removeClass('p_muid-hover'); return false; };
			holder.get(0).ondrop = function(e){
				$(this).removeClass('p_muid-hover');
				e.preventDefault();
				readfiles(e.dataTransfer.files);
			};
		} else {
			holder.hide();
			fileupload.find('span').toggle();
		}
		fileupload.find('input').on('change', function(){
			readfiles(this.files);
		});
	};
	var show_finder = function(){
		dialog_open = true;
		var start_path = pfile.val().replace(/[^/]+$/, ""),
			url = <?php echo json_encode(pines_url('com_elfinder', 'connector', array('start_path' => '__start_path__'))); ?>.replace("__start_path__", start_path);
		pfile.elfdlg = $('<div/>').css("overflow", "visible").dialog({
			width: 900,
			modal: true,
			zIndex: 400000,
			title: "Choose File"+(pfile.hasClass("puploader-multiple") ? "(s)" : "")+(pfile.hasClass("puploader-folders") ? " or Folder"+(pfile.hasClass("puploader-multiple") ? "(s)" : "") : ""),
			close: function(){
				pfile.elfdlg.unbind("dialogopen").unbind("open."+pfile.elf.namespace).unbind("select."+pfile.elf.namespace).elfinder("destroy").dialog("destroy").remove();
				delete pfile.elfdlg;
				delete pfile.elf;
				dialog_open = false;
			}
		});
		pfile.elfdlg.dialog("widget").css("overflow", "visible");
		pfile.elf = pfile.elfdlg.elfinder({
			url: url,
			height: <?php echo (int) $_->config->com_elfinder->default_height; ?>,
			resizable : false,
			commandsOptions: {
				getfile: {
					onlyURL: false,
					multiple: pfile.hasClass("puploader-multiple"),
					folders: pfile.hasClass("puploader-folders")
				}
			},
			getFileCallback: function(file) {
				load_file(file);
				pfile.elfdlg.dialog("close");
				pfile.change();
			}
		}).elfinder('instance');
		pfile.elfdlg.dialog("option", "position", "center");
	};
	var load_file = function(file){
		var title, content = "", value = "";
		if ($.isArray(file)) {
			title = "Multiple Selections";
			$.each(file, function(i, file){
				content += '<h4'+(i > 0 ? ' style="margin-top: 2em;"' : '')+'>'+(file.mime == "directory" ? "Folder" : "File")+": "+$_.safe(file.name)+'</h4>';
				if (file.tmb)
					content += '<div style="text-align: center; margin-bottom: 1em;"><span class="thumbnail" style="display: inline-block;"><img alt="Thumbnail" src="'+$_.safe(file.tmb)+'" /></span></div>';
				content += '<div style="margin-bottom: .5em;">Type: '+$_.safe(file.mime)+'</div><div style="margin-bottom: .5em;">Path: <tt>'+$_.safe(file.path)+'</tt></div><div>URL: <tt>'+$_.safe(file.url)+'</tt></div>';
				value += (value ? "//" : "")+file.url;
			});
		} else {
			title = "Selected "+(file.mime == "directory" ? "Folder" : "File")+": "+$_.safe(file.name);
			if (file.tmb)
				content = '<div style="text-align: center; margin-bottom: 1em;"><span class="thumbnail" style="display: inline-block;"><img alt="Thumbnail" src="'+$_.safe(file.tmb)+'" /></span></div>';
			content += '<div style="margin-bottom: .5em;">Type: '+$_.safe(file.mime)+'</div><div style="margin-bottom: .5em;">Path: <tt>'+$_.safe(file.path)+'</tt></div><div>URL: <tt>'+$_.safe(file.url)+'</tt></div>';
			value = file.url;
		}
		pfile.val(value).attr({
			"data-original-title": title,
			"data-content": content
		}).popover({trigger: 'hover', placement: "bottom", html: true});
	};
	pfile.focus(function(){
		if (!dialog_open)
			$(this).hasClass("puploader-temp") ? show_uploader() : show_finder();
		pfile.blur();
	}).change(function(){
		if (pfile.val() == '')
			pfile.attr({"data-original-title": 'File Upload', "data-content": 'Click to upload files.'});
	});
	$('<button class="btn btn-default" type="button" style="margin-left: .5em">'+(pfile.hasClass("puploader-temp") ? 'Upload' : 'Browse')+'&hellip;</button>')
	.click(function(){pfile.focus()})
	.insertAfter(pfile);
});});
</script>
<style type="text/css">
.p_muid-holder { border: 10px dashed #ccc; width: 300px; min-height: 300px; margin: 20px auto;}
.p_muid-holder.p_muid-hover { border-color: #0c0; }
.p_muid-holder img { display: block; margin: 10px auto; }
.p_muid-holder p { margin: 10px; font-size: 14px; }
.p_muid-progress { width: 100%; }
.p_muid-progress:after { content: '%'; }
.p_muid-fail { background: #c00; padding: 2px; color: #fff; }
.p_muid-hidden { display: none !important;}
</style>
<script type="text/html" id="p_muid-temp-uploader">
	<div>
		<div class="p_muid-holder"></div>
		<p class="p_muid-upload">
			<label>
				<span style="display: none;">Drag &amp; drop not supported in your browser, but you can still upload via this input field:</span>
				<span>Drag a file from your desktop on to the drop zone above or click:</span>
				<br><input type="file" />
			</label></p>
		<p class="p_muid-filereader">File API &amp; FileReader API not supported in your browser</p>
		<p class="p_muid-formdata">XHR2's FormData is not supported in your browser</p>
		<p class="p_muid-progress">XHR2's upload progress isn't supported in your browser</p>
		<p>Upload progress: <progress class="p_muid-uploadprogress" min="0" max="100" value="0">0</progress></p>
	</div>
</script>