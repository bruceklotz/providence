<?php
/* ----------------------------------------------------------------------
 * bundles/ca_acl_access.php : 
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
$t_instance 	= $this->getVar('t_instance');
	
$can_edit	 	= $t_instance->isSaveable($this->request);
$can_delete		= $t_instance->isDeletable($this->request);

$stats			= $this->getVar('statistics');
?>
<div class="sectionBox">
<?php
	if ($can_edit) {
		print $vs_control_box = caFormControlBox(
			caFormSubmitButton($this->request, __CA_NAV_ICON_SAVE__, _t("Save"), 'caAccessControlList').' '.
			caFormNavButton($this->request, __CA_NAV_ICON_CANCEL__, _t("Cancel"), '', $this->request->getModulePath(), $this->request->getController(), 'Access/'.$this->request->getActionExtra(), [$t_instance->primaryKey() => $t_instance->getPrimaryKey()]),
			'',
			''
		);
	}
	print caFormTag($this->request, 'SetAccess', 'caAccessControlList');
	
	if($t_instance->hasField('access')) {
?>	
	<div class='globalAccess'>
		<div class='title'><?= _t('Public access'); ?></div>
		<p class=""><?=  $t_instance->htmlFormElement('access', '^LABEL ^ELEMENT'); ?></p>
<?php
		if (
			(bool)$t_instance->getAppConfig()->get($t_instance->tableName().'_allow_access_inheritance') 
			&& 
			$t_instance->hasField('access_inherit_from_parent')
			&&
			(($t_instance->get('parent_id') > 0) || ($t_instance->tableName() === 'ca_objects'))
		) {
			print $t_instance->htmlFormElement('access_inherit_from_parent', '^LABEL ^ELEMENT', ['label' => _t('Inherit access from parent?')]);
		}
		
				if((bool)$t_instance->hasField('access_inherit_from_parent') && (($stats['subRecordCount'] ?? 0) > 0)) {
?>
		<p>
			<?= ($stats['inheritingSubRecordCount'] === 1) ? 
				_t('%1 %2 (out of %4 total) will inherit public access settings from this %3', $stats['inheritingAccessSubRecordCount'], Datamodel::getTableProperty($t_instance->tableName(), 'NAME_PLURAL'), $t_instance->getProperty('NAME_SINGULAR'), $stats['subRecordCount']) 
				: 
				_t(' %1 %2 (out of %4 total) will inherit public access settings from this %3', $stats['inheritingAccessSubRecordCount'], Datamodel::getTableProperty($t_instance->tableName(), 'NAME_PLURAL'), $t_instance->getProperty('NAME_SINGULAR'), $stats['subRecordCount'])  
			?>
<?php
			if(
					($stats['subRecordCount'] !== $stats['inheritingSubRecordCount'])
					||
					($stats['inheritingSubRecordCount'] > 0)
			) {
?>
				<div style="margin-left: 10px;">
<?php
				if($stats['subRecordCount'] !== $stats['inheritingAccessSubRecordCount']) {
?>
					<?= caHTMLCheckboxInput('set_all_access_inherit_from_parent', ['id' => 'setAllAccessInheritFromParent', 'value' => '1']); ?> <?= _t('Set all to inherit'); ?><span style='margin-left: 10px;'></span>
<?php
				}
				
				if($stats['inheritingAccessSubRecordCount'] > 0) {
?>
					<?= caHTMLCheckboxInput('set_none_access_inherit_from_parent', ['id' => 'setNoneAccessInheritFromParent', 'value' => '1']); ?> <?= _t('Set all to not inherit'); ?>
<?php
				}
?>
				</div>
<?php
			}
		}
		if(($t_instance->tableName() === 'ca_collections') && (bool)$t_instance->getAppConfig()->get('ca_objects_allow_access_inheritance')) {
?>
		<p>
			<?= ($stats['inheritingRelatedObjectCount'] === 1) ? 
				_t('%1 %2 (out of %4 total) will inherit Pawtucket access settings from this %3', $stats['inheritingAccessRelatedObjectCount'], Datamodel::getTableProperty('ca_objects', 'NAME_PLURAL'), $t_instance->getProperty('NAME_SINGULAR'), $stats['potentialInheritingAccessRelatedObjectCount']) 
				: 
				_t(' %1 %2 (out of %4 total) will inherit Pawtucket access settings from this %3', $stats['inheritingAccessRelatedObjectCount'], Datamodel::getTableProperty('ca_objects', 'NAME_PLURAL'), $t_instance->getProperty('NAME_SINGULAR'), $stats['potentialInheritingAccessRelatedObjectCount'])  
			?>
<?php
			if(
					($stats['potentialInheritingAccessRelatedObjectCount'] !== $stats['inheritingAccessRelatedObjectCount'])
					||
					($stats['inheritingAccessRelatedObjectCount'] > 0)
			) {
?>
				<div style="margin-left: 10px;">
<?php
				if($stats['potentialInheritingAccessRelatedObjectCount'] !== $stats['inheritingAccessRelatedObjectCount']) {
?>
					<?= caHTMLCheckboxInput('set_all_objects_access_inherit_from_parent', ['id' => 'setAllObjectsAccessInheritFromParent', 'value' => '1']); ?> <?= _t('Set all to inherit'); ?><span style='margin-left: 10px;'></span>
<?php
				}
				
				if($stats['inheritingAccessRelatedObjectCount'] > 0) {
?>
					<?= caHTMLCheckboxInput('set_none_objects_access_inherit_from_parent', ['id' => 'setNoneObjectsAccessInheritFromParent', 'value' => '1']); ?> <?= _t('Set all to not inherit'); ?>
<?php
				}
?>
				</div>
<?php
			}
		}
	}
?>
	</div>

	<div class='globalAccess'>
		<div class='title'><?= _t('Item access'); ?></div>
<?php 	
		$global_access = $t_instance->getACLWorldAccess(['returnAsInitialValuesForBundle' => true]);
		$global_access_status = $global_access['access_display'];
		print "<div class='control'>"._t('All groups and users %1 this record, unless an exception is created', $t_instance->getACLWorldHTMLFormBundle($this->request, 'caAccessControlList'))."</div>"; 
?>		
		<hr/>
		<div class='subtitle'><?= _t('Exceptions'); ?></div>
		<div class='control'>
<?php
		print $t_instance->getACLGroupHTMLFormBundle($this->request, 'caAccessControlList');			
		print caHTMLHiddenInput($t_instance->primaryKey(), ['value' => $t_instance->getPrimaryKey()]);
?>	
		<?= $t_instance->getACLUserHTMLFormBundle($this->request, 'caAccessControlList'); ?>

		</div>
		<hr/>
		<div class='subtitle'><?= _t('Inheritance'); ?></div>
<?php
if(
	($t_instance->hasField('acl_inherit_from_parent') && (($stats['subRecordCount'] ?? null) > 0))
	||
	($t_instance->hasField('acl_inherit_from_ca_collections'))
	||
	(($t_instance->tableName() === 'ca_collections') && (($stats['relatedObjectCount'] ?? null) > 0))
) {
	
	if ($t_instance->hasField('acl_inherit_from_ca_collections')) {
?>
		<div class='control'><?= $t_instance->htmlFormElement('acl_inherit_from_ca_collections', '^LABEL ^ELEMENT',  ['label' => _t('Inherit item access from collection(s)?')]); ?></div>
<?php
	}
	if ($t_instance->hasField('acl_inherit_from_parent')) {
?>
		<div class='control'><?= $t_instance->htmlFormElement('acl_inherit_from_parent', '^LABEL ^ELEMENT', ['label' => _t('Inherit item access from parent?')]); ?></div>
<?php
	}

	if(
		($t_instance->hasField('acl_inherit_from_parent') && (($stats['subRecordCount'] ?? null) > 0))
	) {
?>
		<p>
			<?= ($stats['inheritingSubRecordCount'] === 1) ? 
				_t('%1 %2 (out of %4 total) will inherit item access settings from this %3', $stats['inheritingSubRecordCount'], $t_instance->getProperty('NAME_SINGULAR'), $t_instance->getProperty('NAME_SINGULAR'), $stats['subRecordCount']) 
				: 
				_t('%1 %2 (out of %4 total) will inherit item access settings from this %3', $stats['inheritingSubRecordCount'], $t_instance->getProperty('NAME_PLURAL'), $t_instance->getProperty('NAME_SINGULAR'), $stats['subRecordCount'])  
			?>
<?php
			if(
				($stats['subRecordCount'] !== $stats['inheritingSubRecordCount'])
				||
				($stats['inheritingSubRecordCount'] > 0)
			) {
?>
				<div class='inheritanceControl'>
<?php
				if($stats['subRecordCount'] !== $stats['inheritingSubRecordCount']) {
?>
					<?= caHTMLCheckboxInput('set_all_acl_inherit_from_parent', ['id' => 'setAllACLInheritFromParent', 'value' => '1', ]); ?> <?= _t('Set all to inherit'); ?><span style='margin-left: 10px;'></span>
<?php
				}
				
				if($stats['inheritingSubRecordCount'] > 0) {
?>
					<?= caHTMLCheckboxInput('set_none_acl_inherit_from_parent', ['id' => 'setNoneACLInheritFromParent', 'value' => '1']); ?> <?= _t('Set all to not inherit'); ?>
<?php
				}
?>
				</div>
<?php
			}
?>			
		</p>
<?php
	}

	if(($t_instance->tableName() === 'ca_collections') && (($stats['relatedObjectCount'] ?? null) > 0)) {
?>
		<p>
			<?= ($stats['inheritingRelatedObjectCount'] === 1) ? 
				_t('%1 %2 (out of %4 total) will inherit item access settings from this %3', $stats['inheritingRelatedObjectCount'], Datamodel::getTableProperty('ca_objects', 'NAME_PLURAL'), $t_instance->getProperty('NAME_SINGULAR'), $stats['potentialInheritingRelatedObjectCount']) 
				: 
				_t(' %1 %2 (out of %4 total) will inherit item access settings from this %3', $stats['inheritingRelatedObjectCount'], Datamodel::getTableProperty('ca_objects', 'NAME_PLURAL'), $t_instance->getProperty('NAME_SINGULAR'), $stats['potentialInheritingRelatedObjectCount'])  
			?>
<?php
			if(
				($stats['potentialInheritingRelatedObjectCount'] !== $stats['inheritingRelatedObjectCount'])
				||
				($stats['inheritingRelatedObjectCount'] > 0)
			) {
?>
				<div class='inheritanceControl'>
<?php
				if($stats['potentialInheritingRelatedObjectCount'] !== $stats['inheritingRelatedObjectCount']) {
?>
					<?= caHTMLCheckboxInput('set_all_acl_inherit_from_ca_collections', ['id' => 'setAllACLInheritFromCollections', 'value' => '1']); ?> <?= _t('Set all to inherit'); ?><span style='margin-left: 10px;'></span>
<?php
				}
				
				if($stats['inheritingRelatedObjectCount'] > 0) {
?>
					<?= caHTMLCheckboxInput('set_none_acl_inherit_from_ca_collections', ['id' => 'setNoneACLInheritFromCollections', 'value' => '1']); ?> <?= _t('Set all to not inherit'); ?>
<?php
				}
?>
				</div>
<?php
			}
?>			
		</p>
<?php
		}
	}
?>
	</form>	
	<div class="editorBottomPadding"><!-- empty --></div>
</div>

<script>
	jQuery(document).ready(function() {
		jQuery('#setAllACLInheritFromCollections, #setNoneACLInheritFromCollections').on('change', function(e) {
			if(jQuery(e.target).attr('id') == 'setAllACLInheritFromCollections') {
				jQuery('#setNoneACLInheritFromCollections').attr('checked', false);
			} else {
				jQuery('#setAllACLInheritFromCollections').attr('checked', false);;
			}
		});
		jQuery('#setAllACLInheritFromParent, #setNoneACLInheritFromParent').on('change', function(e) {
			if(jQuery(e.target).attr('id') == 'setAllACLInheritFromParent') {
				jQuery('#setNoneACLInheritFromParent').attr('checked', false);
			} else {
				jQuery('#setAllACLInheritFromParent').attr('checked', false);;
			}
		});
	});
</script>
