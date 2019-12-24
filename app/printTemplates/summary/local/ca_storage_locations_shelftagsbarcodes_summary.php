<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/summary/local/ca_storage_locations_shelftagsbarcodes_summary.php
 * ----------------------------------------------------------------------
 *
 * To run this report without barcodes (which can stall longer reports) call this report with 'showbarcodes' set to 'no':
 *      $this->setVar('showbarcodes','no');
 *      include($this->getVar('base_path')."/ca_storage_locations_shelftagsbarcodes_summary.php");
 *
 * Layout:
 * Report Header
 * Object contained in THIS Storage Location                             ie: object1,object2
 * Storage Locations contained in THIS Storage Location                  ie: Shelf1, Shelf2
 *     Objects contained with Child Storage Location                     ie: object3,object4
 *	  --Storage Locations contained in the Child Storage Location   ie: box1, box2
 * The drilldown is limited to $recursivelimit.
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Shelf Tags Barcodes V1.3.3
 * @type page
 * @pageSize letter 
 * @pageOrientation portrait
 * @tables ca_storage_locations
 *
 * @marginTop 0.5in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
  global $rowcount,$rowheight, $rowsperpage,$recursivelimit,$showbarcodes;
  $version            = "PHS Shelf Tags Barcodes Summary V1.3.3"; 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $showbarcodes       = $this->getVar('showbarcodes'); // Generate barcodes UNLESS showbarcodes = 'no'
  
  //Build pdf filename (requires modified BaseEditorController.php 
	$fname = $t_item->getWithTemplate("^ca_storage_locations.preferred_labels");  
	$sq =  "PHS_Storage_Locations_Shelf_Tag_".str_replace(array(' ','*','\''),'',$fname).".pdf";// Remove illegal characters to add search term to the file name
	$this->setVar('filename',$sq);
   
  $rowcount=0;  //row count on current page.  Currently incrimented BEFORE, so we must start at0.  TODO: Normilize this
  $rowsperpage=10;
  $rowheight = 90;  // height in mm of each row
  $barheight = 70;   // height in mm of the barcode - needed to correctly center in row
 
  $recursivelimit = 4;
  if ($this->getVar('showbarcodes')){$recursivelimit = $this->getVar('showbarcodes');} // how many storage_locations deep to move down the tree	
  $recursive = $recursivelimit;	
  
  if ($showbarcodes =="no"){$version .= " - No Barcodes";}
 // $this->setVar('reportversion',$version);
  $this->setVar('version',$version);
$headerpath = str_replace("zzzzz","&raquo;",$t_item->getWithTemplate("
      <div class='storagepath'>[ ^ca_storage_locations.hierarchy.preferred_labels.name%removeFirstItems=1%delimiter=_zzzzz_  ]</div>
      <ifdef code='ca_storage_locations.description'><div class='storagepathdescription'>^ca_storage_locations.description</div></ifdef>
  "));  
  
  $this->setVar('headerTitle',$t_item->getWithTemplate("$headerpath")."<span class='recursivenote'>Recursive Limit: $recursivelimit</span>");
  $this->setVar('showPagenumber',1);



  /*******************************************\
 *               Functions                     *
  \*******************************************/
  if (!function_exists('getListofObjectsInThisStorageLocation')){
    function getListofObjectsInThisStorageLocation($this_location){
        global $rowcount,$rowheight,$rowsperpage,$showbarcodes;
        $parent_locationid = $this_location->get('ca_storage_locations.location_id');
        $out="";
       
        if($this_contents = $this_location->getLocationContents('ca_movements',array('showChildren' => 0)))   //requires modified /models/ca_storage_locations
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
                         $objectsSorted[$objectindex]['vs_path'] = caGenerateBarcode($path, array( 'checkValues' => $this_contents->opa_check_values,'type' => 'qrcode', 'height' => 2));
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
                 
                 $thisrow = $rowcount * $rowheight;  
                 $thisline = $thisrow - 50;    
                  
                 
                 $out .="<div style='width:100%; height:1px; border-top:1px solid black; position:fixed; left:0; top:$thisrow;'></div>	
                         <div class='box' style='top:$thisrow;'>&nbsp;&nbsp;&nbsp;&nbsp;</div>
                         <div style='top:$thisrow;' class='name'>$objectname</div>
                         <div style='top:$thisrow;' class='idno'>$objectidno</div>
                         <div style='top:$thisrow;' class='casetype'>$type</div>";
                 if($showbarcodes !="no"){
                      $out .= "<div style=' top:$thisrow; ' class='barcode'><img src='$vs_path.png'/><br/>$path</div>";}//$showbarcodes
                 else{
                      $out .= "<div style=' top:$thisrow; ' class='barcode'><br/></div>";
                 }//else	   
                
                 $out .= "<div style='top:$thisrow;' class='obj_image'>".$oneimage."&nbsp;</div>
                          <div style='top:$thisrow;' class='location'>$storage_location </div>
                          <div style='top:$thisrow;' class='locationidno'> $storage_location_idno</div>
                          <div style='top:$thisrow;' class='ldate'>$dateactive</div>
                      ";
   			
                 if($rowcount >= $rowsperpage){ //check to see if we need a new page?
                     $rowcount =0;
                     $out .= "<div class='pageBreak' style='page-break-before: always;'>&nbsp;</div>";
                 }
                
            
              }//foreach $objectsSorted
        }// if $this_contents = $this_location->getLocationContents('ca_movements'))...
        return $out;
    }//end function	
  }//if function exists
