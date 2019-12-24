<?php
/* ----------------------------------------------------------------------
 * app/views/administrate/maintenance/search_reindex_landing_html.php : PELHAMHS.ORG V1.2.27
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
 
	print "<h1>"._t("Rebuild search indices")."</h1>\n";

	print "<div class='searchReindexHelpText'>";
	print    _t("<p>CollectiveAccess relies upon <em>indices</em> when searching your data.  Indices are simply summaries of your data designed to speed query processing. The precise form and characteristics of the indices used will vary with the type of search engine you  are using. They may be stored on disk, in a database or on another server, but their purpose is always the same: to make searches execute faster.</p>
            <p>For search results to be accurate the database and indices must be in sync. CollectiveAccess simultaneously updates both the database and indicies as you add, edit and delete data, keeping database and indices in agreement. Occasionally things get out of sync, however. If the basic and advanced searches are consistently returning unexpected results you can use this tool to rebuild the indices from the database and bring things back into alignment.</p> 
            <p>Note that depending upon the size of your database rebuilding can take from a few minutes to several hours. During the rebuilding process the system will remain usable but search functions may return incomplete results. Browse functions, which do not rely upon indices, will not be affected.</p>
	         ");
	
	print caFormTag($this->request, 'reindex', 'caSearchReindexForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	print "<div style='text-align: center'>".caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Rebuild search indices"), 'caSearchReindexForm', array())."</div>";
	print "</form>";
	print "</div>\n";
	
		//caUtilsx functions run via iframe...
	echo	"	<a href='$PHP_SELF?iframe=https://pelhamhs.ipower.com".__CA_URL_ROOT__."/caUtilsx.php?update_locations=1'> [update locations] </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
	echo	"	<a href='$PHP_SELF?iframe=https://pelhamhs.ipower.com".__CA_URL_ROOT__."/caUtilsx.php?create_ngrams=1'> [create_ngrams] </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
	echo	"	<a href='$PHP_SELF?iframe=https://pelhamhs.ipower.com".__CA_URL_ROOT__."/caUtilsx.php?compact=1'> [Compact Databases] </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
	echo	"	<a href='$PHP_SELF?iframe=https://pelhamhs.ipower.com".__CA_URL_ROOT__."/caUtilsx.php?reprocessmedia=1'> [Reprocess Media] </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
	$psb['tmpdir']="tmpdir=".__CA_BASE_DIR__."/app/tmp";
	$psb['now']=1;
  echo	"	<a href='$PHP_SELF?iframe=http://pelhamhs.org/cronEmptyTmp.php?tmpdir=".__CA_BASE_DIR__."/app/tmp&amp;now=1'> [Purge ca TMP files ] </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
    echo	"	<a href='$PHP_SELF?iframe=http://pelhamhs.org/cronEmptyTmp.php?psb=$psb'> [Purge ca TMP files NOW!] </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
  echo	"	<a href='$PHP_SELF?iframe=http://pelhamhs.org/cronEmptyTmp.php?tmpdir=/home/users/web/b1269/ipw.pelhamhs/public_html/pa2/app/tmp&amp;now=1'> [Purge pa TMP files NOW!] </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ;
  
	echo"<br/><hr/><br/>";
	if($_REQUEST["iframe"]){
			echo "<iframe src='".$_REQUEST["iframe"]."' width='100%' height='150px' value='Loading...'>Loading...</iframe>";
	}
	
	//list of tables to highlight and preselect for indexing
	$specials=array(
				'ca_objects', 'ca_object_lots', 'ca_places', 'ca_entities',
				'ca_occurrences', 'ca_collections', 'ca_storage_locations',
				'ca_object_representations', 'ca_representation_annotations',
				'ca_list_items'
			);
	//__CA_BASE_URL__	

	$this->opo_datamodel = Datamodel::load();
 
//-----------------------------------------------------

			
	$x=0;
	echo"<div><form action='".__CA_URL_ROOT__."/index.php/administrate/maintenance/SearchReindex/reindex' method='post' 
							id='caSearchReindexFormCB' name='tables' target='_top' enctype='multipart/form-data'>
							<div style='text-align: center;display:inline;float:left;width:auto;margins:2px;border:1px solid white;background-color:#ffffff;'>
							   <input name='allbox' type='checkbox' value='Check All'
							         onClick=\"var j = document.tables.elements.length;for (var i=0;i<j;i++){var e = document.tables.elements[i];if (e.name != 'allbox'){e.checked = document.tables.allbox.checked;}}\">
							   <i>Select all</i>
						     <input type='submit'/>
						  </div><br/><br/><br/><br/>";
	//step through all tables, preselecting special tables, building a form...
	require_once(__CA_LIB_DIR__."/Search/SearchIndexer.php");
	$si = new SearchIndexer();
	
	//foreach($va_table_names = Datamodel::getTableNames() as $vs_table){
	foreach($va_table_names = $si->getIndexedTables() as $vs_table){//echo print_r($vs_table,1)."XXXXX";
			$x++;
			$boxwidth = strlen($vs_table['name'])*0.4+10;
			$boxwidth = (string)$boxwidth."em;"; 
			
			if (in_array($vs_table,$specials)){
					$style="style='text-align: left;display:inline-block;float:left;word-wrap: break-word;margins:2px;border:1px solid white;background-color:orange;width:$boxwidth'";
					$checked="checked";
			}else{
					$style="style='text-align: left;display:inline-block;word-wrap: break-word;float:left;margins:2px;border:1px solid white;background-color:#f5f5f5;width:$boxwidth'";
					$checked="";
			}
			
			//hack to set ca_list_items 
			if ($vs_table =="ca_list_items"){
					$style="style='text-align:left;display:inline-block;float:left;word-wrap: break-word;margins:2px;border:1px solid white;background-color:green;width:$boxwidth'";
					$checked="";
			}
			if (in_array($vs_table,$specials) or true){
			
			
			echo 	"   <div ".$style.">";
			//echo	"     	<input type='checkbox' name='seltable[".$vs_table['num']."]' value='".$vs_table['name']."' ".$checked." />
			echo	"     	<input type='checkbox' name='seltable[".$x."]' value='".$vs_table['name']."' ".$checked." />
			
			
					          <a href='#' onclick=' jQuery(\"#caSearchReindexFormCB\").submit();' class='form-button 1451065697'>
						         <span  style='font-size:8px !important;padding-top:9px;'>
							        <img src='".__CA_THEMES_URL__."/default/graphics/icons/browse_arrow.png'
							            border='0' class='form-button-left hierarchyIcon' style='padding-right: 10px' />
							        [ ".$x." ] ".$vs_table['name']."<br/>&nbsp;&nbsp;&nbsp;&nbsp;cnt:".$vs_table['count']." - tbl# ".$vs_table['num']."
						         </span>
					         </a>
				        </div>";
			}//if in_array
						
	}
echo "	    </form> 
		 </div>
		 <div style='clear:both;height:100px;'><hr/></div>";
	
	

?>