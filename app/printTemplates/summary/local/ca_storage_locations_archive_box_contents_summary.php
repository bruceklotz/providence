<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ca_storage_locations_archive_box_contents_summery.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Storage Locations Archive Box Contents Summary V1.1.2
 * @type page
 * @pageSize letter 
 * @pageOrientation portrait
 * @tables ca_storage_locations
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 * @filename bobo
 * ----------------------------------------------------------------------
 */
	$version = "PHS Storage Locations Archive Box Contents Summary V1.1.2"; 
 	$t_item = $this->getVar('t_subject');
 	
 	
 	//Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_storage_locations.idno");  
	$sq =  "PHS_Storage_Locations_Archive_Box_Contents_Summary_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$this->setVar('filename',$sq);
  //$va_template_info->setOption('filename',$sq);	
  
  
   
	$this->setVar('headerTitle',$t_item->get('ca_storage_locations.preferred_labels'));
	$this->setVar('showPagenumber',true);
  $this->setVar('showPagenumbers',true);
  $this->setVar('version',$version);
  $this->setVar('summary_header_enabled',true);
  
  
  
  			
 /*******************************************\
 *               Functions                     *
  \*******************************************/
  if (!function_exists('getListofObjectsInThisStorageLocation')){
    function getListofObjectsInThisStorageLocation($this_location,$thus){
       
        $parent_locationid = $this_location->get('ca_storage_locations.location_id');
        $out="";
       
        if($this_contents = $this_location->getLocationContents('ca_movements',array('showChildren' => 0)))   //requires modified /models/ca_storage_locations
                {
            	  //In order to sort, we first load an array with all the data, then sort, then display...
                $objectsSorted =[];
                while( $this_contents->nextHit() != NULL ) {
                     
                     $objectname = $this_contents->get('ca_objects.preferred_labels.name');
                     $objectidno = $this_contents->get('ca_objects.idno');
                     $objectid = $this_contents->get('ca_objects.object_id');
                   
                     $the_object = new ca_objects($objectidno);
                     
                     $dateactives = $the_object->get('ca_movements.removal_date',array('returnAsArray' => true, 'sort' => array('ca_movements.removal_date')));
                     $dateactive = $dateactives[count($dateactives)-1];
                     
                     $oneimage = $the_object->get('ca_object_representations.media.thumbnail', array('filterNonPrimaryRepresentations' => true ));
                     
                     
                 $vs_path = caGenerateBarcode( $objectid	, array('checkValues' => $thus->opa_check_values, 'type' => 'code128', 'height' => 30));
               
                 
                 $out .="<div style='top:$thisrow;' class='idno'>$objectidno</div>
                         <div style='top:$thisrow;' class='name'>$objectname</div>
                         
                        
                 
                
                 <div style='top:$thisrow;' class='obj_image'>".$oneimage."&nbsp;</div>
                         
                          <div style='top:$thisrow;' class='ldate'>$dateactive</div>
                      ";
   			
                
                
             }//while ...nextHit()
             
        }// if $this_contents = $this_location->getLocationContents('ca_movements'))...
        return $out;
    }//end function	
  }//if function exists
/***************************************************/
 			
			
  
  
  
  
  
  $locationid = $t_item->get('ca_storage_locations.idno');
  $location_id = $t_item->get('ca_storage_locations.location_id');
  //$root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/";
  
  $path = $t_item->getWithTemplate($root."^ca_storage_locations.idno");
  $vs_path = caGenerateBarcode($locationid, array('checkValues' => $this->opa_check_values, 'type' => 'code128', 'height' => 30));
	
	$root = "http://".$_SERVER["HTTP_HOST"]."/pa2/index.php/Detail/storage_locations/";
	$qr_path = caGenerateBarcode($root.$location_id, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 2));
  
  $contents = getListofObjectsInThisStorageLocation($t_item,$this);
  
	$out = $t_item->getWithTemplate("
	        <h1>^ca_storage_locations.preferred_labels</h1>
	        <h3>^ca_storage_locations.idno</h3>
					<ifdef code='ca_storage_locations.description'>^ca_storage_locations.description</ifdef>
					<unit> $contents
							<unit relativeTo='ca_storage_locations.children' delimiter='<br>'>
									<div class='case'>
										<div class='head'>
											<div class='key'>^ca_storage_locations.preferred_labels</div>
											<div class='qr'><img src='".caGenerateBarcode($root.$t_item->get('ca_storage_locations.location_id'), array( 'checkValues' => $t_storage_location->opa_check_values,'type' => 'qrcode', 'height' => 1)).".png'/><br/>$path</div>
											<div class='a_bk'></div><br/>
											<div class='keydescription'>^ca_storage_locations.description</div>
										</div>	
											<unit relativeTo='ca_objects.ca_objects_location' delimiter=' '>
											  <div class='object'>
													<span class='idno'>[^ca_objects.idno]</span><span class='itemname'> ^ca_objects.preferred_labels</span><br/>
													<ifdef code='ca_objects.display_text'><span class='description'>^ca_objects.display_text</span></ifdef>
										    </div>
										  </unit>
									</div>
							</unit>
					</unit>
					<hr/><br/><br/>
					<div id='barcode'><img src='$vs_path.png'/>$locationid</div> 
	        <div id='qr'><img src='$qr_path.png'/>$root.$location_id</div> 
			");	
			
			

			
			
			
/**************************************************
 *                Style Sheet                     *
 **************************************************/
   $css= "
<style type='text/css'>

    .object    {width:135px;float:left;font-size:10px;height:10px;margins:0px;}  
    .idno      {width:65px; height:10px;font-weight:bold;font-size:10px;display:inline-block;text-align:right;line-height:.9;}
     
   #version      {font-size:6px;position: absolute;
                top: 210px;left:0px;}		
 #timestamp    {font-size:12px;text-align:right;position: absolute;
                top: 198px;left:320px;}	  
   #barcode {
        height:50px;
        font-family:'Times New Roman';
        width:300px;
        float:left;;
        text-align:center;
        margin:0px;
        vertical-align:bottom;
      }
      #barcode img{
        width:100%;
        height:100%;
      }
      #qr {
        border:1px solid green;
        height:auto;
        font-family:'Times New Roman';
        font-size:8px;
        width:125px;
        text-align:center;
        word-wrap: break-word;
        float:right;
        padding:5px;
        
      }
                            
</style>";//style sheet

/*****************************************
 *               BEGIN                   *	
 *****************************************/	
	print $this->render("pdfStart.php");
	echo $css;
	print $this->render("header.php");
	print $this->render("footer.php");	
	echo $out; 
	print $this->render("pdfEnd.php");
	?>