/***************************************************/
 
  if (!function_exists('getListofStorageInThisStorageLocation')){
    function getListofStorageInThisStorageLocation($this_location,$recursive){
        global $rowcount,$rowheight, $rowsperpage,$recursivelimit,$showbarcodes;
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
            
            //Icon and Color:
           	$t_type 				=   $t_storage_location->getTypeInstance();

	          $vs_color = null;
	          if ($t_type) { $vs_color = trim($t_type->get('color')); } 
	          if (!$vs_color && $t_type) { $vs_color = trim($t_type->get('color')); }
	          if (!$vs_color) { $vs_color = "FFFFFF"; }
		
	          $vs_icon = null;
	          if ($t_type) { $vs_icon = $t_type->getMediaTag('icon', 'icon'); }
	          $vs_icon = $t_type->getMediaTag('icon', 'icon');
	          if ($vs_icon){
			          $icon = "<div style='width:75px; border:6px solid #{$vs_color};'>{$vs_icon}</div>\n";
	          }			
            
            
            $vs_storage_icon = $icon;//$t_storage_location->get('ca_list_items.related.icon');//', array('convertCodesToDisplayText' => true));
            
            $storagetypehtml = "{$vs_icon}<span class='iconcasetype' style='color:#{$vs_color};'> $vs_storage_type</span>"; //default casetype style
         
	             	
            // Generate Barcode:	editor/storage_locations/StorageLocationEditor/
            if($showbarcodes !="no"){
                $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/";
                $path =$root.$t_storage_location->get('ca_storage_locations.location_id');
                $vs_path = caGenerateBarcode($path, array( 'checkValues' => $t_storage_location->opa_check_values,'type' => 'qrcode', 'height' => 2));
            }//$showbarcodes
            			
            // Hack because $t_storage_location->get('ca_object_representations.related.media.thumbnail', array('filterNonPrimaryRepresentations' => true )) doesn't work.
            $oneimage =$t_storage_location->get('ca_object_representations.media.thumbnail', array('returnAsArray' => true ));
            $oneimage=$oneimage[0];
									
				    $rowcount++;   
				    $thisrow = $rowcount * $rowheight; 				         
            $t_storage_location = new ca_storage_locations($va_storage_children_id);
			      
            $out .="<div style='width:100%; height:1px; border-top:1px solid black; position:fixed; left:0; top:$thisrow;'></div>	
                    <div class='box' style='top:$thisrow;'>&nbsp;&nbsp;&nbsp;&nbsp;</div>
                    <div style='top:$thisrow;' class='name'>$storage_locationname &nbsp;</div>
                    <div style='top:$thisrow;' class='idno'>$storage_locationsidno</div>
                    <div style='top:$thisrow;' class='storageicon'>$storagetypehtml</div>";       
             if($showbarcodes !="no"){
                $out .= "<div style=' top:$thisrow; ' class='barcode'><img src='$vs_path.png'/><br/>$path</div>";//$showbarcodes
             }else{
             	  $out .="<div style=' top:$thisrow; ' class='barcode'></div>";
             }//else
             $outx .=" </td>
                      <td class='col_image'>$oneimage&nbsp;</td>
                      <td class='col_location'>".print_r($this_storage,1)."
                        <div style='font-size:6px;'>".$x_statuss[$x_key]." ($recursive)</div>
                        <div style='font-size:6px;'>".print_r($this_storage,1)." <br/>$dateactive</div>
                      </td>
                </tr></table>";
            $out .= "<div style='top:$thisrow;' class='obj_image'>".$oneimage."&nbsp;</div>
                          <div style='top:$thisrow;' class='location'>".print_r($this_storage,1)." </div>
                          <div style='top:$thisrow;' class='locationidno'> ".$x_statuss[$x_key]." ($recursive)</div>
                          <div style='top:$thisrow;' class='ldate'>".print_r($this_storage,1)." <br/>$dateactive</div>
                    ";
            if($rowcount >= $rowsperpage){
                 $rowcount =0;
                 $out .= "<div class='pageBreak' style='page-break-before: always;'>&nbsp;</div>";
            }
   
            $out .= getListofObjectsInThisStorageLocation($t_storage_location);
            if($recursive > 0){$recursive--; $out .= getListofStorageInThisStorageLocation($t_storage_location,$recursive);}else{$recursive=$recursivelimit;}          
          }//foreach $va_storage_children
        return $out;
    }//function getListofStorageInThisStorageLocation
  }//function_exists getListofStorageInThisStorageLocation
