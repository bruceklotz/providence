<?php
/* ----------------------------------------------------------------------
 * bundles/ca_entities.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2024 Whirl-i-Gig
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
$t_instance 	= $this->getVar('t_instance');
$t_item 		= $this->getVar('t_item');				// entity
$t_item_rel 	= $this->getVar('t_item_rel');
$t_subject 		= $this->getVar('t_subject');
$settings 		= $this->getVar('settings');
$add_label 		= $this->getVar('add_label');
$rel_types		= $this->getVar('relationship_types');
$placement_code = $this->getVar('placement_code');
$placement_id	= (int)$settings['placement_id'];

$force_values 	= $this->getVar('forceValues');

$sort			= caGetOption('sort', $settings, '');
$allow_drag_sort = caGetOption('allowDragSort', $settings, false);

$read_only		= (caGetOption('readonly', $settings, false)  || ($this->request->user->getBundleAccessLevel($t_instance->tableName(), 'ca_entities') == __CA_BUNDLE_ACCESS_READONLY__));
$dont_show_del	= caGetOption('dontShowDeleteButton', $settings, false);

$batch			= $this->getVar('batch');

$color 			= caGetOption('colorItem', $settings, '');
$first_color 	= caGetOption('colorFirstItem', $settings, '');
$last_color 	= caGetOption('colorLastItem', $settings, '');

$dont_show_relationship_type = caGetOption('dontShowRelationshipTypes', $settings, false) ? 'none' : null; 

$quick_add_enabled = $this->getVar('quickadd_enabled');

// Dyamically loaded sort ordering
$loaded_sort 			= $this->getVar('sort');
$loaded_sort_direction 	= $this->getVar('sortDirection');

// params to pass during entity lookup
$lookup_params = array(
	'types' => caGetOption(['restrict_to_types', 'restrict_to_type'], $settings, ''),
	'noSubtypes' => caGetOption('dont_include_subtypes_in_type_restriction', $settings, false, ['castTo' => 'bool']),
	'noInline' => (!$quick_add_enabled || (bool) preg_match("/QuickAdd$/", $this->request->getController())) ? 1 : 0,
	'self' => $t_instance->tableName().':'.$t_instance->getPrimaryKey()
);

$errors = [];
foreach($action_errors = $this->request->getActionErrors($placement_code) as $o_error) {
	$errors[] = $o_error->getErrorDescription();
}

$count = $this->getVar('relationship_count');
$num_per_page = caGetOption('numPerPage', $settings, 10);

if (!RequestHTTP::isAjax()) {
	if(caGetOption('showCount', $settings, false)) { print $count ? "({$count})" : ''; }

	if ($batch) {
		print caBatchEditorRelationshipModeControl($t_item, $id_prefix);
	} else {
		print caEditorBundleShowHideControl($this->request, $id_prefix, $settings, caInitialValuesArrayHasValue($id_prefix, $this->getVar('initialValues')));
	}
	print caEditorBundleMetadataDictionary($this->request, $id_prefix, $settings);
}

$make_link = !caTemplateHasLinks(caGetOption('display_template', $settings, null));
?>
<div id="<?= $id_prefix; ?>" <?= $batch ? "class='editorBatchBundleContent'" : ''; ?>>
<?php
	print "<div class='bundleSubLabel'>";	
	if(is_array($this->getVar('initialValues')) && sizeof($this->getVar('initialValues'))) {
		print caEditorBundleBatchEditorControls($this->request, $placement_id, $t_subject, $t_instance->tableName(), $settings);
		print caGetPrintFormatsListAsHTMLForRelatedBundles($id_prefix, $this->request, $t_instance, $t_item, $t_item_rel, $placement_id);
	
		if(!$read_only) {
			print caEditorBundleSortControls($this->request, $id_prefix, $t_item->tableName(), $t_instance->tableName(), array_merge($settings, ['sort' => $loaded_sort, 'sortDirection' => $loaded_sort_direction]));
		}
		
	}
	print "<div style='clear:both;'></div></div><!-- end bundleSubLabel -->";


	//
	// Template to generate display for existing items
	//
?>
	<textarea class='caItemTemplate' style='display: none;'>
		<span class="formLabelError">{error}</span>
<?php
	switch($settings['list_format'] ?? null) {
		case 'list':
?>
		<div id="<?= $id_prefix; ?>Item_{n}" class="labelInfo listRel caRelatedItem">
<?php
	if (!$read_only && ca_editor_uis::loadDefaultUI($t_item_rel->tableNum(), $this->request)) {
?><a href="#" class="caInterstitialEditButton listRelEditButton"><?= caNavIcon(__CA_NAV_ICON_INTERSTITIAL_EDIT_BUNDLE__, "16px"); ?></a><?php
	}
	if (!$read_only && !$dont_show_del) {
?><a href="#" class="caDeleteItemButton listRelDeleteButton"><?= caNavIcon(__CA_NAV_ICON_DEL_BUNDLE__, 1); ?></a><?php
	}
?>
			<a href="<?= urldecode(caEditorUrl($this->request, 'ca_occurrences', '{occurrence_id}')); ?>" class="caEditItemButton" id="<?= $id_prefix; ?>_edit_related_{n}"></a>
			<span id='<?= $id_prefix; ?>_BundleTemplateDisplay{n}'>
<?php
			print caGetRelationDisplayString($this->request, 'ca_entities', array('class' => 'caEditItemButton', 'id' => "{$id_prefix}_edit_related_{n}"), array('display' => '_display', 'makeLink' => $make_link, 'prefix' => $id_prefix, 'relationshipTypeDisplayPosition' => $dont_show_relationship_type));
?>
			</span>
			<input type="hidden" name="<?= $id_prefix; ?>_id{n}" id="<?= $id_prefix; ?>_id{n}" value="{id}"/>
		</div>
<?php
			break;
		case 'bubbles':
		default:
?>
		<div id="<?= $id_prefix; ?>Item_{n}" class="labelInfo roundedRel caRelatedItem">
			<span id='<?= $id_prefix; ?>_BundleTemplateDisplay{n}'>
<?php
			print caGetRelationDisplayString($this->request, 'ca_entities', array('class' => 'caEditItemButton', 'id' => "{$id_prefix}_edit_related_{n}"), array('display' => '_display', 'makeLink' => $make_link, 'prefix' => $id_prefix, 'relationshipTypeDisplayPosition' => $dont_show_relationship_type));
?>
			</span>
			<input type="hidden" name="<?= $id_prefix; ?>_id{n}" id="<?= $id_prefix; ?>_id{n}" value="{id}"/>
<?php
	if (!$read_only && ca_editor_uis::loadDefaultUI($t_item_rel->tableNum(), $this->request)) {
?><a href="#" class="caInterstitialEditButton listRelEditButton"><?= caNavIcon(__CA_NAV_ICON_INTERSTITIAL_EDIT_BUNDLE__, "16px"); ?></a><?php
	}
	if (!$read_only && !$dont_show_del) {
?><a href="#" class="caDeleteItemButton"><?= caNavIcon(__CA_NAV_ICON_DEL_BUNDLE__, 1); ?></a><?php
	}
?>			
			<div style="display: none;" class="itemName">{surname}</div>
			<div style="display: none;" class="itemIdno">{idno_sort}</div>
		</div>
<?php
	}
?>
	</textarea>
<?php
	//
	// Template to generate controls for creating new relationship
	//
?>
	<textarea class='caNewItemTemplate' style='display: none;'>
		<span class="formLabelError">{error}</span>
		<div style="clear: both; width: 1px; height: 1px;"><!-- empty --></div>
		<div id="<?= $id_prefix; ?>Item_{n}" class="labelInfo">
			<table class="caListItem">
				<tr>
					<td>
						<input type="text" size="60" name="<?= $id_prefix; ?>_autocomplete{n}" value="{{label}}" id="<?= $id_prefix; ?>_autocomplete{n}" class="lookupBg"/>
					</td>
					<td>
						<select name="<?= $id_prefix; ?>_type_id{n}" id="<?= $id_prefix; ?>_type_id{n}" style="display: none;"></select>
						<input type="hidden" name="<?= $id_prefix; ?>_id{n}" id="<?= $id_prefix; ?>_id{n}" value="{id}"/>
					</td>
					<td>
						<a href="#" class="caDeleteItemButton"><?= caNavIcon(__CA_NAV_ICON_DEL_BUNDLE__, 1); ?></a>
											
						<a href="<?= urldecode(caEditorUrl($this->request, 'ca_entities', '{entity_id}')); ?>" class="caEditItemButton" id="<?= $id_prefix; ?>_edit_related_{n}"><?= caNavIcon(__CA_NAV_ICON_GO__, 1); ?></a>
					</td>
				</tr>
			</table>
		</div>
	</textarea>
	
	<div class="bundleContainer">
		<div class="caItemList">
<?php
	if (sizeof($errors)) {
?>
		<span class="formLabelError"><?= join("; ", $errors); ?><br class="clear"/></span>
<?php
	}
?>
		
		</div>
		<div class="caNewItemList"></div>
		<input type="hidden" name="<?= $id_prefix; ?>BundleList" id="<?= $id_prefix; ?>BundleList" value=""/>

		<div style="clear: both; width: 1px; height: 1px;"><!-- empty --></div>
<?php
	if (!$read_only) {
?>
		<div class='button labelInfo caAddItemButton'><a href='#'><?= caNavIcon(__CA_NAV_ICON_ADD__, '15px'); ?> <?= $add_label ? $add_label : _t("Add relationship"); ?></a></div>
<?php
	}
?>
	</div>
</div>
		
<?php if($quick_add_enabled) { ?>
<div id="caRelationQuickAddPanel<?= $id_prefix; ?>" class="caRelationQuickAddPanel"> 
	<div id="caRelationQuickAddPanel<?= $id_prefix; ?>ContentArea">
	<div class='dialogHeader'><?= _t('Quick Add', $t_item->getProperty('NAME_SINGULAR')); ?></div>
		
	</div>
</div>
<?php } ?>

<div id="caRelationEditorPanel<?= $id_prefix; ?>" class="caRelationQuickAddPanel"> 
	<div id="caRelationEditorPanel<?= $id_prefix; ?>ContentArea">
	<div class='dialogHeader'><?= _t('Relation editor', $t_item->getProperty('NAME_SINGULAR')); ?></div>
		
	</div>
	
	<textarea class='caBundleDisplayTemplate' style='display: none;'>
		<?= caGetRelationDisplayString($this->request, 'ca_entities', array(), array('display' => '_display', 'makeLink' => false, 'relationshipTypeDisplayPosition' => $dont_show_relationship_type)); ?>
	</textarea>
</div>	
<script type="text/javascript">
<?php if($quick_add_enabled) { ?>
	var caRelationQuickAddPanel<?= $id_prefix; ?>;
<?php } ?>
	var caRelationBundle<?= $id_prefix; ?>;
	jQuery(document).ready(function() {
		jQuery('#<?= $id_prefix; ?>caItemListSortControlTrigger').click(function() { jQuery('#<?= $id_prefix; ?>caItemListSortControls').slideToggle(200); return false; });
		jQuery('#<?= $id_prefix; ?>caItemListSortControls a.caItemListSortControl').click(function() {jQuery('#<?= $id_prefix; ?>caItemListSortControls').slideUp(200); return false; });
		
		if (caUI.initPanel) {
<?php if($quick_add_enabled) { ?>
			caRelationQuickAddPanel<?= $id_prefix; ?> = caUI.initPanel({ 
				panelID: "caRelationQuickAddPanel<?= $id_prefix; ?>",						/* DOM ID of the <div> enclosing the panel */
				panelContentID: "caRelationQuickAddPanel<?= $id_prefix; ?>ContentArea",		/* DOM ID of the content area <div> in the panel */
				exposeBackgroundColor: "#000000",				
				exposeBackgroundOpacity: 0.7,					
				panelTransitionSpeed: 400,						
				closeButtonSelector: ".close",
				center: true,
				onOpenCallback: function() {
				jQuery("#topNavContainer").hide(250);
				},
				onCloseCallback: function() {
					jQuery("#topNavContainer").show(250);
				}
			});
