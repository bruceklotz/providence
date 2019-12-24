<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/summary/local/ca_storage_locations_bykeys_summary.php
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
 * @name PHS Storage location by keys V1.0.5
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
  global $rowcount,$rowsperpage,$recursivelimit,$itemcount,$itemcountbottom,$itemcounttop,$showbarcodes,$nodescription,$noobjectbarcode;
  $version            = "PHS Storage location by keys Summary V1.0.4"; 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $showbarcodes       = $this->getVar('showbarcodes'); // Generate barcodes UNLESS showbarcodes = 'no'
   
  $rowcount=1;
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
  $this->setVar('headerTitle',$t_item->getWithTemplate("^ca_storage_locations.preferred_labels"));





  /*******************************************\
 *               Functions                     *
  \*******************************************/
  if (!function_exists('getListofObjectsInThisStorageLocation')){
    function getListofObjectsInThisStorageLocation($this_location){
        global $rowcount,$rowsperpage,$itemcount,$itemcountbottom,$itemcounttop,$nodescription,$noobjectbarcode;
        
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
                $xc_storageids =$the_object->get('ca_storage_locations.idno',array('convertCodesToDisplayText' => true,'returnAsArray' => true));
                $xc_storage = $xc_storages[$child_key];
                $xc_storageid = $xc_storageids[$child_key];
                
                $pattern = "/" . preg_quote("Inactive", "/") . "/"; 
                if ( ($child_id == $parent_locationid) & ( !preg_match($pattern, $child_status[$child_key]) ) ) {
                    // If we are working with THIS storage_location and NOT an Inactive location...
                    
                    $itemcount++;
                  if( ($itemcount >= $itemcountbottom) and ($itemcount <= $itemcounttop) ){
                    $rowcount++;
                    							
		    							
                    //Hack because $the_object->get('ca_object_representations.related.media.tiny', array('filterNonPrimaryRepresentations' => true )) doesn't work:
                    $oneimage=$the_object->get('ca_object_representations.media.thumbnail', array('returnAsArray' => true ));
                    if ($noobjectbarcode != true){	$out .="<div class='objectl'>";}else{						
                       $out .="
                        <div class='object'>	
                               ";
                    }
                    if ($noobjectbarcode != true){
                    	// Generate Barcode:
                       $root = "http://pelhamhs.org/pa2/index.php/Detail/objects/";
                       //$root = caEditorUrl($the_object,'ca_objects',$the_object->get('ca_objects.object_id')); 
                       $path =$root.$the_object->get('ca_objects.object_id');
                       $vs_path = caGenerateBarcode($path, array( 'checkValues' => $the_object->opa_check_values,'type' => 'qrcode', 'height' => 2));
                      $out .= "<div class='barcode'><img src='$vs_path.png'/><br/>$path</div>";
                    }//$noobjectbarcode
                    
                    $out .="    <div class='img'>".$oneimage[0]."</div>
                                <div class='idno'>[".$the_object->get('ca_objects.idno')."]</div>
                                <div class='itemname'> ".$the_object->get('ca_objects.preferred_labels')."</div>";
                    if ($nodescription != true) {
                      if (strlen($the_object->get('ca_objects.display_text')) > 0){
                        $out .= "<div class='description'>".$the_object->get('ca_objects.display_text')."</div>";}
                      else{
                        $out .="<div class='description'>".$the_object->get('ca_objects.description')."</div>";
                    }}
                    
                    
                    $out .="</div>";
   			
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
 	
 // -- Begin Header ---
 $out = str_replace("zzzzz",">>>",$t_item->getWithTemplate("
      <div class='storagepath'><br/><br/>[ ^ca_storage_locations.hierarchy.preferred_labels.name%removeFirstItems=1%delimiter=_zzzzz_  ]<br/><br/></div>"));
  
 // We only want to show object descriptions is there is no Storage Description
 if (strlen($t_item->get('ca_storage_locations.description'))>0){$nodescription = true;}else{$nodescription=null;}
 
 $out .= "<div class='displayLocationbyKey'>
              <div class='case'> 
                  <div class='barcode'>";
                  
 // Now look through all external_links and only display Barcode for "Public Shortcuts":
 $noobjectbarcode=false; // Startoff assuming no Storage Location Barcode, so do Object Barcodes
 $the_url_types=$t_item->get('ca_storage_locations.external_link.link_type',array('convertCodesToDisplayText' => true,'returnAsArray' => true));
     foreach ($the_url_types as $key=>$url_type) {             
        if ($url_type =="Public Shortcuts"){ 
       	   $paths = $t_item->get('ca_storage_locations.external_link.url_entry',array('returnAsArray' => true));
       	   $path = $paths[$key];
       	   $vs_path = caGenerateBarcode($path, array( 'checkValues' => $the_object->opa_check_values,'type' => 'qrcode', 'height' => 2));
           $out .= "<img src='$vs_path.png'/><br/>$path";
           $noobjectbarcode = true;
        }// if "Public Shortcuts
     }// foreach..
  	
 $out .= "        </div>
                  <div class='key'>".$t_item->get('ca_storage_locations.preferred_labels')."</div>
                  <br/>
                  <div class='keydescription'>".$t_item->get('ca_storage_locations.description')."</div><hr/>";

 // --- End of Header ---
 // Now get all objects within THIS Storage_Location (Key):
 $out .= getListofObjectsInThisStorageLocation($t_item);
 $out .= "</div></div>";	
    
 $css= "
   <style type='text/css'>
     .storagepath {text-align:center;font-size:14px;}
     
     .arrow-right {width: 0;  height: 0; border-top: 8px solid transparent; border-bottom: 8px solid transparent; border-left: 8px solid green;}
     .arrow-down {width: 0;   height: 0; border-left: 8px solid transparent;border-right: 8px solid transparent;  border-top: 8px solid #f00; }
     .circle {width: 10px; height: 10px; background: blue;  border-radius: 5px;}
     .diamond-shield {width: 0;  height: 0; border-top: 8px solid transparent; border-bottom: 8px solid transparent; border-left: 8px solid green;}
     .diamond-shield::after {width: 0;   height: 0; border-left: 8px solid transparent;border-right: 8px solid transparent;  border-top: 8px solid #f00; }
	
     .displayLocationbyKey		   {width: 400px;display:inline-block;}
     .displayLocationbyKey  .case 	   {border-radius: 10px;border:1px solid black; padding:14px; width:500px;}	
     .displayLocationbyKey .barcode        {width:100px;height:100px;float:right;font-size:10px;word-wrap: break-word;overflow-wrap: break-word;}
     .displayLocationbyKey .keydescription {text-align:left;font-size:1.5em;}
     .displayLocationbyKey .object         {width:500px;height:80px;display:inline-block;}			
     .displayLocationbyKey .objectl         {width:500px;height:180px;display:inline-block;}			
     
     .displayLocationbyKey .idno           {font-weight:normal;font-size:16px;display:inline-block;width:110px;}
     .displayLocationbyKey .itemname       {font-weight:normal ;font-style:normal;font-size:16px; display:inline-block;	}
     .displayLocationbyKey .description    {float:left;display:inline-block; font-weight:normal; font-size:14px;}
     .displayLocationbyKey .key            {border-radius: 10px;font-size: 5em; width: 100px; height: 100px; 
                                            color: #FFF; background: #000 none repeat scroll 0% 0%; text-align: center;	}
     .displayLocationbyKey .img	           {display:inline-block;float:right; width:100px;}			
    	
  </style>";//style sheet
 
  print $this->render("pdfStart.php");
  echo $css;
  print $this->render("header.php");
  print $this->render("footer.php");
  echo $out;
  print $this->render("pdfEnd.php");
?>