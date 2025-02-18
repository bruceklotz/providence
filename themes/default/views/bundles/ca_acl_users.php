<?php
/* ----------------------------------------------------------------------
 * bundles/ca_acl_users.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2012-2025 Whirl-i-Gig
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
$id_prefix 		= $this->getVar('id_prefix').'_user';
$t_instance 	= $this->getVar('t_instance');
$t_item 		= $this->getVar('t_user');
$t_subject 		= $this->getVar('t_subject');		
$settings 		= $this->getVar('settings');
$add_label 		= $this->getVar('add_label');
$t_acl = new ca_acl();

//$read_only		=	((isset($settings['readonly']) && $settings['readonly'])  || ($this->request->user->getBundleAccessLevel($t_instance->tableName(), 'ca_users') == __CA_BUNDLE_ACCESS_READONLY__));
$read_only = false;

$initial_values = $this->getVar('initialValues');
if (!is_array($initial_values)) { $initial_values = array(); }	
?>
<div id="<?= $id_prefix.$t_item->tableNum().'_rel'; ?>">
<?php
	//
	// The bundle template - used to generate each bundle in the form
	//
?>
	<textarea class='caItemTemplate' style='display: none;'>
		<div id="<?= $id_prefix; ?>Item_{n}" class="labelInfo">
			<table class="caListItem">
				<tr>
					<td class="formLabel">
						<?= _t('User'); ?>
						<input type="text" size="60" name="<?= $id_prefix; ?>_autocomplete{n}" value="{{label}}" id="<?= $id_prefix; ?>_autocomplete{n}" class="lookupBg"/>
						<?= $t_acl->htmlFormElement('access', '^ELEMENT', array('name' => $id_prefix.'_access_{n}', 'id' => $id_prefix.'_access_{n}', 'value' => '{{access}}', 'no_tooltips' => true)); ?>
						<input type="hidden" name="<?= $id_prefix; ?>_id{n}" id="<?= $id_prefix; ?>_id{n}" value="{id}"/>
					</td>
					<td>
<?php
	if (!$read_only) {
?>	
						<a href="#" class="caDeleteItemButton" aria-label='<?= htmlspecialchars(_t('Delete')); ?>'><?= caNavIcon(__CA_NAV_ICON_DEL_BUNDLE__, 1); ?></a>
<?php
	}
?>
					</td>
				</tr>
			</table>
		</div>
	</textarea>
	
	<div class="bundleContainer">
		<div class="control">
			<?= _t('Users'); ?>
<?php
	if (!$read_only) {
?>	
		<span class='button labelInfo caAddItemButton'><a href='#'><?= caNavIcon(__CA_NAV_ICON_ADD__, '15px'); ?></a></span>
<?php
	}
?>
		</div>
		<div class="caItemList">
		
		</div>
	</div>
</div>
			
<script type="text/javascript">
	jQuery(document).ready(function() {
		caUI.initRelationBundle('#<?= $id_prefix.$t_item->tableNum().'_rel'; ?>', {
			fieldNamePrefix: '<?= $id_prefix; ?>_',
			templateValues: ['label', 'effective_date', 'access', 'id'],
			initialValues: <?= json_encode($initial_values); ?>,
			itemID: '<?= $id_prefix; ?>Item_',
			templateClassName: 'caItemTemplate',
			itemListClassName: 'caItemList',
			addButtonClassName: 'caAddItemButton',
			deleteButtonClassName: 'caDeleteItemButton',
			showEmptyFormsOnLoad: 0,
			addMode: 'prepend',
			readonly: <?= $read_only ? "true" : "false"; ?>,
			autocompleteUrl: '<?= caNavUrl($this->request, 'lookup', 'User', 'Get', array()); ?>'
		});
	});
</script>
