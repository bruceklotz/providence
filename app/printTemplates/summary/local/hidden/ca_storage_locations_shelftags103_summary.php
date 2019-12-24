<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/summary/local/ca_storage_locations_shelftags_summary.php
 * ----------------------------------------------------------------------
 
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Shelf Tags Summary V1.0.2
 * @type page
 * @pageSize letter
 * @pageOrientation portrait
 * @tables *
 *
 * @marginTop 0.5in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
  global $rowcount,$rowsperpage,$recursivelimit;
	$version = "PHS Shelf Tags Summary V1.0.3"; 
	$t_item 				= $this->getVar('t_subject');
	$va_bundle_displays 	= $this->getVar('bundle_displays');
	$t_display 				= $this->getVar('t_display');
	$va_placements 			= $this->getVar("placements");
	
	$rowcount=1;
	$rowsperpage=10;
	$recursivelimit=3;	
	$recursive = $recursivelimit;	
	
	
	
	
	$this->setVar('version',$version);
	$this->setVar('headerTitle',$t_item->getWithTemplate("^ca_storage_locations.preferred_labels")."<div class='recursivenote'>Recursive Limit: $recursivelimit</div>");
/*
	Report Header
		Object contained in THIS Storage Location												ie: object1,object2
		Storage Locations contained in THIS Storage Location						ie:	Shelf1, Shelf2
			Objects contained with Child Storage Location									ie: object3,object4
				--Storage Locations contained in the Child Storage Location	ie: box1, box2



*/

 	
/************************************************/
/******************************************
	 Functions
	 ******************************************/
	 if (!function_exists('getListofObjectsInThisStorageLocation')){
	 function getListofObjectsInThisStorageLocation($this_location){
	 		global $rowcount,$rowsperpage;
	 		$out="";
			$parent_locationid = $this_location->get('ca_storage_locations.location_id');
			
			// Get all objects within THIS child Storage_Location
			$this_objects = $this_location->get('ca_objects.object_id', array('returnAsArray' => true));
			foreach ($this_objects as $key => $the_object_id) {
						$the_object = new ca_objects($the_object_id);
		
						$child_storage_ids = $the_object->get('ca_storage_locations.location_id',array('returnAsArray' => true));
						$child_status =	$the_object->get('ca_objects_x_storage_locations.storage_location_status',array('convertCodesToDisplayText' => true,'returnAsArray' => true));
			
						// Step through all of the current Object's storage_locations
						foreach ($child_storage_ids as $child_key => $child_id) {
							$xc_storages =$the_object->get('ca_storage_locations',array('convertCodesToDisplayText' => true,'returnAsArray' => true));
							$xc_storage = $xc_storages[$child_key];
							$pattern = "/" . preg_quote("Inactive", "/") . "/"; 
							if ( ($child_id == $parent_locationid) & ( !preg_match($pattern, $child_status[$child_key]) ) ) {
									// If we are working with THIS storage_location and NOT an Inactive location...
									$rowcount++;
									
									// Generate Barcode:	
									//$root = "http://pelhamhs.org/ca/index.php/editor/objects/ObjectEditor/Edit/object_id/"; // this should get pulled out or automated somehow.
									//$path =$root.$the_object->get('ca_objects.idno');$path=$root;// $the_object->get('ca_objects.idno');
									//$vs_path = caGenerateBarcode($path, array( 'checkValues' => $the_object->opa_check_values,'type' => 'qrcode', 'height' => 1));
	
	
									
									
									$out .="
			 							<tr>
   				   					<td ><div class='box'>&nbsp;</div></td>
   				   					<td class='case'>".$the_object->get('ca_objects.idno')."
   				   						<div class='casetype'>".$the_object->get('ca_objects.type_id',array('convertCodesToDisplayText' => true))."</div>
   				   					</td>
   				   					<td>".$the_object->get('ca_objects.preferred_labels')."</td>
   				   					<td>".$the_object->get('ca_object_representations.related.media.tiny')."</td>
   				   					<td>$xc_storage 
   				   							<div style='font-size:6px;'>".date("M d Y")."</div>
   				   					</td>
   									</tr>";
   			//<div style='font-size:6px;'>".$child_status[$child_key]."</div>
   								if($rowcount > $rowsperpage){
   								$rowcount =1;
   								$out .= "</table><div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><br/><table>";
   							}
   						}// if $child_id ==...
   					}//foreach $child_storage_ids
   		}//foreach Object within THIS storage Location
   		return $out;
	 }//end function	
	 }//if function exists
