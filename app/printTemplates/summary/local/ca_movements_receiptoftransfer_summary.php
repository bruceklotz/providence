<?php
/* ----------------------------------------------------------------------
 *app/printTemplates/summarys/local/receptoftransfer_summary.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Receipt of Transfer V1.0.5 Summary
 * @type page
 * @pageSize letter
 * @pageOrientation portrait
 * @tables ca_movements
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
   $version = "PHS Receipt of Transfer V1.0.5 Summary"; 
   $t_item 				= $this->getVar('t_subject');
   $va_bundle_displays 	= $this->getVar('bundle_displays');
   $t_display 				= $this->getVar('t_display');
   $va_placements 			= $this->getVar("placements");
   //$this->setVar('headerTitle','Receipt of Transfer');
   
   //Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_objects.idno");  
	$sq =  "PHS_Receipt_of_Tranfer_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$this->setVar('filename',$sq);


   $to_array	= $t_item->get('ca_movements.record_of_movement', array('convertCodesToDisplayText' => true,'returnWithStructure' => true));
   $type_array	= $t_item->get('ca_movements.record_of_movement.movement_type', array('convertCodesToDisplayText' => true,'returnWithStructure' => true));
 
   // step through each record_of_movement...
   foreach($to_array as $key=>$toitems){
      foreach($toitems as $ikey=>$toitem){
      
         //--- Only retrieve the correct type of record:
         if ($type_array[$key][$ikey]['type'] == "to storage" or true){
            $movement_receiver = $toitem['movement_receiver'];
            $movement_location = $toitem['movement_location'];
            $transferto    = "<div id='receptofgift_donorl'>Transfered To:</div> 
                              <div id='receptofgift_donor'>$movement_receiver<br/>$movement_location<br/></div>";
            $receipt_date  = "<div id='receipt_datel'>Date:</div><div id='receipt_date'>".$toitem['movement_date']."</div>";
            $movement_description = $toitem['movement_description'];
            $legal_text    = "<div id='legaltext2'>".$toitem['movement_legal_text']."</div>";
            $legal_footer  = "<div id='receipt_legalfoot'><br/><br/><br/> ".$toitem['legal_footer']."</div>";
         }//if
      }//foreach
   }//foreach
   
   //Now get the contents of the storage_location of the movement, not just the contents of the movement.
   $this_location = new ca_storage_locations($t_item->get('ca_storage_locations.location_id'));
   if($this_contents = $this_location->getLocationContents('ca_movements',array('showChildren' => 0)))   //requires modified /models/ca_storage_locations
      {
       while( $this_contents->nextHit() != NULL ) {
       	   $object_list .= "<i>". $this_contents->get('ca_objects.idno')."</i> - ".$this_contents->get('ca_objects.preferred_labels.name')." &nbsp;&nbsp;&nbsp;"; 
       }//while
   }//if $this_content...
   //<unit relativeTo='ca_objects' delimiter=', '>  ^ca_objects.idno - ^ca_objects.preferred_labels.name &nbsp;&nbsp;&nbsp;</unit><br/>
//--------------------------
   $out = $t_item->getWithTemplate("
        <div class='receipt'>
           <div id='version'>$version</div>
           <div id='receipt_title'>Receipt of Transfer</div>
           $receipt_date
           <div id='receipt_top'>
             This Receipt acknowledges the transfer of the object(s) described below and owned by the<br/>
             <b>Pelham Historical Society, Inc.</b> of <b>Pelham, MA.</b> <br/>
             to the <b>$movement_receiver</b> for storage at <b>$movement_location</b> .
             
           </div>		
           <div id='receipt_descriptionl'><u>Description of Object(s) Moved:</u></div><div id='movement_id'>Movement Record id: ^ca_movements.idno</div>
           <div id='receipt_description'>
                <b>Box Id:</b> ^ca_storage_locations <br/>
                <b>Object Accession Id(s):</b> </br>
              $object_list
           $movement_description
           </div>     
           <div id='legal_text'>$legal_text</div>		
             $legal_footer
           <div id='receptofgift_sigblock'> 
	             <b>I certify that the object(s) transferred are as described, and that I am authorized to represent the Pelham Historical Society, Inc.
	             in this matter: </b><br/><br/>
               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature: _________________________________________________   Date: ________________ <br/><br/>
               &nbsp;&nbsp;&nbsp;&nbsp;Printed Name:__________________________________________________Title: ________________ <br/><br/><br/>
           
               <b>I certify that I have Received and Accepted the object(s) as described, in accordance to all terms and conditions set forth and described above.
               I further certify that I am authorized to represent $movement_receiver in this matter:</b><br/><br/>	
               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature: _________________________________________________   Date: ________________ <br/><br/>
               &nbsp;&nbsp;&nbsp;&nbsp;Printed Name:__________________________________________________Title: ________________ <br/><br/>
           </div>		
        </div>");
	
/**************************************************
 *                Style Sheet                     *
 **************************************************/
  $css= "
<style type='text/css'>
  #receipt_title{text-align:center;font-size:35px;position: absolute;
                    top: 30px; left: 250px;}
  #receipt_top  {text-align:center; width:730px; height:100px; position: absolute;
                    top: 150px; left: 30px;}
 
  
  
  #receipt_datel{font-weight:bold;position: absolute;
                    top: 80px; left: 590px;} 
  #receipt_date {position: absolute;
                    top: 80px; left: 630px;}  			
  #legal_text {width:730px; height:380px; position: absolute;
                    top: 420px; left: 30px;}
  #legaltext2{}	 
  #movement_id  {width:100%;text-align:center;font-size:12px; position: absolute;
                    top: 285px; left: 30px;}		
  #receipt_descriptionl{width:100%;text-align:center;font-weight:bold; position: absolute;
                    top: 260px; left: 30px;}
  #receipt_description{width:100%;text-align:center; position: absolute;
                    top: 300px; left: 30px;}	
  #receipt_legalfoot{height:80px;text-align:center;}	
  #receipt_sigblock{ width:720px; height:80px; position: absolute;
                    top: 720px; left: 30px;}	
  #receptofgift_donorl{ width:150px; height:50px; font-weight:bold; position: absolute;
                    top: 140px; left:30px; } 
  #receptofgift_donor{ width:450px; height:50px; position: absolute;
                    top: 140px; left: 140px;}    
  #receptofgift_sigblock{ width:720px; height:80px; position: absolute;
                    top: 640px; left: 30px; }
  #recept_top{text-align:center; width:730px; height:100px;position: absolute;
                    top: 100px; left: 30px;}
    
  #version         {font-size:6px;position: absolute;
                    top: 950px;left:0px;}			
</style>";//style sheet	
	
	
	print $this->render("pdfStart.php");
	echo $css;
	print $this->render("header.php");
	print $this->render("footer.php");
	echo $out;
	print $this->render("pdfEnd.php");

	?>