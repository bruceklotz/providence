<?php
/* ----------------------------------------------------------------------
 * themes/default/views/find/search_controls_html.php 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2016 Whirl-i-Gig
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
 	
 	$t_subject = $this->getVar('t_subject');
 	$vs_table = $t_subject->tableName();
 	$va_lookup_urls = caJSONLookupServiceUrl($this->request, $vs_table, array('noInline' => 1));
 	$vo_result_context = $this->getVar('result_context');
 	
 	$vs_type_id_form_element = '';
	if ($vn_type_id = intval($this->getVar('type_id'))) {
		$vs_type_id_form_element = '<input type="hidden" name="type_id" value="'.$vn_type_id.'"/>';
	}

	$vo_query_builder_config = Configuration::load($this->request->config->get('search_query_builder_config'));
	$vb_query_builder_enabled = $vo_query_builder_config->getBoolean('query_builder_enabled') && $vo_query_builder_config->getBoolean('query_builder_enabled_' . $vs_table);
	$vs_query_builder_toggle = '';
	if ($vb_query_builder_enabled) {
		require_once(__CA_MODELS_DIR__ . '/ca_search_forms.php');
		$vs_query_builder_toggle = ' <a href="#" class="button" id="QueryBuilderToggle">'._t('Query Builder').'&nbsp;&#9662;</a>';
	}

	if (!$this->request->isAjax()) {
		print caFormTag($this->request, 'Index', 'BasicSearchForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true));
		if (!$this->getVar('uses_hierarchy_browser')) {
			print caFormControlBox(
				'<div class="simple-search-box">'._t('Search').': <input type="text" id="BasicSearchInput" name="search" value="'.htmlspecialchars($this->getVar('search'), ENT_QUOTES, 'UTF-8').'" size="40"/>'.$vs_type_id_form_element.$vs_query_builder_toggle.'</div>',
				'<a href="#" onclick="caSaveSearch(\'BasicSearchForm\', jQuery(\'#BasicSearchInput\').val(), [\'search\']); return false;" class="button">'._t('Save search').'&nbsp;&rsaquo;</a>',
				caFormSubmitButton($this->request, __CA_NAV_BUTTON_SEARCH__, _t("Search"), 'BasicSearchForm')
			);
		} else {
			print caFormControlBox(
				'<div class="simple-search-box">'._t('Search').': <input type="text" id="BasicSearchInput" name="search" value="'.htmlspecialchars($this->getVar('search'), ENT_QUOTES, 'UTF-8').'" size="40"/>'.$vs_query_builder_toggle.'</div>'.caFormSubmitButton($this->request, __CA_NAV_BUTTON_SEARCH__, _t("Search"), 'BasicSearchForm'),
				'<a href="#" onclick="caSaveSearch(\'BasicSearchForm\', jQuery(\'#BasicSearchInput\').val(), [\'search\']); return false;" class="button">'._t('Save search').'&nbsp;&rsaquo;</a>',
				'<a href="#" id="browseToggle" class="form-button"></a>'
			);
		}
		?>
		</form>
		<?php
		if ($vb_query_builder_enabled) {
 		?>
		<div id="QueryBuilderContainer">
			<div id="QueryBuilder"></div>
		</div>
		<?php
		}
		if ($this->getVar('uses_hierarchy_browser')) {
		?>
			<div id="browse">
				<div class='subTitle' style='background-color: #eeeeee; padding:5px 0px 5px 5px;'><?php print _t("Hierarchy"); ?></div>
			<?php
			if ($this->request->user->canDoAction('can_edit_'.$vs_table) && ($this->getVar('num_types') > 0)) {
			?>
				<!--- BEGIN HIERARCHY BROWSER TYPE MENU --->
				<div id='browseTypeMenu'>
					<form action='#'>
						<div>
							<?php
							print _t('Add under %2 new %1', $this->getVar('type_menu').' <a href="#" onclick="_navigateToNewForm(jQuery(\'#hierTypeList\').val())">'.caNavIcon($this->request, __CA_NAV_BUTTON_ADD__)."</a>", "<span id='browseCurrentSelection'></span>");
							?>
						</div>
					</form>
	
				</div><!-- end browseTypeMenu -->		
				<!--- END HIERARCHY BROWSER TYPE MENU --->
			<?php
			}
			?>
				<div class='clear' style='height:1px;'><!-- empty --></div>
				
				<!--- BEGIN HIERARCHY BROWSER --->
				<div id="hierarchyBrowser" class='hierarchyBrowser'>
					<!-- Content for hierarchy browser is dynamically inserted here by ca.hierbrowser -->
				</div><!-- end hierarchyBrowser -->
			</div><!-- end browse -->
			<script type="text/javascript">
				var oHierBrowser;
				var stateCookieJar = jQuery.cookieJar('caCookieJar');
				
				jQuery(document).ready(function() {
					
					jQuery('#browseTypeMenu .sf-hier-menu .sf-menu a').click(function() { 
						jQuery(document).attr('location', jQuery(this).attr('href') + oHierBrowser.getSelectedItemID());	
						return false;
					});	
					
					oHierBrowser = caUI.initHierBrowser('hierarchyBrowser', {
						levelDataUrl: '<?php print $va_lookup_urls['levelList']; ?>',
						initDataUrl: '<?php print $va_lookup_urls['ancestorList']; ?>',
						
						editUrl: '<?php print caEditorUrl($this->request, $vs_table, null, false, array(), array('action' => $this->getVar('default_action'))); ?>',
						editButtonIcon: "<?php print caNavIcon($this->request, __CA_NAV_BUTTON_RIGHT_ARROW__); ?>",
						disabledButtonIcon: "<?php print caNavIcon($this->request, __CA_NAV_BUTTON_DOT__); ?>",

						disabledItems: 'full',
						
						initItemID: '<?php print $this->getVar('browse_last_id'); ?>',
						indicatorUrl: '<?php print $this->request->getThemeUrlPath(); ?>/graphics/icons/indicator.gif',
						typeMenuID: 'browseTypeMenu',
						disabledItems: 'full',
						
						currentSelectionDisplayID: 'browseCurrentSelection'
					});
					
					jQuery('#BasicSearchInput').autocomplete(
						{
							minLength: 3, delay: 800, html: true,
							source: '<?php print $va_lookup_urls['search']; ?>',
							select: function(event, ui) {
								if (parseInt(ui.item.id) > 0) {
									oHierBrowser.setUpHierarchy(ui.item.id);	// jump browser to selected item
									if (stateCookieJar.get('<?php print $vs_table; ?>BrowserIsClosed') == 1) {
										jQuery("#browseToggle").click();
									}
								}
								event.preventDefault();
								jQuery('#BasicSearchInput').val('');
							}
						}
					);
					jQuery("#browseToggle").click(function() {
						jQuery("#browse").slideToggle(350, function() { 
							stateCookieJar.set('<?php print $vs_table; ?>BrowserIsClosed', (this.style.display == 'block') ? 0 : 1); 
							jQuery("#browseToggle").html((this.style.display == 'block') ? '<?php print '<span class="form-button">'.addslashes(_t('Close hierarchy viewer')).'</span>';?>' : '<?php print '<span class="form-button">'._t('Open hierarchy viewer').'</span>';?>');
						}); 
						return false;
					});
					
					if (<?php print ($this->getVar('force_hierarchy_browser_open') ? 'true' : "!stateCookieJar.get('{$vs_table}BrowserIsClosed')"); ?>) {
						jQuery("#browseToggle").html('<?php print '<span class="form-button">'.addslashes(_t('Close hierarchy viewer')).'</span>';?>');
					} else {
						jQuery("#browse").hide();
						jQuery("#browseToggle").html('<?php print '<span class="form-button">'.addslashes(_t('Open hierarchy viewer')).'</span>';?>');
					}
				});
				
					
				function caOpenBrowserWith(id) {
					if (stateCookieJar.get('<?php print $vs_table; ?>BrowserIsClosed') == 1) {
						jQuery("#browseToggle").click();
					}
					oHierBrowser.setUpHierarchy(id);
				}
				function caCloseBrowser() {
					if (!stateCookieJar.get('<?php print $vs_table; ?>BrowserIsClosed')) {
						jQuery("#browseToggle").click();
					}
				}
				function _navigateToNewForm(type_id) {
					document.location = '<?php print caEditorUrl($this->request, $vs_table, 0); ?>/type_id/' + type_id + '/parent_id/' + oHierBrowser.getSelectedItemID();
				}
			</script>
				<!--- END HIERARCHY BROWSER --->
			<br />
		<?php
		}
	}
?>

<script type="text/javascript">
	function caSaveSearch(form_id, label, field_names) {
		var vals = {};
		jQuery(field_names).each(function(i, field_name) { 	// process all fields in form
			vals[field_name] = jQuery('#' + form_id + ' [name=' + field_name + ']').val();	
		});
		vals['_label'] = label;	// special "display" title, used if all else fails
		vals['_field_list'] = field_names; // an array for form fields to expect
		jQuery.getJSON('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), "addSavedSearch"); ?>', vals, function(data, status) {
			if ((data) && (data.md5)) {
				jQuery('.savedSearchSelect').prepend(jQuery("<option></option>").attr("value", data.md5).text(data.label)).attr('selectedIndex', 0);
			}
		});
	}

	// Show "add to set" controls if set tools is open
	jQuery(document).ready(function() {
		if (jQuery("#searchSetTools").is(":visible")) {
			jQuery(".addItemToSetControl").show();
		}
	});

	<?php
	if ($vb_query_builder_enabled) {
		$vo_query_builder_options = $vo_query_builder_config->get('query_builder_global_options') ?: array();
		$vo_query_builder_options['filters'] = caGetQueryBuilderFilters($t_subject, $vo_query_builder_config);
		$vo_query_builder_options['allow_empty'] = true;
	?>
	function caUpdateSearchQueryBuilderToggleText() {
		jQuery('#QueryBuilderToggle').html('<?php print _t('Query Builder'); ?>&nbsp;' + (caSearchQueryBuilderIsVisible() ? '&#9652;' : '&#9662;'));
	}

	// Event handler for the query builder toggle
	function caToggleSearchQueryBuilder() {
		var $container = jQuery('#QueryBuilderContainer');
		$container.slideToggle('medium');
		jQuery.cookieJar('caCookieJar').set('<?php print $vs_table; ?>QueryBuilderIsExpanded', !caSearchQueryBuilderIsVisible());
		caUpdateSearchQueryBuilderToggleText();
		caSetSearchQueryBuilderRulesFromSearchInput();
		return false;
	}

	function caSearchQueryBuilderIsVisible() {
		return jQuery.cookieJar('caCookieJar').get('<?php print $vs_table; ?>QueryBuilderIsExpanded');
	}

	function caSetSearchQueryBuilderRulesFromSearchInput() {
		var rules;
		if (!caSearchQueryBuilderIsVisible()) {
			return;
		}
		try {
			rules = caUI.convertSearchQueryToQueryBuilderRuleSet(jQuery('#BasicSearchInput').val());
			if (rules) {
				jQuery('#QueryBuilder').queryBuilder('setRules', rules);
			}
		} catch (e) {
			// TODO Something else? Display to user?
			if (console && console.error) {
				console.error(e);
			}
		}
	}

	function caSetSearchInputQueryFromQueryBuilder() {
		var query, rules;
		if (!caSearchQueryBuilderIsVisible()) {
			return;
		}
		rules = jQuery('#QueryBuilder').queryBuilder('getRules');
		if (rules) {
			query = caUI.convertQueryBuilderRuleSetToSearchQuery(rules);
			if (query) {
				jQuery('#BasicSearchInput').val(query);
			}
		}
	}

	function caGetSearchQueryBuilderUpdateEvents() {
		return [
			'afterAddGroup.queryBuilder',
			'afterDeleteGroup.queryBuilder',
			'afterAddRule.queryBuilder',
			'afterDeleteRule.queryBuilder',
			'afterUpdateRuleValue.queryBuilder',
			'afterUpdateRuleFilter.queryBuilder',
			'afterUpdateRuleOperator.queryBuilder'
		].join(' ');
	}

	// Initialise query builder
	jQuery(document).ready(function() {
		jQuery('#QueryBuilderContainer').toggle(caSearchQueryBuilderIsVisible());
		jQuery('#QueryBuilder')
			.queryBuilder(<?php print json_encode($vo_query_builder_options, JSON_PRETTY_PRINT) ?>)
			.on(caGetSearchQueryBuilderUpdateEvents(), caSetSearchInputQueryFromQueryBuilder);
		jQuery('#QueryBuilderToggle').click(caToggleSearchQueryBuilder);
		jQuery('#BasicSearchInput').change(caSetSearchQueryBuilderRulesFromSearchInput);
		caUpdateSearchQueryBuilderToggleText();
		caSetSearchQueryBuilderRulesFromSearchInput();
	});
	<?php
	}
	?>
</script>
