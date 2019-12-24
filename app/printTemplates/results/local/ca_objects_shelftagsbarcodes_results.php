<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ca_objects_shelftagsbarcodes_results.php
 * ----------------------------------------------------------------------
 *** Current Hack to deal with 32 record limit (before the css crashes) ****
 * /index.php/editor/storage_locations/StorageLocationEditor/PrintSummary/location_id/414?group=1
 * or call subreport:
 *  $this->setVar('group',1);
 *  include($this->getVar('base_path')."/ca_objects_shelftagsbarcodes_results.php");
 If no group term, then all records
 If group=n, then 32 records starting at n

 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Object Shelf Tags Barcodes Results V1.0.8
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
  global $rowcount,$rowsperpage,$recursivelimit,$itemcount,$itemcountbottom,$itemcounttop,$showbarcodes;
  $version            = "PHS Object Shelf Tags Barcodes Results V1.0.8"; 
 
  $vo_result 				= $this->getVar('result');
  $vn_items_per_page 		= $this->getVar('current_items_per_page');
  $vs_current_sort 		= $this->getVar('current_sort');
 
 
 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $showbarcodes       = $this->getVar('showbarcodes'); // Generate barcodes UNLESS showbarcodes = 'no'
   
//  $rowcount=1;
  $rowsperpage=10;
  $recursivelimit=3;	
  $recursive = $recursivelimit;	
  
  //The following is limits output to group (of $itemcountsize records)unless group is null, than return All records
  $itemcount=0;
  $itemcountsize=32;
  $itemcountgroup=$_GET["group"];
  $group=$this->getVar('group');
  
  if ( $group  ){
      $itemcountgroup= $group; //$group passed from other pages ie: ca_storage_locations_shelftagsbarcodes2_summary.php
      $version .= " Group $group";
      }
  if (!$itemcountgroup) {
      $itemcountbottom = 0;
      $itemcounttop=1000; //way above any resonable limit
  }
  elseif($itemcountgroup==0 or $itemcountgroup == 1){//first page is group=0 or group=1
      $itemcountbottom = 1;
      $itemcounttop = $itemcountsize;
  }else{
      $itemcountbottom = ($itemcountsize *( $itemcountgroup - 1 )) + 1;
      $itemcounttop = $itemcountsize * $itemcountgroup;
  }
  if ($showbarcodes =="no"){$version .= " - No Barcodes";}
  $this->setVar('version',$version);
  $this->setVar('headerTitle',$t_item->getWithTemplate("^ca_storage_locations.preferred_labels")."<div class='recursivenote'>Recursive Limit: $recursivelimit, Page Group:$itemcountgroup</div>");
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
        global $rowcount,$rowsperpage,$itemcount,$itemcountbottom,$itemcounttop,$showbarcodes;
        
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
                    
                    
                    $itemcount++;
                  if( ($itemcount >= $itemcountbottom) and ($itemcount <= $itemcounttop) ){
                    $rowcount++;
                    							
                    // Generate Barcode:
                    if ($showbarcodes !="no"){
                       $root = "http://pelhamhs.org/ca/index.php/editor/objects/ObjectEditor/Edit/object_id/"; // this should get pulled out or automated somehow.
                       $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/objects/ObjectEditor/Edit/object_id/";
                       //$root = caEditorUrl($the_object,'ca_objects',$the_object->get('ca_objects.object_id')); 
                       $path =$root.$the_object->get('ca_objects.object_id');
                       $vs_path = caGenerateBarcode($path, array( 'checkValues' => $the_object->opa_check_values,'type' => 'qrcode', 'height' => 1));
		                }//if $showbarcodes
		    							
                    //Hack because $the_object->get('ca_object_representations.related.media.tiny', array('filterNonPrimaryRepresentations' => true )) doesn't work:
                    $oneimage=$the_object->get('ca_object_representations.media.tiny', array('returnAsArray' => true ));
									
                    $out .="
                        <div class='tagrow'>	
                            <div class='col_box'><div class='box'>&nbsp;</div></div>
                            <div class='col_case'>".$the_object->get('ca_objects.idno')."<br/>
                                <span class='casetype'>".$the_object->get('ca_objects.type_id',array('convertCodesToDisplayText' => true))."</span>&nbsp;
                             </div>";
                    if($showbarcodes !="no"){
                      $out .= "<div class='col_barcode'><div class='itembarcode'><img src='$vs_path.png'/><br/>$path</div></div>";}//$showbarcodes
                      $out .= "<div class='col_name'>".$the_object->get('ca_objects.preferred_labels')."&nbsp;</div>
                               <div class='col_image'>".$oneimage[0]."</div>
                               <div class='col_location'>$xc_storage 
                                 <div style='font-size:6px;'> $xc_storageid <br/>$dateactive</div>
                               </div>
                         </div>";
   			
                    if($rowcount > $rowsperpage){
                        $rowcount =1;
                        $out .= "</div><div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><br/><div class='storageshelftag'>";
                    }
                  }//if($itemcount > $itemcountlimit){
                }// if $child_id ==...
            }//foreach $child_storage_ids
        }//foreach Object within THIS storage Location
        return $out;
    }//end function	
  }//if function exists