<?php } ?>
			caRelationEditorPanel<?= $id_prefix; ?> = caUI.initPanel({ 
				panelID: "caRelationEditorPanel<?= $id_prefix; ?>",						/* DOM ID of the <div> enclosing the panel */
				panelContentID: "caRelationEditorPanel<?= $id_prefix; ?>ContentArea",		/* DOM ID of the content area <div> in the panel */
				exposeBackgroundColor: "#000000",				
				exposeBackgroundOpacity: 0.7,					
				panelTransitionSpeed: 400,						
				closeButtonSelector: ".close",
				center: true,
				onOpenCallback: function() {
				jQuery("#topNavContainer").hide(250);
				},
				onCloseCallback: function() {
					jQuery("#topNavContainer").show(250);
				}
			});
		}
		
		caRelationBundle<?= $id_prefix; ?> = caUI.initRelationBundle('#<?= $id_prefix; ?>', {
			fieldNamePrefix: '<?= $id_prefix; ?>_',
			formName: '<?= $this->getVar('id_prefix'); ?>',
			templateValues: ['label', 'id', 'type_id', 'typename', 'idno_sort'],
			initialValues: <?= json_encode($this->getVar('initialValues')); ?>,
			initialValueOrder: <?= json_encode(array_keys($this->getVar('initialValues'))); ?>,
			itemID: '<?= $id_prefix; ?>Item_',
			placementID: '<?= $placement_id; ?>',
			templateClassName: 'caNewItemTemplate',
			initialValueTemplateClassName: 'caItemTemplate',
			itemListClassName: 'caItemList',
			newItemListClassName: 'caNewItemList',
			listItemClassName: 'caRelatedItem',
			addButtonClassName: 'caAddItemButton',
			deleteButtonClassName: 'caDeleteItemButton',
			hideOnNewIDList: ['<?= $id_prefix; ?>_edit_related_'],
			showEmptyFormsOnLoad: 1,
			minChars: <?= (int)$t_subject->getAppConfig()->get(["ca_entities_autocomplete_minimum_search_length", "autocomplete_minimum_search_length"]); ?>,
			relationshipTypes: <?= json_encode($this->getVar('relationship_types_by_sub_type')); ?>,
			autocompleteUrl: '<?= caNavUrl($this->request, 'lookup', 'Entity', 'Get', $lookup_params); ?>',
			types: <?= json_encode($settings['restrict_to_types'] ?? null); ?>,
			restrictToAccessPoint: <?= json_encode($settings['restrict_to_access_point'] ?? null); ?>,
			restrictToSearch: <?= json_encode($settings['restrict_to_search'] ?? null); ?>,
			bundlePreview: <?= caGetBundlePreviewForRelationshipBundle($this->getVar('initialValues')); ?>,
			readonly: <?= $read_only ? "true" : "false"; ?>,
			isSortable: <?= ($allow_drag_sort ? "true" : "false"); ?>,
			listSortOrderID: '<?= $id_prefix; ?>BundleList',
			listSortItems: 'div.roundedRel,div.listRel',
			autocompleteInputID: '<?= $id_prefix; ?>_autocomplete',
<?php if($quick_add_enabled) { ?>
			quickaddPanel: caRelationQuickAddPanel<?= $id_prefix; ?>,
			quickaddUrl: '<?= caNavUrl($this->request, 'editor/entities', 'EntityQuickAdd', 'Form', array('entity_id' => 0, 'dont_include_subtypes_in_type_restriction' => (int)($settings['dont_include_subtypes_in_type_restriction'] ?? 0), 'prepopulate_fields' => join(";", $settings['prepopulateQuickaddFields'] ?? []))); ?>',
<?php } ?>
			sortUrl: '<?= caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'Sort', array('table' => $t_item_rel->tableName())); ?>',
			
			loadedSort: <?= json_encode($loaded_sort); ?>,
			loadedSortDirection: <?= json_encode($loaded_sort_direction); ?>,
			
			interstitialButtonClassName: 'caInterstitialEditButton',
			interstitialPanel: caRelationEditorPanel<?= $id_prefix; ?>,
			interstitialUrl: '<?= caNavUrl($this->request, 'editor', 'Interstitial', 'Form', array('t' => $t_item_rel->tableName())); ?>',
			interstitialPrimaryTable: '<?= $t_instance->tableName(); ?>',
			interstitialPrimaryID: <?= (int)$t_instance->getPrimaryKey(); ?>,
			
			itemColor: '<?= $color; ?>',
			firstItemColor: '<?= $first_color; ?>',
			lastItemColor: '<?= $last_color; ?>',
			
			totalValueCount: <?= (int)$count; ?>,
			partialLoadUrl: '<?= caNavUrl($this->request, '*', '*', 'loadBundleValues', array($t_subject->primaryKey() => $t_subject->getPrimaryKey(), 'placement_id' => $placement_id, 'bundle' => 'ca_entities')); ?>',
			partialLoadIndicator: '<?= addslashes(caBusyIndicatorIcon($this->request)); ?>',
			loadSize: <?= $num_per_page; ?>,		
			
			minRepeats: <?= caGetOption('minRelationshipsPerRow', $settings, 0); ?>,
			maxRepeats: <?= caGetOption('maxRelationshipsPerRow', $settings, 65535); ?>,
			
			isSelfRelationship:<?= ($t_item_rel && $t_item_rel->isSelfRelationship()) ? 'true' : 'false'; ?>,
			subjectTypeID: <?= (int)$t_subject->getTypeID(); ?>,
			forceNewRelationships: <?= json_encode($force_values); ?>
		});
	});
</script>
