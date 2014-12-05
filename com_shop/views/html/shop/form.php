<?php
/**
 * Provides a form for the user to edit a shop.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = (!isset($this->entity->guid)) ? 'Editing New Shop' : 'Editing ['.h($this->entity->name).']';
$this->note = 'Provide shop details in this form.';
$_->editor->load();
$_->com_pgrid->load();
$_->uploader->load();
?>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_shop', 'shop/save')); ?>">
	<script type="text/javascript">
		$_(function(){
			// Attributes
			var attributes = $("#p_muid_tab_attributes input[name=attributes]");
			var attributes_table = $("#p_muid_tab_attributes .attributes_table");
			var attribute_dialog = $("#p_muid_tab_attributes .attribute_dialog");

			attributes_table.pgrid({
				pgrid_paginate: false,
				pgrid_toolbar: true,
				pgrid_toolbar_contents : [
					{
						type: 'button',
						text: 'Add Attribute',
						extra_class: 'picon picon-list-add',
						selection_optional: true,
						click: function(){
							attribute_dialog.dialog('open');
						}
					},
					{
						type: 'button',
						text: 'Remove Attribute',
						extra_class: 'picon picon-list-remove',
						click: function(e, rows){
							rows.pgrid_delete();
							update_attributes();
						}
					}
				],
				pgrid_view_height: "300px"
			});

			// Attribute Dialog
			attribute_dialog.dialog({
				bgiframe: true,
				autoOpen: false,
				modal: true,
				width: 500,
				buttons: {
					"Done": function(){
						var cur_attribute_name = attribute_dialog.find("input[name=cur_attribute_name]").val();
						var cur_attribute_value = attribute_dialog.find("input[name=cur_attribute_value]").val();
						if (cur_attribute_name == "" || cur_attribute_value == "") {
							alert("Please provide both a name and a value for this attribute.");
							return;
						}
						var new_attribute = [{
							key: null,
							values: [
								$_.safe(cur_attribute_name),
								$_.safe(cur_attribute_value)
							]
						}];
						attributes_table.pgrid_add(new_attribute);
						$(this).dialog('close');
					}
				},
				close: function(){
					update_attributes();
				}
			});

			var update_attributes = function(){
				attribute_dialog.find("input[name=cur_attribute_name]").val("");
				attribute_dialog.find("input[name=cur_attribute_value]").val("");
				attributes.val(JSON.stringify(attributes_table.pgrid_get_all_rows().pgrid_export_rows()));
			};

			update_attributes();
		});
	</script>
	<ul class="nav nav-tabs" style="clear: both;">
		<li class="active"><a href="#p_muid_tab_general" data-toggle="tab">General</a></li>
		<li><a href="#p_muid_tab_attributes" data-toggle="tab">Attributes</a></li>
	</ul>
	<div id="p_muid_shop_tabs" class="tab-content">
		<div class="tab-pane active" id="p_muid_tab_general">
			<?php if (isset($this->entity->guid)) { ?>
			<div class="date_info" style="float: right; text-align: right;">
				<?php if (isset($this->entity->user)) { ?>
				<div>User: <span class="date"><?php e("{$this->entity->user->name} [{$this->entity->user->username}]"); ?></span></div>
				<div>Group: <span class="date"><?php e("{$this->entity->group->name} [{$this->entity->group->groupname}]"); ?></span></div>
				<?php } ?>
				<div>Created: <span class="date"><?php e(format_date($this->entity->cdate, 'full_short')); ?></span></div>
				<div>Modified: <span class="date"><?php e(format_date($this->entity->mdate, 'full_short')); ?></span></div>
			</div>
			<?php } ?>
			<div class="pf-element">
				<label><span class="pf-label">Name</span>
					<input class="pf-field form-control" type="text" name="name" size="24" value="<?php e($this->entity->name); ?>" /></label>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">Display Email</span>
					<span class="pf-note">Check this to display your email on your shop page. Note that customers can already reach you through a private message.</span>
					<input class="pf-field" type="checkbox" name="display_email" value=""<?php echo $this->entity->display_email ? ' checked="checked"' : ''; ?> /></label>
			</div>
			<script type="text/javascript">
				$_(function(){
					$("#p_muid_thumbnail").change(function(){
						var tmp_url = <?php echo json_encode(pines_url('com_sales', 'product/temp_image', array('image' => '__image__', 'type' => '__type__', 'source' => '__source__', 'options' => '__options__'))); ?>;
						$("#p_muid_thumbnail_preview").attr("src", tmp_url.replace('__image__', escape($(this).val())).replace('__type__', 'thumbnail').replace('__source__', 'temp').replace('__options__', ''));
					});
				});
			</script>
			<div class="pf-element">
				<span class="pf-label">Thumbnail</span>
				<input class="pf-field form-control puploader puploader-temp" id="p_muid_thumbnail" type="text" name="thumbnail" value="<?php e($this->entity->thumbnail); ?>" />
			</div>
			<div class="pf-element">
				<span class="pf-label">Thumbnail Preview</span>
				<div class="pf-group">
					<div class="pf-field">
						<div class="thumbnail">
							<img alt="Thumbnail Preview" id="p_muid_thumbnail_preview" src="<?php e(isset($this->entity->thumbnail) ? $_->config->location.$this->entity->thumbnail : "http://placehold.it/{$_->config->com_sales->product_thumbnail_width}x{$_->config->com_sales->product_thumbnail_height}"); ?>" />
						</div>
					</div>
				</div>
			</div>
			<script type="text/javascript">
				$_(function(){
					$("#p_muid_header").change(function(){
						var tmp_url = <?php echo json_encode(pines_url('com_sales', 'product/temp_image', array('image' => '__image__', 'type' => '__type__', 'source' => '__source__', 'options' => '__options__'))); ?>;
						$("#p_muid_header_preview").attr("src", tmp_url.replace('__image__', escape($(this).val())).replace('__type__', 'header').replace('__source__', 'temp').replace('__options__', ''));
					});
				});
			</script>
			<div class="pf-element">
				<span class="pf-label">Header Image</span>
				<span class="pf-note">1600x400 or 4x1 ratio</span>
				<input class="pf-field form-control puploader puploader-temp" id="p_muid_header" type="text" name="header" value="<?php e($this->entity->header); ?>" />
			</div>
			<div class="pf-element pf-full-width">
				<span class="pf-label">Header Preview</span>
				<div class="pf-group">
					<div class="pf-field">
						<div class="header">
							<img alt="Header Preview" style="width: 100%; height: 25%;" id="p_muid_header_preview" src="<?php e(isset($this->entity->header) ? $_->config->location.$this->entity->header : 'http://placehold.it/1600x400'); ?>" />
						</div>
					</div>
				</div>
			</div>
			<div class="pf-element pf-full-width">
				<span class="pf-label">Description</span><br />
				<textarea rows="3" cols="35" class="peditor" style="width: 100%;" name="description"><?php e($this->entity->description_pesource); ?></textarea>
			</div>
			<div class="pf-element pf-full-width">
				<span class="pf-label">Short Description</span><br />
				<textarea rows="3" cols="35" class="peditor-simple" style="width: 100%;" name="short_description"><?php e($this->entity->short_description_pesource); ?></textarea>
			</div>
			<br class="pf-clearing" />
		</div>
		<div class="tab-pane" id="p_muid_tab_attributes">
			<div class="pf-element pf-full-width">
				<table class="attributes_table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Value</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->entity->attributes as $cur_attribute) { ?>
						<tr>
							<td><?php e($cur_attribute['name']); ?></td>
							<td><?php e($cur_attribute['value']); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<input type="hidden" name="attributes" />
			</div>
			<div class="attribute_dialog" title="Add an Attribute" style="display: none;">
				<div class="pf-form">
					<div class="pf-element">
						<label>
							<span class="pf-label">Name</span>
							<input class="pf-field form-control" type="text" name="cur_attribute_name" size="24" />
						</label>
					</div>
					<div class="pf-element">
						<label>
							<span class="pf-label">Value</span>
							<input class="pf-field form-control" type="text" name="cur_attribute_value" size="24" />
						</label>
					</div>
				</div>
				<br style="clear: both; height: 1px;" />
			</div>
			<br class="pf-clearing" />
		</div>
	</div>
	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<?php } ?>
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
		<input class="pf-button btn btn-default" type="button" onclick="$_.get(<?php e(json_encode(pines_url('com_shop', 'shop/list'))); ?>);" value="Cancel" />
	</div>
</form>