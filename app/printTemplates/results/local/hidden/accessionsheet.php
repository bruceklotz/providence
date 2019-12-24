<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/accessionsheet.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Accession Sheet V1.1.5 Results
 * @type page
 * @pageSize letter
 * @pageOrientation portrait
 * @tables *
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
	$version = "PHS Accession Sheet V1.1.6 Results"; 
 	$t_item 				= $this->getVar('t_subject');
	$va_bundle_displays 	= $this->getVar('bundle_displays');
	$t_display 				= $this->getVar('t_display');
	$va_placements 			= $this->getVar("placements");
	$this->setVar('headerTitle','Collections Accessions Sheet');
	$this->setVar('showPagenumber',true);
	//$this->setVar('marginTop',20);

 
 	$vo_result = $this->getVar('result');	
	$vo_result->seek(0);
	$vo_result->nextHit();  //while.....
	
	$textpage=$vo_result->getWithTemplate("
			<unit relativeTo='ca_entities' delimiter=', ' restrictToRelationshipTypes='owners'>^ca_entities.preferred_labels.displayname</unit>
			^ca_objects.provenance
			<unit delimiter=' '>	
					<div class='conditiond'> ^ca_objects.condition_report.condition_date </div>
					<div class='conditionu'> ^ca_objects.condition_report.condition_by </div>
					<div class='conditionn'> ^ca_objects.condition_report.condition_notes</div>
					<br/>
			</unit>");
	$textdesc = $vo_result->getWithTemplate("^ca_objects.description%delimiter=,_ ");		
		// If the description or textpage is larger than 100 then make report two pages long.
		if(strlen($textdesc)> 100 or strlen($textpage)> 100)
			{	$twopages="2";
				$pagebreak="<div class='pageBreak' style='page-break-before: always;'>&nbsp;</div>\n";
				$this->setVar('showPagenumber',true);
		}else{
				$twopages="";
				$pagebreak="";
		}
		
				
	
	$out = $vo_result->getWithTemplate("
		<div class='accession'>
			<div id='version'>$version</div>
			<div id='name'><div class='l_left'>Name:</div><div class='d_full'>^ca_objects.preferred_labels</div><div class='a_bk'></div></div>
			<div id='mediar'><img src='".$vo_result->getMediaPath('ca_object_representations.media', 'preview')."'></div>
			
			");
	
	//^ca_object_representations.media</div>");
	
	$root = 'http://pelhamhs.org/ca/index.php/editor/objects/ObjectEditor/Edit/object_id/';
	$path = $vo_result->getWithTemplate($root."^ca_objects.object_id");
	$vs_path = caGenerateBarcode($path, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 3));
	
	$out .= "<div id='barcode'><img src='$vs_path.png'/><br/>$path</div>";
	
	$out .= $vo_result->getWithTemplate("
			<div id='idnol'>Id:</div><div id='idno'>^ca_objects.idno</div>	
			<div id='titlel'>Title:</div><div id='titled'>^ca_objects.nonpreferred_labels%delimiter=,_ </div>
	");

	$lex_item_array = explode("|",$vo_result->getWithTemplate("	
		<unit relativeTo='ca_objects.lexicon3.hierarchy'   removeFirstItems='2' delimiter= ' | ' >^ca_objects.lexicon3.preferred_labels</unit>"));
	$lex_hier_raw =	str_replace(array("&amp;",";"),array("&"," --> "),$vo_result->getWithTemplate(" ^ca_objects.lexicon3.hierarchy.preferred_labels"));			
	$lex_hier_array = explode("Root node for lexicon3 --> ",$lex_hier_raw);
		
	$lexout = "
		<div id='lexicon3'>
				<div class='lexicon3l'>Catagories:</div>
				<div class='lexicon3d'>";
					foreach($lex_item_array as $key=>$item){
						$lexout .= "<div class='lexicon3t'> $item</div><div class='lexicon3h'> ".preg_replace('/--> $/','',$lex_hier_array[$key+1])."</div>";
					} 
		
	$lexout .="
	 			</div>
				<div class='a_bk'></div>
		</div>";
	//+++++++++++++++++++++++
/*				^ca_objects.lexicon3.hierarchy.preferred_labels%removeFirstItems=1%delimiter=_>".unichr(0x27a0).">_ 
	$lexout =  $vo_result->getWithTemplate("
			<div id='lexicon3'>
				<div class='lexicon3l'>Catagories:</div>
				<div class='lexicon3d'>
						{{{<unit relativeTo='ca_objects'>c ^ca_list.lexicon3 d</unit>}}}<br/>
						{{{^ca_objects.lexicon3.hierarchy.preferred_labels%removeFirstItems=1%delimiter=_".unichr(0x27a8)."_ }}}
				</div>
				<div class='a_bk'></div>
		</div>");			
		
	*/	
	
	$out .= $lexout;
	

	
	$out .= "
	
			<div id='dimension'>
			<div class='dimensionl'>Dimensions:&nbsp;<br/><br/>Weight:&nbsp;</div>
			<div class='dimensiond'>
				^ca_objects.dimensions.dimensions_length<ifnotdef code='ca_objects.dimensions.dimensions_length'>____</ifnotdef> Length x 
				^ca_objects.dimensions.dimensions_width<ifnotdef code='ca_objects.dimensions.dimensions_width'>_____</ifnotdef> Width x 
				^ca_objects.dimensions.dimensions_height<ifnotdef code='ca_objects.dimensions.dimensions_height'>____</ifnotdef> Height<br/> 
				^ca_objects.dimensions.dimensions_thickness<ifnotdef code='ca_objects.dimensions.dimensions_thickness'>____</ifnotdef>Thickness<br/>
				<ifnotdef code='ca_objects.dimensions.dimensions_weight'>____</ifnotdef>^ca_objects.dimensions.dimensions_weight
				<b>Notes:</b> ^ca_objects.dimensions.measurement_notes
			</div>
			<div class='a_bk'></div>
		</div>
		<div id='status'>
			<div class='statusl'>Status: </div>
			<div class='statusd'>
					^ca_objects.status (<i>^ca_objects.item_status_id</i>)
			</div>
			<div class='a_bk'></div>
		</div>
		<ifdef code='ca_objects.is_deaccessioned'>
			<div id='deaccession'>
			
				<unit delimiter=' '>
					<div class='deaccessionl'>*** DE-ACCESSIONED! ***</div>
					<div class='deaccessiond'> ^ca_objects.deaccession_date &nbsp; - &nbsp; <i>^ca_objects.deaccession_type_id </i></div> 
					<div class='deaccessionn'>^ca_objects.deaccession_notes</div>
				</unit>
			</div>
		</ifdef>
		
		<div class='a_bk'></div>
		<div id='makerl'>Maker:</div>
		<div id='maker'><unit relativeTo='ca_entities' delimiter=', ' restrictToRelationshipTypes='creator'>
				^ca_entities.preferred_labels.displayname 
		</unit></div>	
	
		<div id='makedatel'>Date Created:</div>
		<div id='makedate'>
			<unit  delimiter='<br/> '><if rule='^ca_objects.date.dc_dates_types =~ /Date created/'>
		 		^ca_objects.date.dates_value 
			</if></unit>
		</div>
	
		<div id='datecopyrl'>Copyrighted:</div>
		<div id='datecopyr'>
			<unit  delimiter='<br/> '><if rule='^ca_objects.date.dc_dates_types =~ /Date copyrighted/'>
		 		^ca_objects.date.dates_value 
			</if></unit>
		</div>	

		<div id='lotl'>Lot:</div><div id='lot'>^ca_object_lots</div>	
		<div id='collectionl'>Collection:</div><div id='collection'>^ca_collections</div>
	
		<div id='location'>
			<div class='locationl'>Location:</div>
			<div class='locationd'>^ca_storage_locations.preferred_labels
				<span class='locationp'>(
					<unit relativeTo='ca_storage_locations' delimiter=\"<span class='arrowr'> ---> </span>\" removeFirstItems='1' >
						^ca_storage_locations.hierarchy.preferred_labels.name
					</unit>
				)</span>
			</div>
			<div class='a_bk'></div>
		</div>

		<div id='source'>
			<div class='sourcel'>Donor:&nbsp;</div>
			<div class='sourced'>
				<unit relativeTo='ca_entities' delimiter=', ' restrictToRelationshipTypes='donor'>
					^ca_entities.preferred_labels.displayname 
				</unit>
			</div>	
			<div class='a_bk'></div>
			<div class='sourcel'>Source:&nbsp;</div>
			<div class='sourced'>
				<unit relativeTo='ca_entities' delimiter=', ' restrictToRelationshipTypes='source'>
					^ca_entities.preferred_labels.displayname 
				</unit>
			</div>	
			<div class='a_bk'></div>
		</div>

		<div id='description$twopages'>
			<div class='l_left'>Description: </div><div class='descriptiond$twopages'> ^ca_objects.description%delimiter=,_ </div>
			<div class='a_bk'></div>
		</div>
	
		<div id='materialsl'>Materials: </div><div id='materials'> ^ca_objects.materials%delimiter=,_</div>		
	$pagebreak
		<div id='provenance$twopages'>
			<div class='l_left'>Provenance:&nbsp; </div>
			<div class='provenanced'>^ca_objects.provenance</div>
		</div>
	
		<div id='ownersl$twopages'>Owners:</div>
		<div id='owners$twopages'><unit relativeTo='ca_entities' delimiter=', ' restrictToRelationshipTypes='owners'>
			^ca_entities.preferred_labels.displayname 
		</unit></div>
		<div class='a_bk'></div>
		<div id='condition$twopages'>
			<div class='l_left'>Condition:&nbsp;</div>
			<div class='d_full'>
				<unit delimiter=' '>	
					<div class='conditiond'> ^ca_objects.condition_report.condition_date </div>
					<div class='conditionu'> ^ca_objects.condition_report.condition_by </div>
					<div class='conditionn'> ^ca_objects.condition_report.condition_notes </div>
					<br/>
				</unit>
			</div>
			<div class='a_bk'></div>	
		</div>
		<div class='a_bk'></div>
		<div id='cataloged'>
			<unit delimiter=' '>
				<div class='catalogedl'>^ca_objects.catalogedby.cataloged_date_type</div>
				<div class='catalogedd'><b>:</b> ^ca_objects.catalogedby.catalogdate</div>
				<div class='catalogedn'><i> ^ca_objects.catalogedby.cataloger </i> ^ca_objects.catalogedby.notes</div>
				<div class='a_bk'></div>
			</unit>
		</div>
		<div class='a_bk'></div>

	</div>";
	$out = $vo_result->getWithTemplate($out);
	print $this->render("pdfStart.php");
	print $this->render("header.php");
	print $this->render("footer.php");	
	/*<div class='pageBreak' style='page-break-before: always;'>&nbsp;</div>\n*/
	echo $out;
	print $this->render("pdfEnd.php");
	?>