/***************************************************/
/*     Begin Here                                  */
/**************************************************/
 // First get all objects within THIS Storage_Location
 $out .= getListofObjectsInThisStorageLocation($t_item);
 $out .= getListofStorageInThisStorageLocation($t_item,$recursive);
	
 $css= "
   <style type='text/css'> 
     .storagepath            {width:100%; text-align:center; font-size:14px;}
     .storagepathdescription {width:100%;	text-align:center; font-size:12px;}
     .col_image img    { height:$rowheight;}
     .col_location  {width:150px; text-align:right; padding-top:20px; font-size:14px; height:$rowheight;}
     .recursivenote {font-size:8px;}     
     .storageicon   {font-size:25px; font-style:italic; height:25px; width:200px; padding-top:60px;position:fixed; left:35;}        
     .box           {border:1px solid black;position:fixed; left:1;  width:15px; height:15px; margin-top:35px;margin-bottom:30px;}
     .name          {font-size:20px; height:25px; width:200px; margin-top:8px; position:fixed; left:35;} 
     .idno          {font-size:18px; height:50px; width:200px; padding-top:35px;position:fixed; left:35;}
     .casetype      {font-size:15px; font-style:italic; height:73px; width:200px; padding-top:65px;position:fixed; left:35;}
     .iconcasetype  {font-size:45px; font-style:italic; height:73px; width:200px; padding-top:65px;position:fixed; left:35;}
     .barcode       {width:$barheight; height:$rowheight; font-size:8px; word-wrap:break-word; overflow-wrap:break-word; 
                     position:fixed; left:240;rotate:90; padding:10px}
     .obj_image     {position:fixed; left:355; padding:10px; height:$barheight;}
     .location      {width:250px; text-align:right; padding-top:15px; font-size:18px; height:35px;position:fixed; left:440;}
     .locationidno  {width:250px; text-align:right; padding-top:40px; font-size:18px; height:50px;position:fixed; left:440;}
     .ldate         {width:250px;font-size:16px; text-align:right; margin-top:65px;height: 100px; position:fixed; left:440;}     
     .pageBreak	      {clear:both;}	
     #version       {font-size:6px; position:absolute; top:950px; left:0px;}	    
</style>";//style sheet
 
  $mpdf =  new \Mpdf\Mpdf(['orientation' => 'P']);
  $mpdf->SetDisplayMode('fullpage'); 	
  
  $header = "<div id='header'>";
 	$header .= "<img src='".$this->request->getThemeDirectoryPath()."/graphics/logos/".$this->request->config->get('report_img')."' class='headerImg'/>";
	$header .= "<div class='headerTitle'>".$this->getVar('headerTitle').$this->getVar('reportversion')."</div>";
  
  $footer = "<table style='border-collapse: collapse;' width='100%'>
               <tr >
                 <td style='border-top:1px solid black' width='33%'>$version</td>
                 <td style='border-top:1px solid black' width='33%' align='center'>{PAGENO}/{nbpg}</td>
                 <td width='33%' style='border-top:1px solid black; text-align: right;'>{DATE m-j-Y}</td>
               </tr>
             </table>";
 
  $pdfout .= $css;
  $pdfout .= $out; 
 // echo $pdfout;exit;
  $mpdf->SetHTMLHeader($header);
  $mpdf->SetHTMLFooter($footer);
   $mpdf->WriteHTML($pdfout);
  $mpdf->Output($sq,\Mpdf\Output\Destination::DOWNLOAD);
  exit;
 
?>