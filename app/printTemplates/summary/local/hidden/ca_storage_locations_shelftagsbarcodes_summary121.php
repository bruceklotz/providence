<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/summary/local/ca_storage_locations_shelftagsbarcodes_summary.php
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
 * @name PHS Shelf Tags Barcodes Summary V1.2.1 IP
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
  $version            = "PHS Shelf Tags Barcodes Summary V1.2.1"; 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $showbarcodes       = $this->getVar('showbarcodes'); // Generate barcodes UNLESS showbarcodes = 'no'
  
  //Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_storage_locations.location_id");  
	$sq =  "PHS_Storage_Locations_Shelf_Tag_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$this->setVar('filename',$sq);
   
  $rowcount=1;
  $rowsperpage=10;
  $rowheight = "55px";
  //$itemcount=1;
  //$itemspergroup=19;
  $recursivelimit=4;	
  $recursive = $recursivelimit;	
  
  if ($showbarcodes =="no"){$version .= " - No Barcodes";}
 // $this->setVar('reportversion',$version);
  $this->setVar('version',$version);
  $this->setVar('headerTitle',$t_item->getWithTemplate("^ca_storage_locations.preferred_labels")."<div class='recursivenote'>Recursive Limit: $recursivelimit</div>");
  $this->setVar('showPagenumber',1);

  /*******************************************\
 *               Functions                     *
  \*******************************************/
  if (!function_exists('getListofObjectsInThisStorageLocation')){
    function getListofObjectsInThisStorageLocation($this_location){
        global $rowcount,$rowsperpage,$showbarcodes;//$itemcount,$itemspergroup;
        $parent_locationid = $this_location->get('ca_storage_locations.location_id');
        $out="";
       
        if($this_contents = $this_location->getLocationContents('ca_movements'))
                {
            	//In order to sort, we first load an array with all the data, then sort, then display...
                $objectsSorted =[];
                while( $this_contents->nextHit() != NULL ) {
                     $objectindex =$this_contents->get('ca_objects.idno');
                     $objectsSorted[$objectindex]['objectname'] = $this_contents->get('ca_objects.preferred_labels.name');
                     $objectsSorted[$objectindex]['objectidno'] = $this_contents->get('ca_objects.idno');
                     $objectsSorted[$objectindex]['objectid'] = $this_contents->get('ca_objects.object_id');
                     $objectsSorted[$objectindex]['storage_location_idno'] = $this_location->get('ca_storage_locations.idno'); 
                     $objectsSorted[$objectindex]['storage_location'] = $this_location->get('ca_storage_locations.preferred_labels');
                     $objectsSorted[$objectindex]['type'] = $this_contents->get('ca_objects.type_id',array('convertCodesToDisplayText' => true));
                 
                     $the_object = new ca_objects($objectsSorted[$objectindex]['objectid']);
                     
                     $dateactives = $the_object->get('ca_movements.removal_date',array('returnAsArray' => true, 'sort' => array('ca_movements.removal_date')));
                     $objectsSorted[$objectindex]['dateactive'] = $dateactives[count($dateactives)-1];
                 
                     // Generate Barcode:
                     if ($showbarcodes !="no"){
                         $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/objects/ObjectEditor/Edit/object_id/";
                         $path =$root.$objectsSorted[$objectindex]['objectid'];
                         $objectsSorted[$objectindex]['vs_path'] = caGenerateBarcode($path, array( 'checkValues' => $this_contents->opa_check_values,'type' => 'qrcode', 'height' => 1));
		     }//if $showbarcodes
		 						
                     $objectsSorted[$objectindex]['oneimage'] = $the_object->get('ca_object_representations.media.thumbnail', array('filterNonPrimaryRepresentations' => true ));
              
              }//while ...nextHit()
              
              //Now sort
              ksort( $objectsSorted);
             
              //Now retrieve and display...
              foreach ( $objectsSorted as $objectindex => $item){
                 $rowcount++;            
                 $objectname = $item['objectname'];
                 $objectidno = $item['objectidno'];
                 $objectid = $item['objectid'];
                 $storage_location_idno = $item['storage_location_idno'];
                 $storage_location = $item['storage_location'];
                 $type = $item['type'];
                 $dateactive = $item['dateactive'];
                 $vs_path = $item['vs_path'];
		             $oneimage = $item['oneimage'];
                 
                 $bheight = (($rowcount) * $rowheight)."px;";      
                  
                 $out .="
                    <tr class='tagrow'>	
                       <td class='col_box'><div class='box'>&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
                       <td class='col_case'>$objectidno<br/>
                           <span class='casetype'>$type</span>&nbsp;
                       </td>";
                 if($showbarcodes !="no"){
                      $out .= "<td class='col_barcode'><div class='barcode''><imgstyle='transform:rotate(90);'  src='$vs_path.png'/><br/>$path</div>";}//$showbarcodes
                 else{
                      $out .= "<td class='col_barcode'><div class='barcode'><br/></div>";
                 }//else	   
                
                 $out .= "<span class='col_name'>$objectname &nbsp;</span></td>
                          <td class='col_image'>".$oneimage."&nbsp;</td>
                          <td class='col_location'>$storage_location
                              <div style='font-size:6px;'> $storage_location_idno <br/><br/>$dateactive</div>
                          </td>
                       </tr>";
   			
                 if($rowcount > $rowsperpage){ //check to see if we need a new page?
                     $rowcount =1;
                     //$itemcount =1;
                     $out .= "</table><div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><br/><table class='tag_table'>";
                 }
                 // if ($itemcount > $itemspergroup){$itemcount=1; $out .= "</table><hr style='height:10px;color:green;'/><table class='tag_table'>";}
            
              }//foreach $objectsSorted
        }// if $this_contents = $this_location->getLocationContents('ca_movements'))...
        return $out;
    }//end function	
  }//if function exists
