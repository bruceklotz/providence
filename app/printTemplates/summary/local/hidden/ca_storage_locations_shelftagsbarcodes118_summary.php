	<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/summary/local/ca_storage_locations_shelftagsbarcodes_summary.php
 * ----------------------------------------------------------------------
 *** Current Hack to deal with 32 record limit (before the css crashes) ****
 * /index.php/editor/storage_locations/StorageLocationEditor/PrintSummary/location_id/414?group=1
 * or call subreport:
 *  $this->setVar('group',1);
 *  include($this->getVar('base_path')."/ca_storage_locations_shelftagsbarcodes_summary.php");
 If no group term, then all records
 If group=n, then 32 records starting at n

 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Shelf Tags Barcodes Summary V1.1.8
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
  global $rowcount,$rowsperpage,$recursivelimit,$itemcount,$itemspergroup,$showbarcodes;
  $version            = "PHS Shelf Tags Barcodes Summary V1.1.8"; 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $showbarcodes       = $this->getVar('showbarcodes'); // Generate barcodes UNLESS showbarcodes = 'no'
   
  $rowcount=1;
  $rowsperpage=10;
  $rowheight = "55px";
  $itemcount=1;
  $itemspergroup=19;
  $recursivelimit=3;	
  $recursive = $recursivelimit;	
  
  if ($showbarcodes =="no"){$version .= " - No Barcodes";}
  $this->setVar('version',$version);
  $this->setVar('headerTitle',$t_item->getWithTemplate("^ca_storage_locations.preferred_labels")."<div class='recursivenote'>Recursive Limit: $recursivelimit</div>");
/*
  Report Header
  Object contained in THIS Storage Location                             ie: object1,object2
  Storage Locations contained in THIS Storage Location                  ie: Shelf1, Shelf2
      Objects contained with Child Storage Location                     ie: object3,object4
	  --Storage Locations contained in the Child Storage Location   ie: box1, box2
*/

  /*******************************************\
 *               Functions                     *
  \*******************************************/
  if (!function_exists('getListofObjectsInThisStorageLocation')){
    function getListofObjectsInThisStorageLocation($this_location){
        global $rowcount,$rowsperpage,$itemcount,$itemspergroup,$showbarcodes;
        
        $out="";
        $parent_locationid = $this_location->get('ca_storage_locations.location_id');
			
        // Get all objects within THIS child Storage_Location
        $this_objects = $this_location->get('ca_objects.object_id', array('returnAsArray' => true));
        foreach ($this_objects as $key => $the_object_id) {
            $the_object = new ca_objects($the_object_id);
            $child_storage_ids = $the_object->get('ca_storage_locations.location_id',array('returnAsArray' => true));
            $child_status =	$the_object->get('ca_objects_x_storage_locations.storage_location_status',array('convertCodesToDisplayText' => true,'returnAsArray' => true));
			
            // Step through all of the current Object's storage_locations...looking for location that are NOT Inactive
            foreach ($child_storage_ids as $child_key => $child_id) {
                $xc_storages =$the_object->get('ca_storage_locations',array('convertCodesToDisplayText' => true,'returnAsArray' => true));
                $xc_storageids =$the_object->get('ca_storage_locations.idno',array('convertCodesToDisplayText' => true,'returnAsArray' => true));
                $xc_storage = $xc_storages[$child_key];
                $xc_storageid = $xc_storageids[$child_key];
                
                $dateactives =$the_object->get('ca_objects_x_storage_locations.effective_date',array('returnAsArray' => true)); 
                $dateactive = $dateactives[$child_key];
                
                $pattern = "/" . preg_quote("Inactive", "/") . "/"; 
                if ( ($child_id == $parent_locationid) & ( !preg_match($pattern, $child_status[$child_key]) ) ) {
                    // If we are working with THIS storage_location and NOT an Inactive location...
                    
                 
                    $rowcount++;
                    $itemcount++;
                    							
                    // Generate Barcode:
                    if ($showbarcodes !="no"){
                       //$root = "http://pelhamhs.org/ca/index.php/editor/objects/ObjectEditor/Edit/object_id/"; // this should get pulled out or automated somehow.
                       $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/objects/ObjectEditor/Edit/object_id/";
                       //$root = caEditorUrl($the_object,'ca_objects',$the_object->get('ca_objects.object_id')); 
                       $path =$root.$the_object->get('ca_objects.object_id');
                       $vs_path = caGenerateBarcode($path, array( 'checkValues' => $the_object->opa_check_values,'type' => 'qrcode', 'height' => 1));
		                }//if $showbarcodes
		    							
                    //Hack because $the_object->get('ca_object_representations.related.media.tiny', array('filterNonPrimaryRepresentations' => true )) doesn't work:
                    $oneimage=$the_object->get('ca_object_representations.media.tiny', array('returnAsArray' => true ));
									
                    $out .="
                        <tr class='tagrow'>	
                            <td class='col_box'><div class='box'>&nbsp;</div></td>
                            <td class='col_case'>".$the_object->get('ca_objects.idno')."<br/>
                                <span class='casetype'>".$the_object->get('ca_objects.type_id',array('convertCodesToDisplayText' => true))."</span>&nbsp;
                            </td>";
                    if($showbarcodes !="no"){
                         $out .= "<td class='col_barcode'><div class='barcode'><img src='$vs_path.png'/><br/>$path</div>";}//$showbarcodes
                    else{
                    	   $out .= "<td class='col_barcode'><div class='barcode'><br/></div>";
                    }//else	   
                      $out .= "<span class='col_name'>".$the_object->get('ca_objects.preferred_labels')."&nbsp;</span></td>
                               <td class='col_image'>".$oneimage[0]."&nbsp;</td>
                               <td class='col_location'>$xc_storage 
                                 <div style='font-size:6px;'> $xc_storageid <br/>$dateactive</div>
                               </td>
                               
                         </tr>";
   			
                    if($rowcount > $rowsperpage){
                        $rowcount =1;
                        $itemcount =1;
                        $out .= "</table><div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><br/><table class='tag_table'>";
                    }
                    
                    if ($itemcount > $itemspergroup){$itemcount=1; $out .= "</table><hr style='height:10px;color:green;'/><table class='tag_table'>";}
                 
                }// if $child_id ==...
            }//foreach $child_storage_ids
        }//foreach Object within THIS storage Location
        return $out;
    }//end function	
  }//if function exists
