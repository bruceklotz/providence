	<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/summary/local/ca_object_lots_lot_contents_summary.php
 * ----------------------------------------------------------------------
 *
 * To run this report without barcodes (which can stall longer reports) call this report with 'showbarcodes' set to 'no':
 *      $this->setVar('showbarcodes','no');
 *      include($this->getVar('base_path')."/ca_storage_locations_shelftagsbarcodes_summary.php");
 *
 * Report Header
 * Object contained in THIS Storage Location                             ie: object1,object2
 * Storage Locations contained in THIS Storage Location                  ie: Shelf1, Shelf2
 *     Objects contained with Child Storage Location                     ie: object3,object4
 *	  --Storage Locations contained in the Child Storage Location   ie: box1, box2
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Object Lots Lot Contents Summary V1.0.0
 * @type page
 * @pageSize letter 
 * @pageOrientation portrait
 * @tables ca_object_lots
 *
 * @marginTop 0.5in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
  global $rowcount,$rowsperpage,$recursivelimit,$itemcount,$itemspergroup,$showbarcodes;
  $version            = "PHS Object Lots Lot Contents Summary V1.0.0"; 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $showbarcodes       = $this->getVar('showbarcodes'); // Generate barcodes UNLESS showbarcodes = 'no'
  
  //Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_object_lots.idno_stub");  
	$sq =  "PHS_Object_Lots_Content_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$this->setVar('filename',$sq);
   
  $rowcount=1;
  $rowsperpage=10;
  $rowheight = "55px";
  //$itemcount=1;
  //$itemspergroup=19;
  $recursivelimit=3;	
  $recursive = $recursivelimit;	
  
  if ($showbarcodes =="no"){$version .= " - No Barcodes";}
  $this->setVar('version',$version);
  $this->setVar('headerTitle',$t_item->getWithTemplate("^ca_object_lots.preferred_labels")."<div class='recursivenote'>Recursive Limit: $recursivelimit</div>");


 	
 // Report header/title
 $out = str_replace("zzzzz","&raquo;",$t_item->getWithTemplate("
      <div class='storagepath'><br/><br/>[ ^ca_storage_locations.hierarchy.preferred_labels.name%removeFirstItems=1%delimiter=_zzzzz_  ]</div>
      <ifdef code='ca_object_lots.description'><div class='storagepathdescription'>^ca_object_lots.description<br/></div></ifdef>
  "));

 $out .= "<table class='tag_table'>";
 // First the Lot's Info:
 $rowcount++;
                   							
 // Generate Barcode:
 if ($showbarcodes !="no"){
     $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/object_lots/ObjectLotEditor/Edit/lot_id/";
     $path =$root.$t_item->get('ca_object_lots.lot_id');
     $vs_path = caGenerateBarcode($path, array( 'checkValues' => $t_item->opa_check_values,'type' => 'qrcode', 'height' => 1));
  }//if $showbarcodes
		    							
  //Hack because $the_object->get('ca_object_representations.related.media.tiny', array('filterNonPrimaryRepresentations' => true )) doesn't work:
  $oneimage=$t_item->get('ca_object_representations.media.tiny', array('returnAsArray' => true ));
  $out .="
          <tr class='tagrow'>	
             <td class='col_box'><div class='box'>&nbsp;</div></td>
             <td class='col_case'>".$t_item->get('ca_object_lots.idno_stub')."<br/>
               <span class='casetype'>".$t_item->get('ca_object_lots.type_id',array('convertCodesToDisplayText' => true))."</span>&nbsp;
             </td>";
   if($showbarcodes !="no"){
           $out .= "<td class='col_barcode'><div class='barcode'><img src='$vs_path.png'/><br/>$path</div>";}//$showbarcodes
   else{
            $out .= "<td class='col_barcode'><div class='barcode'><br/></div>";
   }//else	   
   $out .= "   <span class='col_name'>".$t_item->get('ca_object_lots.preferred_labels')." &nbsp;</span></td>
               <td class='col_image'>".$oneimage[0]."&nbsp;</td>
               <td class='col_location'>$storage_location
                   <div style='font-size:6px;'> $storage_location_idno <br/>$dateactive</div>
               </td>
            </tr>";
   			
                            

 
 	
  $lot_id = $t_item->get('ca_object_lot.lot_id');
  // Get all objects within THIS Storage_Location
  $this_objects = $t_item->get('ca_objects.object_id', array('returnAsArray' => true));
  
  $this_idno = $t_item->get('ca_objects.idno', array('returnAsArray' => true));  //used for sorting
  array_multisort($this_idno,$this_objects); //sort object_ids by Object idno
     
  $objectcount = count( $this_objects);
   foreach ($this_objects as $key => $the_object_id) {
        $the_object = new ca_objects($the_object_id);
            
            $objectname = $the_object->get('ca_objects.preferred_labels');
            $objectidno = $the_object->get('ca_objects.idno');
            
            $the_location =$the_object->getLastLocation();  // This loads the class $the_location with the most current ca_storage_locations
            $storage_location_idno = $the_object->get('ca_storage_locations.idno'); 
            $storage_location_id = $the_object->get('ca_storage_locations.location_id');
            $storage_location = $the_object->get('ca_storage_locations.preferred_labels');   
            //$dateactive =$the_location->get('ca_objects_x_storage_locations.effective_date');
          //  if ( $storage_location_id == $parent_locationid ) {         
                $rowcount++;
                //$itemcount++;
                    							
                // Generate Barcode:
                if ($showbarcodes !="no"){
                     $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/objects/ObjectEditor/Edit/object_id/";
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
                 $out .= "<span class='col_name'>$objectname &nbsp;</span></td>
                          <td class='col_image'>".$oneimage[0]."&nbsp;</td>
                          <td class='col_location'>$storage_location
                              <div style='font-size:6px;'> $storage_location_idno <br/>$dateactive</div>
                          </td>
                       </tr>";
   			
                 if($rowcount > $rowsperpage){ //check to see if we need a new page?
                     $rowcount =1;
                     //$itemcount =1;
                     $out .= "</table><div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><br/><table class='tag_table'>";
                 }
                 // if ($itemcount > $itemspergroup){$itemcount=1; $out .= "</table><hr style='height:10px;color:green;'/><table class='tag_table'>";}
          //    }//if  ( $storage_location_id == $parent_locationid ) {
        }//foreach Object within THIS storage Location     
 
 
 
 
 
 
 
 
 
 
 
 
 
 // First get all objects within THIS Storage_Location
// $out .= getListofObjectsInThisLot($t_item);
// $out .= getListofStorageInThisStorageLocation($t_item,$recursive);

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
     .pageBreak	      {clear:both;}	
     #version       {font-size:6px; position:absolute; top:950px; left:0px;}	    
</style>";//style sheet
  
  $pdfout =  $this->render("pdfStart.php");
  $pdfout .= $css;
  $pdfout .= $this->render("header.php");
  $pdfout .= $this->render("footer.php");
  $pdfout .= $out;
  $pdfout .= $this->render("pdfEnd.php");
  // exit;
  echo $pdfout;
// exit;  
 
?>