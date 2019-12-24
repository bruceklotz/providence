<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ca_storage_locations_object_bin_label_summary.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Storage Locations Object Bin Label Summary V1.0.6
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
	$version = "PHS Storage Locations Object Bin Label Summary V1.0.6"; 
 	$t_item = $this->getVar('t_subject');
 	
 	
 	//Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_storage_locations.idno");  
	$sq =  "PHS_Storage_Locations_Object_Bin_Label_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$this->setVar('filename',$sq);
  //$va_template_info->setOption('filename',$sq);	
   
	//$this->setVar('headerTitle','Objects Bin Label');
	$this->setVar('showPagenumber',false);
  //$this->config->set('summary_show_timestamp',false);
	
	$out = $t_item->getWithTemplate("
		<div class='accession'>
			<div id='version'>$version $textlen</div>
			<div id='timestamp'>".caGetLocalizedDate(null, array('dateFormat' => 'delimited'))."</div>");
			
//--- Generate Barcode ------
	$root = 'http://pelhamhs.org/ca/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/'; // this should get pulled out or automated somehow.
	$path = $t_item->getWithTemplate($root."^ca_storage_locations.location_id");
	$vs_path = caGenerateBarcode($path, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 3));
	
	$barcode = "<div id='barcode'><img src='$vs_path.png'/><br/>$path</div>";
	
  $parent_locationid = $t_item->get('ca_storage_locations.location_id');
  $parent_location = $t_item->get('ca_storage_locations.preferred_labels');
  
  // Get all objects within THIS Storage_Location
 
  //$this_contents = $t_item->getLocationContents('ca_movements');
  if($this_contents = $t_item->getLocationContents('ca_movements')){
       //In order to sort, we first load an array with all the data, then sort, then display...
       $objectsSorted =[];
       while( $this_contents->nextHit() != NULL ) {
            $objectindex =$this_contents->get('ca_objects.idno');
            $objectsSorted[$objectindex]['objectname'] = $this_contents->get('ca_objects.preferred_labels.name');
            $objectsSorted[$objectindex]['objectidno'] = $this_contents->get('ca_objects.idno');
            $objectsSorted[$objectindex]['objectid'] = $this_contents->get('ca_objects.object_id');
            $objectsSorted[$objectindex]['type'] = $this_contents->get('ca_objects.type_id',array('convertCodesToDisplayText' => true));
            
            $the_object = new ca_objects($objectsSorted[$objectindex]['objectid']);
                     
            $dateactives = $the_object->get('ca_movements.removal_date',array('returnAsArray' => true, 'sort' => array('ca_movements.removal_date')));
            $objectsSorted[$objectindex]['dateactive'] = $dateactives[count($dateactives)-1];
             
            // Generate Barcode:
            if ($showbarcodes !="no"){
                 $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/objects/ObjectEditor/Edit/object_id/";
                 //$path =$root.$objectsSorted[$objectindex]['objectid'];
                 $path =$objectsSorted[$objectindex]['objectid'];
                 $objectsSorted[$objectindex]['vs_path'] = caGenerateBarcode($path, array( 'checkValues' => $this_contents->opa_check_values,'type' => 'code128', 'height' => $halfheight));
		        }//if $showbarcodes
		 						
            $objectsSorted[$objectindex]['oneimage'] = $the_object->get('ca_object_representations.media.thumbnail', array('filterNonPrimaryRepresentations' => true ));
            // get the src for that image
            preg_match( "@src='([^']+)'@" , $objectsSorted[$objectindex]['oneimage'], $matches);
            $objectsSorted[$objectindex]['oneimagesrc'] = array_pop($matches);
                   
       }//while ...nextHit()
 
       //Now sort
       ksort( $objectsSorted);
              
       $objectcount = count( $objectsSorted);
             
       //Now retrieve and display...
       foreach ( $objectsSorted as $objectindex => $item){
            $rowcount++;            
            $objectname = $item['objectname'];
            $objectidno = $item['objectidno'];
            $name_array = $item['name_array'];
            $type_array = $item['type_array']; 
            $objectid = $item['objectid'];
            $type = $item['type'];
            $dateactive = $item['dateactive'];
            $vs_path = $item['vs_path'];
		        $oneimage = $item['oneimage'];
		        $oneimagesrc = $item['oneimagesrc'];
                 
            //--- "alternate" nonpreferred_labels ------
            $fullname ="<div id='titled'>";                  
            if($name_array){
                 foreach($name_array as $key=>$nameitems){
                     foreach($nameitems as $ikey=>$nameitem){
                          if ($type_array[$key][$ikey]['type_id'] == "alternate"){$fullname .= $nameitem['name'];}
                     }
                 }//foreach
            }//if
            $fullname .="</div>";
            //------------------------------------------          
            $lines++;
            switch ($objectcount){
           	  case 1: 
           	         // Format for a single object result...
                     $objectout .= "<div class='idno1'>$objectidno</div><div class='objectn1'>$objectname &nbsp;&nbsp;</div>";
                     $objectout .= $fullname;
                     $image =	"<div class='mediar'> $oneimage</div>";

                     //--- Generate Barcode ------
	                   $root = 'http://pelhamhs.org/ca/index.php/editor/objects/ObjectEditor/Edit/object_id/'; // this should get pulled out or automated somehow.
	                   $path = $root.$objectid;
	                   $vs_path = caGenerateBarcode($path, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 3));
	                   $barcode = "<div id='barcode'><img src='$vs_path.png'/><br/>$path</div>";
                     break;
              case 2:
              case 3:
              case 4:
                     // Format for a few (2-4) object results...
                     $objectimage =	"<div class='mediar2'>$oneimage</div>";
                     $objectout .= "<div class='object2'><div class='idno2'>$objectidno  </div><div class='objectn2'>- $objectname</div>
                                    </div> $objectimage <br/>";
                     break;
              default:
                     // Format for many object results...
                     $objectout .= "<div class='object'><div class='idno'>$objectidno - </div><div class='objectn'>$objectname &nbsp;&nbsp;</div></div>";
		                 if($lines > 2){$lines=0;$objectout .="<br/>";}
            }//case           
      
      
      
       }//foreach	
   }//if if $this_contents 
                 
   $out .= "<div id='objects'>$objectout</div>";
   $outl = "<div class='locationl'>Location:</div>
            <div class='locationn'>^ca_storage_locations.preferred_labels.name</div>
            <div class='locationp'>[ ^ca_storage_locations.hierarchy.preferred_labels.name%removeFirstItems=1 ]</div>";
   $out .=  str_replace(";"," &raquo; ",$t_item->getWithTemplate($outl));	
   $out .= "$barcode $image </div>";
   $out = $t_item->getWithTemplate($out);
	
