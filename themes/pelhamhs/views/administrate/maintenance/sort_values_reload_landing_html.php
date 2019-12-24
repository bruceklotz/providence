<?php
/* ----------------------------------------------------------------------
 * app/views/administrate/maintenance/sort_values_reload_landing_html.php :PELHAMHS
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2011 Whirl-i-Gig
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
 
	print "<h1>"._t("Reload sort values")."</h1>";

	print "<div class='searchReindexHelpText'>";
	print _t("<p>CollectiveAccess relies upon <em>sort values</em> when sorting values that should not sort alphabetically, such as titles with articles (eg. <em>The Man Who Fell to Earth</em> should sort as <em>Man Who Fell to Earth, The</em>) and alphanumeric identifiers (eg. <em>2011.001</em> and <em>2011.2</em> should sort next to each other with leading zeros in the first ignored).</p>
<p>Sort values are derived from corresponding values in your database. The internal format of sort values can vary between versions of CollectiveAccess causing erroneous sorting behavior after an upgrade. If you notice values such as titles and identifiers are sorting incorrectly, you may need to reload sort values from your data.</p> 
<p>Note that depending upon the size of your database reloading sort values can take from a few minutes to an hour or more. During the reloading process the system will remain usable but search and browse functions may return incorrectly sorted results. </p>
	");
	
	print caFormTag($this->request, 'reload', 'caSortValuesReloadForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort values"), 'caSortValuesReloadForm', array())."</div>";
	print "</form>";
	print "</div>";
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadFormA', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort All values"), 'caSortValuesReloadFormA', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='all'>";
	print "</form>";
	print "</div>";

echo "<div style='float:left;width:240px;'>".caFormTag($this->request, 'reload', 'caSortValuesReloadFormB', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort all but ca_list_values"), 'caSortValuesReloadFormB', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='short'>";
	print "</form>";
	print "</div>";

echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadFormC', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_list_items"), 'caSortValuesReloadFormC', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='list_items'>";
	print "</form>";
	print "</div>";

echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadFormE', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_list_items 1"), 'caSortValuesReloadFormE', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='list_items1'>";
	print "</form></div>";
	
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadFormF', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_list_items 2"), 'caSortValuesReloadFormF', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='list_items2'>";
	print "</form></div>";
	
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadFormD', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_objects"), 'caSortValuesReloadFormD', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_objects'>";
	print "</form></div>";
	
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm1', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_object_lots"), 'caSortValuesReloadFormD', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_object_lots'>";
	print "</form></div>";

echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm2', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_places"), 'caSortValuesReloadForm2', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_places'>";
	print "</form></div>";

echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm3', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_entities"), 'caSortValuesReloadForm3', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_entities'>";
	print "</form></div>";

echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm4', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_occurrences"), 'caSortValuesReloadForm4', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_occurrences'>";
	print "</form></div>";
	
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm5', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_collections"), 'caSortValuesReloadForm5', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_collections'>";
	print "</form></div>";
	
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm6', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_storage_locations"), 'caSortValuesReloadForm6', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_storage_locations'>";
	print "</form></div>";
	
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm7', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_object_representations"), 'caSortValuesReloadForm7', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_object_representations'>";
	print "</form></div>";
	
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm8', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_representation_annotations"), 'caSortValuesReloadForm8', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_representation_annotations'>";
	print "</form></div>";

echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm9', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_loans"), 'caSortValuesReloadForm9', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_loans'>";
	print "</form></div>";

echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm10', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_movements"), 'caSortValuesReloadForm10', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_movements'>";
	print "</form></div>";

echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm11', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_tours"), 'caSortValuesReloadForm11', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_tours'>";
	print "</form></div>";

echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadForm12', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort ca_tour_stops"), 'caSortValuesReloadForm12', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='bytable'><input type='hidden' name='tablename' value='ca_tour_stops'>";
	print "</form></div>";


echo"<hr/>";
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadFormg1', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort Group 1"), 'caSortValuesReloadFormg1', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='group1'>";
	print "</form></div>";	
	
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadFormg2', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort Group 2"), 'caSortValuesReloadFormg2', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='group2'>";
	print "</form></div>";
	
echo "<div style='float:left;width:240px;'>". caFormTag($this->request, 'reload', 'caSortValuesReloadFormg3', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Reload sort Group 3"), 'caSortValuesReloadFormg3', array())."</div>";
echo" <input type='hidden' name='reloadmode' value='group3'>";
	print "</form></div>";		
	
	print "</div>";		
	?>