/***************************************************/
  if (!function_exists('getListofStorageInThisStorageLocation')){
    function getListofStorageInThisStorageLocation($this_location,$recursive){
        global $rowcount,$rowsperpage,$recursivelimit,$itemcount,$itemspergroup,$showbarcodes;
        
        $out="";
			
        // Now get all children storage_locations within THIS Storage_Location
        $this_storage =$this_location->get('ca_storage_locations.preferred_labels',array('convertCodesToDisplayText' => true));
        $this_storageid =$this_location->get('ca_storage_locations.idno',array('convertCodesToDisplayText' => true));
        $va_storage_children = $this_location->get('ca_storage_locations.children.location_id', array('returnAsArray' => true));
        
        foreach ($va_storage_children as $va_key => $va_storage_children_id) {
            $t_storage_location = new ca_storage_locations($va_storage_children_id);
			
            //$x_statuss =	$tc_object->get('ca_objects_x_storage_locations.storage_location_status',array('convertCodesToDisplayText' => true,'returnAsArray' => true));
			
            #information about this (child) storage location
            $vs_storage_type = $t_storage_location->get('ca_storage_locations.type_id', array('convertCodesToDisplayText' => true));
            $storagetypehtml="<div class='casetype'>$vs_storage_type</div>"; //default casetype style
            if ($vs_storage_type == "box") {$storagetypehtml="<div class='arrow-right'></div><span class='casetypebox'>$vs_storage_type</span>";}
            if ($vs_storage_type == "shelf") {$storagetypehtml = "<div class='arrow-down'></div><span class='casetypeshelf'>$vs_storage_type</span>";}
            if ($vs_storage_type == "key") {$storagetypehtml = "<div class='circle'></div><span class='casetypeshelf'>$vs_storage_type</span>";}
		
		
            $dateactives = $t_storage_location->get('ca_objects_x_storage_locations.effective_date',array('returnAsArray' => true)); 
            $dateactive = $dateactives[$va_key];//error_log("L174 ".print_r($dateactives,1));
            $rowcount++;
            $itemcount++;
            	
            // Generate Barcode:	editor/storage_locations/StorageLocationEditor/
            if($showbarcodes !="no"){
              //$root = "http://pelhamhs.org/ca/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/"; // this should get pulled out or automated somehow.
              $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/";
              $path =$root.$t_storage_location->get('ca_storage_locations.location_id');
              $vs_path = caGenerateBarcode($path, array( 'checkValues' => $t_storage_location->opa_check_values,'type' => 'qrcode', 'height' => 1));
            }//$showbarcodes
            			
            // Hack because $t_storage_location->get('ca_object_representations.related.media.tiny', array('filterNonPrimaryRepresentations' => true )) doesn't work.
            $oneimage=$t_storage_location->get('ca_object_representations.media.tiny', array('returnAsArray' => true ));
									
            $out .="
                <tr class='tagrow'>
                    <td class='col_box'><div class='box'>&nbsp;</div></td>
                    <td class='col_case'>".$t_storage_location->get('ca_storage_locations.idno')."
                        $storagetypehtml
                    </td>";
             if($showbarcodes !="no"){
                $out .="<td class='col_barcode'><div class='barcode'><img src='$vs_path.png'/><br/>$path</div>";}//$showbarcodes
             else{
             	  $out .="<td class='col_barcode'><div class='barcode'><br/></div>";
             }//else
                $out .=" <span class='col_name'>".$t_storage_location->get('ca_storage_locations.preferred_labels')."</span></td>
                    <td class='col_image'>".$oneimage[0]."&nbsp;</td>
                    <td class='col_location'>".print_r($this_storage,1)."
                        <div style='font-size:6px;'>".$x_statuss[$x_key]." ($recursive)</div>
                        <div style='font-size:6px;'>".print_r($this_storage,1)." <br/>$dateactive</div>
                     </td
                   
                </tr>";
            if($rowcount > $rowsperpage){
                 $rowcount =1;
                 $itemcount =1;
                 $out .= "</table><div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><br/><table class='tag_table'>";
            }
            
            if ($itemcount > $itemspergroup){$itemcount=1;$out .= "</table><hr style='height:10px;color:green;'/><table class='tag_table'>";}
            
            $out .= getListofObjectsInThisStorageLocation($t_storage_location);
            if($recursive > 0){$recursive--; $out .= getListofStorageInThisStorageLocation($t_storage_location,$recursive);}else{$recursive=$recursivelimit;}
           
        }//foreach $va_storage_children
        return $out;
    }//function getListofStorageInThisStorageLocation
  }//function_exists getListofStorageInThisStorageLocation
