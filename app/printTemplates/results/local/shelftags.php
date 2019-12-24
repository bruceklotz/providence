<?php
/* ----------------------------------------------------------------------
 * app/templates/summary/shelftags.php
 * ----------------------------------------------------------------------
 
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Results Shelf Tags V1.0.1.04
 * @type page
 * @pageSize letter
 * @pageOrientation landscape
 * @tables ca_objects, ca_entities, ca_collections, ca_object_lots
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
	$version = "PHS Results Shelf Tags V1.0.1.03"; 
 	$t_display				= $this->getVar('t_display');
	$va_display_list 		= $this->getVar('display_list');
	$vo_result 				= $this->getVar('result');
	$vn_items_per_page 		= $this->getVar('current_items_per_page');
	$vs_current_sort 		= $this->getVar('current_sort');
	$vs_default_action		= $this->getVar('default_action');
	$vo_ar					= $this->getVar('access_restrictions');
	$vo_result_context 		= $this->getVar('result_context');
	$vn_num_items			= (int)$vo_result->numHits();
	$vs_color 				= ($this->request->config->get('report_text_color')) ? $this->request->config->get('report_text_color') : "#FFFFFF";
	$vn_start 				= 0;
	print $this->render("pdfStart.php");
	print $this->render("header.php");
	print $this->render("footer.php");
  echo"<div id='body'>";
	$vn_lines_on_page = 0;
	$vn_items_in_line = 0;
	$vn_left = $vn_top = 0;
	$vn_page_count = 0;
	
	$va_storage_children = $vo_result->get('ca_storage_locations.children.location_id', array('returnWithStructure' => true));
	foreach ($va_storage_children as $va_key => $va_storage_children_id) { // step through each child
			
			$t_storage_location = new ca_storage_locations($va_storage_children_id); // load new instance of storage_location for the child
			
			$thumbnail=$t_storage_location->get('ca_object_representations.media.small', array('returnWithStructure' => true));
			$taginfo['thumbnail']="( ".$thumbnail[$va_key]." )";
			$taginfo['type']= $t_storage_location->get('ca_storage_locations.type_id', array('convertCodesToDisplayText' =>'true'));
			$taginfo['id']=$t_storage_location->get('ca_storage_locations.idno');//;echo "::".$taginfo['id'].";;";
			$taginfo['name']=$t_storage_location->get('ca_storage_locations.preferred_labels.name');
			$taginfo['barcode'] = "http://pelhamhs.org/ca/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/".$taginfo['id'];
			$taginfo['location']= $t_storage_location->get('ca_storage_locations.parent.idno');
			$taginfo = 	displaytag($this,$taginfo);
			echo $taginfo['out'];
		
			//$va_objects = $t_storage_location->get('ca_storage_locations.related.object_idno', array('returnWithStructure' => true,'convertCodesToDisplayText' => true));
			$va_objects = $t_storage_location->get('ca_objects.idno', array('returnWithStructure' => true));
			//var_dump($va_objects);
			// Now step through each object that is in the parent
			foreach ($va_objects as $key=> $y_object) {
					$x_objects = new ca_storage_locations($y_object); // load new instance of storage_location for the child
			
					$loc=$x_objects->get('ca_storage_locations.idno', array('returnWithStructure' => true));
					$location= $x_objects->get('ca_storage_locations.idno');
					$type= $x_objects->get('ca_objects.type_id', array('convertCodesToDisplayText' => true));
					 
					$thumbnail="[".$x_objects->get('ca_object_representations.media.small')."]";//print_r($thumbnail);
					$name=$x_objects->get('ca_objects.preferred_labels.name');
					$barcode = "http://pelhamhs.org/ca/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/".$taginfo['id'];
					$taginfo['type']= $type;//[$key];
					$taginfo['location']=$location[$key];
					$taginfo['thumbnail']=$thumbnail;//[$key];
					$taginfo['id']=$id;//[$key];
					$taginfo['name']=$name;//[$key];
					$taginfo['barcode'] = "http://pelhamhs.org/ca/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/".$taginfo['id'];
					$taginfo = 	displaytag($this,$taginfo);
					echo $taginfo['out'];
	//	}//while
					//	}//foreach $va_objects
			}
	
/*	
		
	//	var_dump($t_objects);
		#information about this (child) storage location
		$vs_storage_type = $x_objects->get('ca_storage_locations.type_id', array('convertCodesToDisplayText' => true));
		
		$vntop=$vn_top+3;
					
		echo "	<div class='shelftag' style='left:".$vn_left."mm; top:".$vntop."mm;'>
				<div class='tagmedia'>".
					$t_storage_location->get('ca_object_representations.media.thumbnail')."<br/>
				</div>
				<div class='tagbarcode'>";
					$ps_identifier=$t_storage_location->get('ca_storage_locations.idno');
					$ps_id=$t_storage_location->get('ca_storage_locations.location_id');
					//this is a hack
					$pv_identifier="http://pelhamhs.org/ca/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/$ps_id";
				
					$vs_path = caGenerateBarcode($pv_identifier, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 150)); 
					print "<img src='".$vs_path.".png'/>"; //<br/><a href='$pv_identifier'>$ps_identifier</a><hr/>"; 
			echo"	</div>
				<div class='tagcase'>".$t_storage_location->get('ca_storage_locations.idno')."</div>
				<div class='taglabel'>".$t_storage_location->get('ca_storage_locations.preferred_labels')."</div>
				<div class='tagcasetype'>$vs_storage_type</div>
			</div>";					
		 					
		$vn_items_in_line++;
		$vn_left += 52;
		if ($vn_items_in_line >= 5) {
				$vn_items_in_line = 0;
				$vn_left = 0;
				$vn_top += 58;
				$vn_lines_on_page++;
				print "<br class=\"clear\"/>\n";
		}
		if ($vn_lines_on_page >= 1) { 
				$vn_page_count++;
				$vn_lines_on_page = 0;
				$vn_left = 0; 
				$vn_top = ($this->getVar('PDFRenderer') === 'domPDF') ? 0 : ($vn_page_count * 183);
				print "<div class=\"pageBreak\" style=\"page-break-before: always;\">&nbsp;</div>\n";
		}
		
*/		
		
	}//foreach