/**************************************************
 *                Style Sheet                     *
 **************************************************/
   $css= "
<style type='text/css'>
 
 #objects      {width:350px; height:60px;position: absolute;
                top: 28px; left: 105px;}
    .object    {width:135px;float:left;font-size:10px;height:10px;margins:0px;}  
    .object2   {width:350px;float:left;font-size:10px;margins:0px;}  
    .idno      {width:65px; height:10px;font-weight:bold;font-size:10px;display:inline-block;text-align:right;line-height:.9;}
    .idno1     {width:300px; height:40px; font-size:40px; position: absolute; 
                 top:0px; left:0px;}
    .idno2      {width:155px; height:28px;font-weight:bold;font-size:25px;display:inline-block;text-align:right;line-height:.9;}
    
    .objectn   {width:72px; height:20px;display:inline-block;font-size:10px;overflow:hidden;line-height:.9;}              
    .objectn1  {width:300px; height:20px; font-size:20px; position: absolute; 
                 top: 43px; left:0px;}
    .objectn2  {width:175px; height:28px;display:inline-block;font-size:20vw;overflow:hidden;line-height:.9;}   
    /* font-size: calc(#{$min_font}px + (#{$max_font} - #{$min_font}) * ( (100vw - #{$min_width}px) / ( #{$max_width} - #{$min_width})));*/           
 #barcode      {width:100px; height:100px;font-size:6px;word-wrap: break-word;overflow-wrap: break-word;	position: absolute;
                top: 28px; left: 0px;}
 #barcode img  {height:100px;}
    .mediar        {width:75px; height:75px;	position: absolute; 
                 top: 35px; left: 340px;}
    .mediar2   {width:28px; height:28px;display:inline-block;}
    .mediar2 img    {width:25px; height:25px;}

 #titled       {width:300px; height:20px; font-size:20px; word-wrap: break-word;position: absolute;	
                top: 65px; left: 0px;}				
   .locationl  {width:90px; font-size:14px; text-align:right;  position: absolute; 
                top: 150px; left: 0px;}    		
   .locationn  {width:200px;font-size:25px;font-weight:bold;padding:2px;background-color:black;color:white;display:inline-block;position: absolute; 
                top: 140px; left: 105px;}
   .locationp  {font-size:10px;position: absolute; 
                top: 180px; left: 105px;}	
 #version      {font-size:6px;position: absolute;
                top: 210px;left:0px;}		
 #timestamp    {font-size:12px;text-align:right;position: absolute;
                top: 198px;left:320px;}	                 
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