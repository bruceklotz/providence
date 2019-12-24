<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/caseindex.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Case Index V1.0.0 Results
 * @type page
 * @pageSize letter
 * @pageOrientation portrait
 * @tables ca_objects, ca_entities, ca_collections, ca_object_lots
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
	$version = "PHS Case Index V1.0.0 Results"; 
 	$t_item 				= $this->getVar('t_subject');
	$va_bundle_displays 	= $this->getVar('bundle_displays');
	$t_display 				= $this->getVar('t_display');
	$va_placements 			= $this->getVar("placements");
	//$this->setVar('headerTitle','Collections Accessions Sheet');
	$this->setVar('showPagenumber',null);
	$this->setVar('hideHeader',true);
	
	print $this->render("pdfStart.php");
	print $this->render("header.php");
	print $this->render("footer.php");
 
 	$vo_result = $this->getVar('result');	
	$vo_result->seek(0);
	$vo_result->nextHit();  //while.....		
	
	$path = $vo_result->getWithTemplate("
							<unit relativeTo='ca_storage_locations.children.external_link' delimiter=' '>
										<if rule='^ca_storage_locations.external_link.link_type=~/Public Shortcuts/'>^ca_storage_locations.external_link.url_entry</if>
							</unit>");
	
	$vs_path = caGenerateBarcode($path, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 2));
	
	$out = $vo_result->getWithTemplate("
		<div class='accession'>
			<div id='version'>$version</div>
			
			<div class='displayLocationbyCase'>
					<h1><u>^ca_storage_locations.preferred_labels</u></h1>
					<ifdef code='ca_storage_locations.description'>^ca_storage_locations.description<br></ifdef>
					<unit>
							<unit relativeTo='ca_storage_locations.children' delimiter='<br>'>
									<div class='case'>
										<div class='head'>
											<div class='key'>^ca_storage_locations.preferred_labels</div>
											<div class='qr'><img src='$vs_path.png'/></br>$path</div>
											<div class='a_bk'></div><br/>
											<div class='keydescription'>^ca_storage_locations.description</div>
										
										</div>	
										
											<unit relativeTo='ca_objects.idno' delimiter=' '><div class='object'>
													<span class='idno'>[^ca_objects.idno]</span><span class='itemname'> ^ca_objects.preferred_labels</span><br/>
													<ifdef code='ca_objects.display_text'><span class='description'>^ca_objects.display_text</span></ifdef>
										</div>	</unit>
										
									</div>
							</unit>
					</unit>
			</div>
			");

	echo $out;
	print $this->render("pdfEnd.php");
	?>