/*----------------------*/
// $taginfo
function displaytag($me,$taginfo){
		$thumbnail =$taginfo['thumbnail'];
		$barcode =$taginfo['barcode'];
		$vn_items_in_line =$taginfo['vn_items_in_line'];
		$vn_top=$taginfo['vntop'];
		$vn_top=$taginfo['vn_top'];
		$vn_left=$taginfo['vn_left'];
		$vn_page_count =$taginfo['vn_page_count'];
		$vn_lines_on_page =$taginfo['vn_lines_on_page'];
		$vntop=$vn_top+3;
				
		$out = "	<div class='shelftag' style='left:".$vn_left."mm; top:".$vntop."mm;'>
									<div class='tagmedia'>$thumbnail</div>
									<div class='taglocation'>".$taginfo['location']."</div>
									<div class='tagbarcode'>";
		$vs_path = caGenerateBarcode($barcode, array('checkValues' => $me->opa_check_values, 'type' => 'qrcode', 'height' => 150)); 
		$out .= 			"<img src='".$vs_path.".png'/>";
		$out .= 			"</div>
									<div class='tagcase'>".$taginfo['id']."</div>
									<div class='tagcasetype'>".$taginfo['type']."</div>
									<div class='taglabel'>".$taginfo['name']."</div>
							</div>";					
		 					
		$vn_items_in_line++;
		$vn_left += 52;
		if ($vn_items_in_line >= 5) {
				$vn_items_in_line = 0;
				$vn_left = 0;
				$vn_top += 58;
				$vn_lines_on_page++;
				$out .= "<br class=\"clear\"/>\n";
		}
		if ($vn_lines_on_page >= 1) { 
				$vn_page_count++;
				$vn_lines_on_page = 0;
				$vn_left = 0; 
				$vn_top = ($me->getVar('PDFRenderer') === 'domPDF') ? 0 : ($vn_page_count * 183);
				$out .= "<div class=\"pageBreak\" style=\"page-break-before: always;\">&nbsp;</div>\n";
		}
		$taginfo['vn_top']=$vn_top;
		$taginfo['vntop']=$vntop;
		$taginfo['vn_left']=$vn_left;
		$taginfo['vn_items_in_line']=$vn_items_in_line;
		$taginfo['vn_page_count']=$vn_page_count;
		$taginfo['vn_lines_on_page']=$vn_lines_on_page;
		$taginfo['out']=$out;
		return $taginfo;
}// function
//-------------------------	
/*	
		//$va_objects = $vo_result->get('ca_objects.idno', array('returnWithStructure' => true));
	//	$va_objects = $vo_result->get('ca_objects.idno', array('returnAsArray' => true));
		$va_objects = $vo_result->get('ca_storage_locations.ca_objects', array('returnAsArray' => true,'convertCodesToDisplayText' => true));
		var_dump($va_objects);
		foreach ($va_objects as $va_key => $va_objects_id) {//var_dump($va_key);
			#load new instance 
			$t_objects = new ca_objects($va_key); echo" ~ $va_key => $va_objects_id |";
		
			#information about this (child) storage location
			$vs_object_type = $t_objects->get('ca_objects.type_id', array('convertCodesToDisplayText' => true));
		echo"$vs_object_type";
			$vntop=$vn_top+3;
					
		echo "	<div class='shelftag' style='left:".$vn_left."mm; top:".$vntop."mm;'>
				<div class='tagmedia'>".
					$t_objects->get('ca_object_representations.media.thumbnail')."<br/>
				</div>
				<div class='tagbarcode'>";
					$ps_identifier=$t_objects->get('ca_objects.idno');
					$ps_id=$t_objects->get('ca_objects.object_id');
					//this is a hack
					$pv_identifier="http://pelhamhs.org/ca/index.php/editor/objects/ObjectEditor/Edit/object_id/$ps_id";
				
					$vs_path = caGenerateBarcode($pv_identifier, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 150)); 
					print "<img src='".$vs_path.".png'/><br/><a href='$pv_identifier'>$ps_identifier</a><hr/>"; 
			echo"	</div>
				<div class='tagcase'>".$t_objects->get('ca_objects.idno')."<br/>$ps_id object: $vn_items_in_line</div>
				<div class='taglabel'>".$t_objects->get('ca_objects.preferred_labels')."</div>
				<div class='tagcasetype'>$vs_objects_type</div>
			</div>";			
		
		
		$vn_items_in_line++;
		$vn_left += 58;
			
		//	}//if $vo_result->getWithTemplate('^ca_objects.preferred_labels.name').
		if ($vn_items_in_line >= 5) {
				$vn_items_in_line = 0;
				$vn_left = 0;
				$vn_top += 58;
				$vn_lines_on_page++;
				print "<br class=\"clear\"/>\n";
		}
		if ($vn_lines_on_page >= 1) { 
				$vn_page_count++;
				$vn_lines_on_page = 0;
				$vn_left = 0; 
				$vn_top = ($this->getVar('PDFRenderer') === 'domPDF') ? 0 : ($vn_page_count * 183);
				print "<div class=\"pageBreak\" style=\"page-break-before: always;\">&nbsp;</div>\n";
		}
	}//while
		
*/
			
//----------------------

?>
		</div>
<?php
	print $this->render("pdfEnd.php");
?>
php");
?>
