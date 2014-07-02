<?php
/**
 * A view to load the Toopay Bootstrap Markdown Editor editor.
 *
 * @package Components\toopaymd
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
if (isset($_->com_elfinder))
	$_->com_elfinder->load();
$content_css = array_merge($_->editor->get_css(), array(h($_->config->location . $_->template->editor_css)));
?>
<script type="text/javascript">
$_.loadcss("<?php e($_->config->location); ?>components/com_toopaymd/includes/bootstrap-markdown-2.5.0/css/bootstrap-markdown.min.css");
$_.loadjs("<?php e($_->config->location); ?>components/com_toopaymd/includes/bootstrap-markdown-2.5.0/js/bootstrap-markdown.js");
$_.loadjs("<?php e($_->config->location); ?>components/com_toopaymd/includes/pagedown/Markdown.Converter.js");
$_.loadjs("<?php e($_->config->location); ?>components/com_toopaymd/includes/pagedown/Markdown.Sanitizer.js");
$_.loadjs("<?php e($_->config->location); ?>components/com_toopaymd/includes/Markdown.Extra.js");

$_(function(){

var preview = function(e){
	var content = e.parseContent(),
		panel = e.$editor.find(".md-footer .md-custom-preview");

	if (content.match(/^\s?$/)) {
		panel.hide()
	} else {
		panel.show().find(".preview-pane").html(content);
	}
};
var options = {
iconlibrary: "fa",
onPreview: function(e){
	var converter = new Markdown.Converter();
	Markdown.Extra.init(converter, {extensions: "all"});
	return converter.makeHtml(e.$textarea.val());
},
resize: true,
hiddenButtons:['cmdPreview','cmdUrl','cmdImage'],
footer:'<div class="md-custom-preview"><div class="page-header" style="margin: 0 0 10px;"><h5 style="margin: 0;">Live Preview</h5></div><div class="preview-pane"></div></div>',
onShow: preview,
onChange: preview,
additionalButtons: [
	[{
		name: "group2be",
		data: [{
			name: 'cmdNewUrl',
			title: 'URL/Link',
			hotkey: 'Ctrl+L',
			icon: "fa fa-link",
			callback: function(e){
				var chunk, cursor, selected = e.getSelection(), content = e.getContent(), link;

				if (selected.length == 0) {
					chunk = e.__localize('enter link description here');
				} else {
					chunk = selected.text;
				}

				link = prompt(e.__localize('Insert Hyperlink'),'http://');

				if (link != null && link != '' && link != 'http://') {
					e.replaceSelection('['+chunk+']('+link+')');
					cursor = selected.start+1;

					e.setSelection(cursor,cursor+chunk.length);
				}
			}
		},{
			name: "cmdNewImage",
			title: "Image",
			hotkey: 'Ctrl+G',
			icon: "fa fa-picture-o",
			callback: function(e){
				// Give ![] surround the selection and prepend the image link
				var chunk, cursor, selected = e.getSelection(), content = e.getContent(), link;

				if (selected.length == 0) {
					// Give extra word
					chunk = e.__localize('enter image description here');
				} else {
					chunk = selected.text;
				}

				var elfdlg = $("<div></div>").appendTo("body").elfinder({
					url: <?php echo json_encode(pines_url("com_elfinder", "connector")); ?>,
					height: <?php echo (int) $_->config->com_elfinder->default_height; ?>,
					resizable : false,
					getFileCallback: function(link) {
						if (link != null) {
							// transform selection and set the cursor into chunked text
							e.replaceSelection('!['+chunk+']('+link+' "'+e.__localize('enter image title here')+'")');
							cursor = selected.start+2;

							// Set the next tab
							e.setNextTab(e.__localize('enter image title here'));

							// Set the cursor
							e.setSelection(cursor,cursor+chunk.length);
						}
						elfdlg.dialog("close");
					}
				});
				elfdlg.css("overflow", "visible").dialog({
					width: 900,
					modal: true,
					zIndex: 400000,
					title: "Choose Image",
					close: function(){
						elfdlg.elfinder("destroy").dialog("destroy").remove();
					}
				}).dialog("widget").css("overflow", "visible");
			}
		}]
	},{
		name: "groupHelp",
		data: [{
			name: 'cmdHelp',
			title: 'Help',
			icon: "fa fa-question-circle",
			callback: function(e){
				$('<div class="modal">\
  <div class="modal-dialog">\
    <div class="modal-content">\
      <div class="modal-header">\
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\
        <h4 class="modal-title">Help</h4>\
      </div>\
      <div class="modal-body">\
        <p>This editor uses Markdown Extra syntax. Some of the newer features that are supported don\'t show in the preview.</p>\
        <p>The original Markdown features are described <a href="http://daringfireball.net/projects/markdown/" target="_blank">here</a>.</p>\
        <p>The additional Markdown Extra features are described <a href="https://michelf.ca/projects/php-markdown/extra/" target="_blank">here</a>.</p>\
      </div>\
      <div class="modal-footer">\
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>\
      </div>\
    </div>\
  </div>\
</div>').appendTo('body').modal();
			}
		}]
	}]
]
};

// Convert textareas.
$("textarea.peditor").markdown($.extend({}, options, {
	height: 300
}));
$("textarea.peditor-simple").markdown($.extend({}, options, {
	height: 200
}));
$("textarea.peditor-email").markdown($.extend({}, options, {
	height: 300
}));

});
</script>