/***************************************************/
	 if (!function_exists('getListofStorageInThisStorageLocation')){
	 function getListofStorageInThisStorageLocation($this_location,$recursive){
	 		global $rowcount,$rowsperpage,$recursivelimit;
	 		$out="";
			
	 		// Now get all children storage_locations within THIS Storage_Location
			$this_storage =$this_location->get('ca_storage_locations.preferred_labels',array('convertCodesToDisplayText' => true));
	$va_storage_children = $this_location->get('ca_storage_locations.children.location_id', array('returnAsArray' => true));
	foreach ($va_storage_children as $va_key => $va_storage_children_id) {
			$t_storage_location = new ca_storage_locations($va_storage_children_id);
			
			//$x_statuss =	$tc_object->get('ca_objects_x_storage_locations.storage_location_status',array('convertCodesToDisplayText' => true,'returnAsArray' => true));
			
			#information about this (child) storage location
			$vs_storage_type = $t_storage_location->get('ca_storage_locations.type_id', array('convertCodesToDisplayText' => true));
			$storagetypehtml="<div class='casetype'>$vs_storage_type</div>"; //default casetype style
			if ($vs_storage_type == "box") {$storagetypehtml="<div class='arrow-right'></div><div class='casetypebox'>$vs_storage_type</div>";}
			if ($vs_storage_type == "shelf") {$storagetypehtml = "<div class='arrow-down'></div><div class='casetypeshelf'>$vs_storage_type</div>";}
		
			$rowcount++;
			$out .="
			 		<tr>
   				   <td ><div class='box'>&nbsp;</div></td>
   				   <td class='case'>".$t_storage_location->get('ca_storage_locations.idno')."
   				      $storagetypehtml
   				   </td>
   				   <td>".$t_storage_location->get('ca_storage_locations.preferred_labels')."</td>
   				   <td>".$t_storage_location->get('ca_object_representations.media.tiny')."</td>
   				   <td>".print_r($this_storage,1)." 
   				   		<div style='font-size:6px;'>".$x_statuss[$x_key]." ($recursive)</div>
   				   		<div style='font-size:6px;'>".date("M d Y")."</div>
   				   </td>
   				</tr>";
			if($rowcount > $rowsperpage){
   								$rowcount =1;
   								$out .= "</table><div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><br/><table>";
   							}
   		$out .= getListofObjectsInThisStorageLocation($t_storage_location);
   		if($recursive > 0){$recursive--; $out .= getListofStorageInThisStorageLocation($t_storage_location,$recursive);}else{$recursive=$recursivelimit;}
		}//foreach $va_storage_children
   	
	 return $out;
	 }//function getListofStorageInThisStorageLocation
	 }//function_exists getListofStorageInThisStorageLocation
/***************************************************/
 	
 	
 	
 	
 	// Report header/title
	$out = str_replace("zzzzz",">>>",$t_item->getWithTemplate("
								<div class='storagepath'><br/><br/>[ ^ca_storage_locations.hierarchy.preferred_labels.name%removeFirstItems=1%delimiter=_zzzzz_  ]</div>
								<ifdef code='ca_storage_locations.description'><div class='storagepathdescription'>^ca_storage_locations.description<br/></div></ifdef>
								"));

	
	$out .= "<div class='storageshelftag'><table>";
		
	
	// First get all objects within THIS Storage_Location
	$out .= getListofObjectsInThisStorageLocation($t_item);
	$out .= getListofStorageInThisStorageLocation($t_item,$recursive);

 		$out .= " </table></div>";
		print $this->render("pdfStart.php");
		echo "
		<style type='text/css'>
			.storageshelftag {text-align:left;margin-left:auto;margin-right:auto;width:100%;}
			.storageshelftag table {width:100%;cellspacing:0px; cellpadding:0px;border:1px solid black; border-collapse: collapse;}
			.storageshelftag td{height:75px;padding:4px; border-top:1px solid black; border-bottom:1px solid black;margin:0px;}
			.storageshelftag tr{}
			.recursivenote {font-size:8px;}
			.casetypebox{font-weight:extra-bold;	font-stretch: expanded;font-size:15px;padding-left:15px;float:left;}
			.casetypeshelf{font-weight:extra-bold;	font-stretch: expanded;font-size:15px;padding-left:20px;padding-top:15px;float:left;}
			.arrow-right {width: 0;  height: 0; border-top: 8px solid transparent; border-bottom: 8px solid transparent; border-left: 8px solid green;}
			.arrow-down {width: 0;   height: 0; border-left: 8px solid transparent;border-right: 8px solid transparent;  border-top: 8px solid #f00; }
			
		
		</style>";//style sheet
		
		print $this->render("header.php");
		print $this->render("footer.php");
		echo $out;
		print $this->render("pdfEnd.php");
	

	
	
	?>