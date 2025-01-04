<?php
/* ----------------------------------------------------------------------
 * bundles/ca_relationship_type_labels_preferred.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2025 Whirl-i-Gig
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
$labels 			= $this->getVar('labels');
$t_label 			= $this->getVar('t_label');
$initial_values 	= $this->getVar('label_initial_values');
if (!$force_new_labels = $this->getVar('new_labels')) { $force_new_labels = array(); }	// list of new labels not saved due to error which we need to for onto the label list as new

$settings 			= $this->getVar('settings');
$add_label 		= $this->getVar('add_label');

$locale_list		= $this->getVar('locale_list');

$read_only		= ((isset($settings['readonly']) && $settings['readonly'])  || ($this->request->user->getBundleAccessLevel('ca_relationship_types', 'preferred_labels') == __CA_BUNDLE_ACCESS_READONLY__));

print caEditorBundleShowHideControl($this->request, $id_prefix.'Labels', $settings, caInitialValuesArrayHasValue($id_prefix.'Labels', $initial_values));	
print caEditorBundleMetadataDictionary($this->request, $id_prefix.'Labels', $settings);
?>
<div id="<?= $id_prefix; ?>Labels">
<?php
	//
	// The bundle template - used to generate each bundle in the form
	//
?>
	<textarea class='caLabelTemplate' style='display: none;'>
		<div id="{fieldNamePrefix}Label_{n}" class="labelInfo">
			<div style="float: right;">
				<a href="#" class="caDeleteLabelButton"><?= caNavIcon(__CA_NAV_ICON_DEL_BUNDLE__, 1); ?></a>
			</div>
			<table>
				<tr valign="middle">
					<td>
						<table>
							<tr>
								<td>
									<?= $t_label->htmlFormElement('typename', null, array('name' => "{fieldNamePrefix}typename_{n}", 'id' => "{fieldNamePrefix}typename_{n}", "value" => "{{typename}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $read_only)); ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= $t_label->htmlFormElement('description', null, array('name' => "{fieldNamePrefix}description_{n}", 'id' => "{fieldNamePrefix}description_{n}", "value" => "{{description}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $read_only)); ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= $t_label->htmlFormElement('typename_reverse', null, array('name' => "{fieldNamePrefix}typename_reverse_{n}", 'id' => "{fieldNamePrefix}typename_reverse{n}", "value" => "{{typename_reverse}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $read_only)); ?>
								</td>
							</tr>
							<tr>
								<td>
									<?= $t_label->htmlFormElement('description_reverse', null, array('name' => "{fieldNamePrefix}description_reverse_{n}", 'id' => "{fieldNamePrefix}description_reverse{n}", "value" => "{{description_reverse}}", 'no_tooltips' => true, 'textAreaTagName' => 'textentry', 'readonly' => $read_only)); ?><br/>
									
									<?php print '<div class="formLabel">'.$locale_list.'</div>'; ?>
								</td>
							<tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</textarea>
	
	<div class="bundleContainer">
		<div class="caLabelList">
		
		</div>
		<div class="button labelInfo caAddLabelButton"><a href='#'><?= caNavIcon(__CA_NAV_ICON_ADD__, '15px'); ?> <?= $add_label ? $add_label : _t("Add label"); ?></a></div>
	</div>
			
	
</div>
<script type="text/javascript">
	caUI.initLabelBundle('#<?= $id_prefix; ?>Labels', {
		mode: 'preferred',
		fieldNamePrefix: '<?= $id_prefix; ?>',
		templateValues: ['typename', 'description', 'typename_reverse', 'description_reverse', 'locale_id'],
		initialValues: <?= json_encode($initial_values); ?>,
		forceNewValues: <?= json_encode($force_new_labels); ?>,
		labelID: 'Label_',
		localeClassName: 'labelLocale',
		templateClassName: 'caLabelTemplate',
		labelListClassName: 'caLabelList',
		addButtonClassName: 'caAddLabelButton',
		deleteButtonClassName: 'caDeleteLabelButton',
		readonly: <?= $read_only ? "1" : "0"; ?>,
		bundlePreview: <?php $cur = current($initial_values); print caEscapeForBundlePreview($cur['typename']); ?>,
		defaultLocaleID: <?= ca_locales::getDefaultCataloguingLocaleID(); ?>
	});
</script>
