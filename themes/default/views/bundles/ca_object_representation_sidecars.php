<?php
/* ----------------------------------------------------------------------
 * bundles/ca_object_representation_sidecars.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2021-2023 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
$id_prefix 		= $this->getVar('placement_code').$this->getVar('id_prefix');
$t_instance 		= $this->getVar('t_subject');			// representation
$t_sidecar 			= $this->getVar('t_sidecar');			// sidecar	
$settings 			= $this->getVar('settings');
$add_label 		= $this->getVar('add_label');

$read_only		= ((isset($settings['readonly']) && $settings['readonly'])  || ($this->request->user->getBundleAccessLevel($t_instance->tableName(), 'ca_users') == __CA_BUNDLE_ACCESS_READONLY__));

$initial_values = $this->getVar('initialValues');
if (!is_array($initial_values)) { $initial_values = []; }

print caEditorBundleShowHideControl($this->request, $id_prefix);
print caEditorBundleMetadataDictionary($this->request, $id_prefix, $settings);
?>
<div id="<?= $id_prefix; ?>">
<?php
	//
	// Bundle template for new items
	//
?>
	<textarea class='caNewItemTemplate' style='display: none;'>
		<div id="<?= $id_prefix; ?>Item_{n}" class="labelInfo">
<?php
	if (!$read_only) {
?>	
			<div style="float: right;">
				<a href="#" class="caDeleteItemButton"><?= caNavIcon(__CA_NAV_ICON_DEL_BUNDLE__, 1); ?></a>
			</div>
<?php
	}
?>
			<div class="caListItem">
				<table>
					<tr>
						<td>
							<span class="formLabel"><?= _t('Description'); ?></span><br/>
				<?= $t_sidecar->htmlFormElement('notes', '^ELEMENT', array('name' => $id_prefix.'_notes{n}', 'id' => $id_prefix.'_notes{n}', 'no_tooltips' => true, 'dont_show_null_value' => true, 'width' => '670px')); ?>
						</td>
					</tr>
					<tr>
						<td>
							<span class="formLabel"><?= _t('Sidecar file'); ?></span>
					
							<?= $t_sidecar->htmlFormElement('sidecar_file', '^ELEMENT', array('name' => $id_prefix.'_sidecar_file{n}', 'id' => $id_prefix.'_sidecar_file{n}', 'no_tooltips' => true)); ?>
						</td>
					</tr>
				</table>
			
				<input type="hidden" name="<?= $id_prefix; ?>_id{n}" id="<?= $id_prefix; ?>_id{n}" value="{id}"/>
			</div>
		</div>
	</textarea>
<?php
	//
	// Bundle template for existing items
	//
?>		
	<textarea class='caItemTemplate' style='display: none;'>
		<div id="<?= $id_prefix; ?>Item_{n}" class="labelInfo">
<?php
	if (!$read_only) {
?>	
			<div style="float: right;">
				<a href="#" class="caDeleteItemButton"><?= caNavIcon(__CA_NAV_ICON_DEL_BUNDLE__, 1); ?></a>
			</div>
<?php
	}
?>
			<div class="caListItem">
				<span class="formLabel"><span class="sidecarFiletype"><span class="filetype">{typename}: {original_filename}</span></span> <span class="formLabelPlain"><em>{notes}</em> ({filesize})</span>
				<?= urlDecode(caNavLink($this->request, caNavIcon(__CA_NAV_ICON_DOWNLOAD__, 1,  null, array('align' => 'top')), '', '*', '*', 'downloadSidecarFile', array('representation_id' => $t_instance->getPrimaryKey(), 'sidecar_id' => "{sidecar_id}", 'download' => 1), array('id' => "{$id_prefix}download{sidecar_id}", 'class' => 'attributeDownloadButton'))); ?>
			
				<input type="hidden" name="<?= $id_prefix; ?>_sidecar_id{n}" id="<?= $id_prefix; ?>_sidecar_id{n}" value="{sidecar_id}"/>
			</div>
		</div>
	</textarea>
	
	<div class="bundleContainer">
		<div class="caItemList"></div>
<?php
	if (!$read_only) {
?>	
		<div class='button labelInfo caAddItemButton'><a href='#'><?= caNavIcon(__CA_NAV_ICON_ADD__, '15px'); ?> <?= $add_label ? $add_label : _t("Add sidecar file"); ?></a></div>
<?php
	}
?>
	</div>
</div>
			
<script type="text/javascript">
	jQuery(document).ready(function() {
		caUI.initRelationBundle('#<?= $id_prefix; ?>', {
			fieldNamePrefix: '<?= $id_prefix; ?>_',
			templateValues: ['mimetype', 'sidecar_id', 'filesize', 'notes'],
			initialValues: <?= json_encode($initial_values); ?>,
			initialValueOrder: <?= json_encode(array_keys($initial_values)); ?>,
			itemID: '<?= $id_prefix; ?>Item_',
			initialValueTemplateClassName: 'caItemTemplate',
			templateClassName: 'caNewItemTemplate',
			itemListClassName: 'caItemList',
			addButtonClassName: 'caAddItemButton',
			deleteButtonClassName: 'caDeleteItemButton',
			showEmptyFormsOnLoad: 0,
			readonly: <?= $read_only ? "true" : "false"; ?>
		});
	});
</script>