/***************************************************/
 	
 // Report header/title
 $out = str_replace("zzzzz","&raquo;",$t_item->getWithTemplate("
      <div class='storagepath'><br/><br/>[ ^ca_storage_locations.hierarchy.preferred_labels.name%removeFirstItems=1%delimiter=_zzzzz_  ]</div>
      <ifdef code='ca_storage_locations.description'><div class='storagepathdescription'>^ca_storage_locations.description<br/></div></ifdef>
  "));

 $out .= "<table class='tag_table'>";

 // First get all objects within THIS Storage_Location
 $out .= getListofObjectsInThisStorageLocation($t_item);
 $out .= getListofStorageInThisStorageLocation($t_item,$recursive);

 $out .= " </table>";
	
 $css= "
   <style type='text/css'>  
     .storagepath            {width:100%; text-align:center; font-size:14px;}
     .storagepathdescription {width:100%;	text-align:center; font-size:12px;}
table.tag_table     {border-collapse:collapse;}
     .tagrow tr     {height:$rowheight}
     .tagrow td     {padding:4px; border-top:1px solid black; border-bottom:1px solid black; margin:0px; height:$rowheight}	
     .col_box       {width:30px; height:$rowheight}  
     .col_case      {width:200px; padding-top:25px; font-size:18px; height:$rowheight}
     .col_barcode   {word-wrap: break-word;overflow-wrap: break-word; width:$rowheight; height:$rowheight}
     .col_name      {width:160px; padding:-50px 0 50px 0; margin:0px 0 160px 60px; 
                     display:inline-block; float:left; position:relative; top:0px; right:50px; height:$rowheight}
     .col_image     {width:100px; height:$rowheight}
     .col_location  {width:150px; text-align:right; padding-top:20px; font-size:14px; height:$rowheight}
     .recursivenote {font-size:8px;}
     .casetype      {font:10px; font-style:italic;}
     .casetypebox   {font-weight:extra-bold; font-stretch: expanded; font-size:15px; padding-left:15px;}
     .casetypeshelf {font-weight:extra-bold; font-stretch: expanded; font-size:15px; padding-left:20px; padding-top:15px;}
     .arrow-right   {width:0; height:0; border-top:8px solid transparent; border-bottom:8px solid transparent; border-left:8px solid green;}
     .arrow-down    {width:0; height:0; border-left:8px solid transparent; border-right:8px solid transparent; border-top:8px solid #f00; }
     .circle        {width:10px; height:10px; background:blue; border-radius:5px;}
     .diamond-shield{width:0; height:0; border-top:8px solid transparent; border-bottom:8px solid transparent; border-left:8px solid green;}
     .diamond-shield::after {width:0; height:0; border-left:8px solid transparent; border-right:8px solid transparent; border-top:8px solid #f00; }
     .barcode       {width:$rowheight; height:$rowheight; font-size:6px; word-wrap:break-word; overflow-wrap:break-word; transform:rotate(90deg);
                     position:relative; top:5px; left:50px;}
     .barcode img   {height:$rowheight;}
     .box           {border:1px solid black; width:15px; height:15px; margin:5px;}		
     .newrow	      {clear:both;}	
     #version       {font-size:6px; position:absolute; top:950px; left:0px;}	    
</style>";//style sheet

 //define(DEBUG_LAYOUT,"");
 //define(DEBUG_LAYOUT_LINES,""); 
 
 //$e = new \Exception;var_dump($e->getTraceAsString());exit;
  
  $pdfout =  $this->render("pdfStart.php");
  $pdfout .= $css;
  $pdfout .= $this->render("header.php");
  $pdfout .= $this->render("footer.php");
  $pdfout .= $out;
  $pdfout .= $this->render("pdfEnd.php");
   
  echo $pdfout;
// exit;  
 
?>