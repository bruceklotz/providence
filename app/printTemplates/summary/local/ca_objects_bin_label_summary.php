<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ca_objects_bin_label_summary.php PELHAMHS.ORG 
 * ----------------------------------------------------------------------
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Objects Bin Label V1.0.5
 * @type page
 * @pageSize letter
 * @pageOrientation portrait
 * @tables ca_objects
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
	$version = "PHS Objects Bin Labels V1.0.5 Summary"; 
 	$t_item = $this->getVar('t_subject');
 	
 	//Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_objects.idno");  
	$sq =  "PHS_Object_Bin_Label_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$this->setVar('filename',$sq);
	
	
	//$this->setVar('headerTitle','Objects Bin Label');
	$this->setVar('showPagenumber',false);
        //$this->config->set('summary_show_timestamp',false);
	 
	$out = $t_item->getWithTemplate("
		<div class='accession'>
			<div id='version'>$version $textlen</div>
			<div id='timestamp'>".caGetLocalizedDate(null, array('dateFormat' => 'delimited'))."</div>
			<div id='name'>^ca_objects.preferred_labels</div>
			<div id='mediar'>^ca_object_representations.media.thumbnail</div>");
	
//--- Generate Barcode ------
	$root = 'http://pelhamhs.org/ca/index.php/editor/objects/ObjectEditor/Edit/object_id/'; // this should get pulled out or automated somehow.
	$path = $t_item->getWithTemplate($root."^ca_objects.object_id");
	$vs_path = caGenerateBarcode($path, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 3));
	
	$out .= "<div id='barcode'><img src='$vs_path.png'/><br/>$path</div>";
	
//--- "alternate" nonpreferred_labels ------
  $fullname ="<div id='titled'>";
  $name_array	= $t_item->get('ca_objects.nonpreferred_labels', array('convertCodesToDisplayText' => true,'returnWithStructure' => true));
  $type_array	= $t_item->get('ca_objects.nonpreferred_labels.type_id', array('convertCodesToDisplayText' => true,'returnWithStructure' => true));
  foreach($name_array as $key=>$nameitems){
     foreach($nameitems as $ikey=>$nameitem){
        if ($type_array[$key][$ikey]['type_id'] == "alternate"){$fullname .= $nameitem['name'];}
     }
  }
  $fullname .="</div>";
	
	$out .= $t_item->getWithTemplate("
			<div id='idno'>^ca_objects.idno</div>	
			$fullname");

//--- Storage Location -----
 // Get the current objects's last movement

  $object_id = $t_item->get('ca_objects.object_id'); 
  $the_object = new ca_objects($object_id);
  if( $the_movement = $the_object->getLastMovement(array('dateElement' => 'removal_date','object_id' =>'$object_id'))){
      $movement_id = $the_movement->get('movement_id');
      //Hack to get the location_id because we need to lookup ca_movements_x_storage_locations by movement_id (and not relationship_id as New ca_movements_x_storage_locations() requires )
      $o_data = new Db();
      $qr_result = $o_data->query("
           SELECT *
           FROM `ca_movements_x_storage_locations`
           WHERE `movement_id` =".$movement_id);
      while($qr_result->nextRow()) { $location_id = $qr_result->get('location_id'); }
      $this_location = new ca_storage_locations($location_id);
      $locationh = htmlspecialchars_decode($this_location->get('ca_storage_locations.hierarchy.preferred_labels.name',array('delimiter' => ' > ')));
      $location = htmlspecialchars_decode($this_location->get('ca_storage_locations.preferred_labels.name'));
  }else{$location="";$locationh="";}


  $the_location = $t_item->getLastMovement($object_id); 
  
/* */
//this doent work!
 // $t_item->deriveCurrentLocationForBrowse();
 // $location = $t_item->get('ca_objects.current_loc_class');
  
  //$the_location =$t_item->getLastLocation();  // This loads the class $the_location with the most current ca_storage_locations
  //$storage_location_idno = $the_location->get('ca_storage_locations.idno'); not needed
  if($the_location){
     $location = $the_location->get('ca_storage_locations.preferred_labels'); 
     $locationh = str_replace(";"," &raquo; ",$the_location->get('ca_storage_locations.hierarchy.preferred_labels.name')); 
  } 
/**/  

  $locationid = $t_item->get('current_loc_id'); 
  $out .= "	 
        <div class='locationl'>Location:</div>
        <div class='locationn'>$location</div>
        <div class='locationp'>[ $locationh ]<span style='font-size:6px;'> ($locationid)</span></div>
        $barcode2
     </div>";
  $out = $t_item->getWithTemplate($out);
	
/**************************************************
 *                Style Sheet                     *
 **************************************************/
  $css= "
<style type='text/css'>
 #name          {width:183px; height:20px; font-size:20px; position: absolute; 
                 top: 73px; left:105px;}
 #mediar        {width:75px; height:75px;	position: absolute; 
                 top: 35px; left: 340px;}
 #mediar img    {height:75px;}
 #barcode       {width:100px; height:100px;font-size:6px;word-wrap: break-word;overflow-wrap: break-word;	position: absolute;
                 top: 28px; left: 0px;}
 #barcode img   {height:100px;}
 #idno          {width:183px; height:20px; font-size:40px; position: absolute; 
                 top: 28px; left: 105px;}
 #titled        {width:300px; height:20px; font-size:20px; word-wrap: break-word;position: absolute;	
                 top: 95px; left: 105px;}				
   .locationl   {width:90px; font-size:14px; text-align:right;  position: absolute; 
                 top: 150px; left: 0px;}   
   
   .locationn   {width:200px;font-size:25px;font-weight:bold;padding:2px;background-color:black;color:white;display:inline-block;position: absolute; 
                 top: 140px; left: 105px;}
   .locationp   {font-size:10px;position: absolute; 
                 top: 180px; left: 105px;}	
 #version       {font-size:6px;position: absolute;
                 top: 210px;left:0px;}		
 #timestamp     {font-size:12px;text-align:right;position: absolute;
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