/***************************************************/
 
  if (!function_exists('getListofStorageInThisStorageLocation')){
    function getListofStorageInThisStorageLocation($this_location,$recursive){
        global $rowcount,$rowsperpage,$recursivelimit,$showbarcodes;//$itemcount,$itemspergroup;
        $out="";
        // Now get all children storage_locations within THIS Storage_Location
        $this_storage =$this_location->get('ca_storage_locations.preferred_labels',array('convertCodesToDisplayText' => true));
        $this_storageid =$this_location->get('ca_storage_locations.idno',array('convertCodesToDisplayText' => true));
        $va_storage_children = $this_location->get('ca_storage_locations.children.location_id', array('returnAsArray' => true));
      
        foreach ($va_storage_children as $va_key => $va_storage_children_id) {
            $t_storage_location = new ca_storage_locations($va_storage_children_id);
	    
            #information about this (child) storage location
            $storage_locationsidno = $t_storage_location->get('ca_storage_locations.idno');
            $storage_locationname =$t_storage_location->get('ca_storage_locations.preferred_labels');
            $vs_storage_type = $t_storage_location->get('ca_storage_locations.type_id', array('convertCodesToDisplayText' => true));
            
            $storagetypehtml="<div class='casetype'>$vs_storage_type</div>"; //default casetype style
            if ($vs_storage_type == "box") {$storagetypehtml="<div class='arrow-right'></div><span class='casetypebox'>$vs_storage_type</span>";}
            if ($vs_storage_type == "shelf") {$storagetypehtml = "<div class='arrow-down'></div><span class='casetypeshelf'>$vs_storage_type</span>";}
            if ($vs_storage_type == "key") {$storagetypehtml = "<div class='circle'></div><span class='casetypeshelf'>$vs_storage_type</span>";}
	    
	          //$dateactives = $t_storage_location->get('ca_objects_x_storage_locations.effective_date',array('returnAsArray' => true)); 
            //$dateactive = $dateactives[$va_key];//error_log("L174 ".print_r($dateactives,1));
                	
            // Generate Barcode:	editor/storage_locations/StorageLocationEditor/
            if($showbarcodes !="no"){
                $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/";
                $path =$root.$t_storage_location->get('ca_storage_locations.location_id');
                $vs_path = caGenerateBarcode($path, array( 'checkValues' => $t_storage_location->opa_check_values,'type' => 'qrcode', 'height' => 1));
            }//$showbarcodes
            			
            // Hack because $t_storage_location->get('ca_object_representations.related.media.thumbnail', array('filterNonPrimaryRepresentations' => true )) doesn't work.
            $oneimage =$t_storage_location->get('ca_object_representations.media.thumbnail', array('returnAsArray' => true ));
            $oneimage=$oneimage[0];
									
				    $rowcount++;   
				    $bheight = (($rowcount) * $rowheight)."px;";      
            $t_storage_location = new ca_storage_locations($va_storage_children_id);
			      $out .="
                <tr class='tagrow'>
                    <td class='col_box'><div class='box'>&nbsp;</div></td>
                    <td class='col_case'><span class='col_name'>$storage_locationname</span><br/>$storage_locationsidno
                        $storagetypehtml
                    </td>";
             if($showbarcodes !="no"){
                $out .="<td class='col_barcode'><div class='barcode' ><img style='transform:rotate(90);' src='$vs_path.png'/><br/>$path</div></iframe>";}//$showbarcodes
             else{
             	  $out .="<td class='col_barcode'><div class='barcode'><br/></div>";
             }//else
             $out .=" </td>
                      <td class='col_image'>$oneimage&nbsp;</td>
                      <td class='col_location'>".print_r($this_storage,1)."
                        <div style='font-size:6px;'>".$x_statuss[$x_key]." ($recursive)</div>
                        <div style='font-size:6px;'>".print_r($this_storage,1)." <br/>$dateactive</div>
                      </td>
                </tr>";
            if($rowcount > $rowsperpage){
                 $rowcount =1;
                // $itemcount =1;
                 $out .= "</table><div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><br/><table class='tag_table'>";
            }
            
            //if ($itemcount > $itemspergroup){$itemcount=1;$out .= "</table><hr style='height:10px;color:green;'/><table class='tag_table'>";}
            
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
 $rowcount++; //room for header
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
     .tagrow tr     {height:$rowheight;}
     .tagrow td     {padding:4px; border-top:1px solid black; border-bottom:1px solid black; margin:0px; height:$rowheight}	
     .col_box       {width:30px; height:$rowheight}  
     .col_case      {width:250px; padding-top:25px; font-size:18px; height:$rowheight}
     .col_barcode   {word-wrap: break-word;overflow-wrap: break-word; width:150px; height:$rowheight; }
     .col_name      {width:160px; padding:-50px 0 50px 0; margin:0px 0 160px 60px; 
                     display:inline-block; float:left; position:relative; top:0px; right:50px; height:$rowheight}
     /*.col_image     {width:100px; height:$rowheight}*/
     .col_image img    { height:$rowheight}
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
     .barcode       {width:$rowheight; height:$rowheight; font-size:6px; word-wrap:break-word; overflow-wrap:break-word; 
                     position:relative; top:5px; left:50px;border:1px solid red;}
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
  
 // set_include_path("/mpdf/mpdf/src");
 // include("../mpdf6/mpdf.php");
  //require_once __DIR__ . '/vendor/autoload.php';
 // require_once('/hermes/bosnaweb28a/b1269/ipw.pelhamhs/public_html/ca/vendor/autoload.php');
  $mpdf =  new \Mpdf\Mpdf(['orientation' => 'L']);
 // $mpdf=new mPDF('c'); 
  $mpdf->SetDisplayMode('fullpage');
  $mpdf->WriteHTML($pdfout);
  $mpdf->Output();
  exit;
  
  
  
  
  // exit;
  echo $pdfout;
 exit;  
 
?>