/***************************************************/


 	
 
 
 
 // Report header/title
 $out = str_replace("zzzzz","&raquo;",$t_item->getWithTemplate("
      <div class='storagepath'><br/><br/>[Object List  ^ca_storage_locations.hierarchy.preferred_labels.name%removeFirstItems=1%delimiter=_zzzzz_  ]</div>
      <ifdef code='ca_storage_locations.description'><div class='storagepathdescription'>^ca_storage_locations.description<br/></div></ifdef>
  "));

 $pagebreak = "</div><div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><div class='storageshelftag'>";
 $groupbreak = "</div><div class='storageshelftag2'>";
 
 
 
 
 $out .= "<br/><div class='storageshelftag'>";

 
 $vo_result->seek(0);
 $vn_line_count = 0;
 
 $rowcount = 0; // Current row on the page
 $rowsperpage=9;// Base 0
 $totalrowcount = 0; // Running total of all rows
 
 $groupcount = 0;
 $rowspergroup = 30;
 
 while($vo_result->nextHit() and $totalrowcount < 100) {
     $rowcount++;
     $groupcount++;
     $totalrowcount++;
                 
     // Generate Barcode:
     if ($showbarcodes !="no"){
          $root = "http://pelhamhs.org/ca/index.php/editor/objects/ObjectEditor/Edit/object_id/"; // this should get pulled out or automated somehow.
          $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/objects/ObjectEditor/Edit/object_id/";
          //$root = caEditorUrl($the_object,'ca_objects',$the_object->get('ca_objects.object_id')); 
          $path =$root.$vo_result->get('ca_objects.object_id');
          $vs_path = caGenerateBarcode($path, array( 'checkValues' => $the_object->opa_check_values,'type' => 'qrcode', 'height' => 1));
	   }//if $showbarcodes
	 
     //Hack because $the_object->get('ca_object_representations.related.media.tiny', array('filterNonPrimaryRepresentations' => true )) doesn't work:
     $oneimage=$vo_result->get('ca_object_representations.media.tiny', array('returnAsArray' => true ));
									
     $out .="
              <div class='tagrow'>	
                   <div class='col_box'><div class='box'>&nbsp;</div></div>
                   <div class='col_case'>".$vo_result->get('ca_objects.idno')."<br/>
                                          <span class='casetype'>".$vo_result->get('ca_objects.type_id',array('convertCodesToDisplayText' => true))."</span>&nbsp;
                   </div>";
     if($showbarcodes !="no"){
          $out .= "<div class='col_barcode'><div class='itembarcode'><img src='$vs_path.png'/><br/>$path</div></div>";}//$showbarcodes
     $out .=      "<div class='col_name'>".$vo_result->get('ca_objects.preferred_labels')."&nbsp;</div>
                   <div class='col_image'>".$oneimage[0]."</div>
                   <div class='col_location'>$xc_storage 
                       <div style='font-size:6px;'> $xc_storageid <br/>$dateactive</div>
                   </div>
              </div>";
   			
           if($rowcount > $rowsperpage){$rowcount =0; $out .= $pagebreak; }
           if($groupcount > $rowspergroup){$groupcount =0; $out .= $groupbreak; }
 
 
 }//while...
 if ($rowcount != 0 ){$out .= "</div>";}
// $out .= " </div>";
	
 $css= "
   <style type='text/css'>  
     .storagepath {text-align:center;font-size:14px;}
     .storagepathdescription {	text-align:center;font-size:12px;}
     .storageshelftag {text-align:left;margin-left:auto;margin-right:auto;width:100%;border-left:1px solid black;border-right:1px solid black;}
     .storageshelftag .tagrow:first-child {border-top:1px solid black;} 
     .storageshelftag2 {text-align:left;margin-left:auto;margin-right:auto;width:100%;border-left:1px solid black;border-right:1px solid black;}
     .storageshelftag2 .tagrow:first-child {border-top:1px solid black;} 


     .tagrow {height:75px; padding:4px; border-top:0px solid black; border-bottom:1px solid black;margin:0px;float:none; }	
	    
     .col_box {height:50px;width:30px;float:left;  transform: translate(0, 25%) ;}  
     .col_case {height:50px;width:200px;float:left; transform: translate(0, 20%);font-size:18px;}
     .col_barcode {height:75px; width:70px;float:left;}
     .col_name 	{width:160px;float:left; height:50px; transform: translate(0, 20%)}
     .col_image  {width:100px;float:left;/*max-height:65px;*/ height:65px; position:relative; /*95%; margins:5%;*/}
     .col_image img {  max-height: 100%;  max-width: 100%; width: auto; height: auto;  position: absolute;  top: 0;  bottom: 0;  left: 0;   right: 0;margin: auto;}
     .col_location {width:200px;float:right; height:50px; transform: translate(0, 20%);font-size:14px;}
     .recursivenote {font-size:8px;}
     .casetype {font:10px;font-style:italic;}
     .casetypebox{font-weight:extra-bold; font-stretch: expanded;font-size:15px;padding-left:15px;}
     .casetypeshelf{font-weight:extra-bold; font-stretch: expanded;font-size:15px;padding-left:20px;padding-top:15px;}
     .arrow-right {width: 0;  height: 0; border-top: 8px solid transparent; border-bottom: 8px solid transparent; border-left: 8px solid green;}
     .arrow-down {width: 0;   height: 0; border-left: 8px solid transparent;border-right: 8px solid transparent;  border-top: 8px solid #f00; }
     .circle {width: 10px; height: 10px; background: blue;  border-radius: 5px;}
     .diamond-shield {width: 0;  height: 0; border-top: 8px solid transparent; border-bottom: 8px solid transparent; border-left: 8px solid green;}
     .diamond-shield::after {width: 0;   height: 0; border-left: 8px solid transparent;border-right: 8px solid transparent;  border-top: 8px solid #f00; }

     .itembarcode { width:75px; height:75px;font-size:6px;word-wrap: break-word;overflow-wrap: break-word;transform: rotate(90deg);
                    position: relative; top: 5px; left: 50px;}
     .itembarcode img { height:75px;}
     .locationbarcode { width:75px; height:75px;font-size:6px;word-wrap: break-word;overflow-wrap: break-word;transform: rotate(90deg);
                    position: relative; top: 5px; left: 50px;}
     .locationbarcode img { height:75px;}
     .checkbox {border:1px solid black;width:15px;height:15px;margin:5px;}
     .box {border:1px solid black;width:15px;height:15px;margin:5px;}		
     .newrow	{clear:both;}	
 #version       {font-size:6px;position: absolute;
                 top: 950px;left:0px;}	    
		
  </style>";//style sheet
//foreach ($page ){
 
  $pdfout =  $this->render("pdfStart.php");
  $pdfout .= $css;
  $pdfout .= $this->render("header.php");
  $pdfout .= $this->render("footer.php");
  $pdfout .= $out;//."<hr/>moo<hr/>$out";
  $pdfout .= $this->render("pdfEnd.php");
   
  echo $pdfout;
   echo $pdfout;
   
 
?>