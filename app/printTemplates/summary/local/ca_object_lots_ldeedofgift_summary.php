<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ldeedofgift.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS_Deed_of_Gift_V1.0.3Lot_Summary
 * @type page 
 * @pageSize letter
 * @pageOrientation portrait
 * @tables ca_object_lots, ca_objects
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
	$version = "PHS Deed of Gift V1.0.3 Lot Summary"; 
 	$t_item 				= $this->getVar('t_subject');
	$va_bundle_displays 	= $this->getVar('bundle_displays');
	$t_display 				= $this->getVar('t_display');
	$va_placements 			= $this->getVar("placements");
	$this->setVar('hideTimestamp',true);
	//	$this->setVar('headerTitle','Deed of Gift');
	//Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_object_lots.idno_stub");  
	$sq =  "PHS_Deed_of_Gift_Lot_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$this->setVar('filename',$sq);
	
//--- Only retrieve the correct type of record:
   $from_array	= $t_item->get('ca_object_lots.deed_of_gift', array('convertCodesToDisplayText' => true,'returnWithStructure' => true));
   $type_array	= $t_item->get('ca_object_lots.deed_of_gift.type', array('convertCodesToDisplayText' => true,'returnWithStructure' => true));
   foreach($from_array as $key=>$fromitems){
      foreach($fromitems as $ikey=>$fromitem){
         if ($type_array[$key][$ikey]['type'] == "receive"){
            $deed_of_gift_full_name = $fromitem['deed_of_gift_full_name'];
            $deed_of_gift_address_block = $fromitem['deed_of_gift_address_block'];
            $deed_of_gift_date = $fromitem['deed_of_gift_date'];
            $transferfrom = " <div id='deedofgift_donorl'>Donor(s):</div> 
                              <div id='deedofgift_donor'>$deed_of_gift_full_name<br/>$deed_of_gift_address_block<br/>".
	                      $fromitem['deed_of_gift_phone'].
                           "</div>";
            $deeddate =    "<div id='deedofgift_datel'>Date:</div><div id='deedofgift_date'>$deed_of_gift_date</div>";
         
            $deed_of_gift_description = $fromitem['deed_of_gift_description'];
            
            $legal_text =  "<div id='legaltext2'>".$fromitem['legal_text']."</div>";
            
            $legal_footer = "<div id='deedofgift_legalfoot'><br/><br/><br/> ".$fromitem['legal_footer']."</div>";
         }//if
      }//foreach
   }//foreach


//--------------------------	
   $out = $t_item->getWithTemplate("
      <div id='deedofgift_title'>Deed of Gift</div>
      <div id='deedofgift_top'>
         This <b>Deed of Gift</b> is made on <b>$deed_of_gift_date</b> by 
         <b>$deed_of_gift_full_name</b> of<br/>
         <b>$deed_of_gift_address_block</b> 
         to the <b>Pelham Historical Society, Inc.</b> of <b>Pelham, MA.</b>
      </div>		
      <div id='deedofgift_accessionl'>Accession <br/>Lot No:</div><div id='deedofgift_accession'>^ca_object_lots.idno_stub</div>	
      $transferfrom	
      $deeddate
      <div id='deedofgift_body'>			
         $legal_text
         <div id='deedofgift_descriptionl'><br/>Description of Gift/Donation:<br/></div>
         <div id='deedofgift_description'>
            <span style='font-size:10px; font-style:italic;'>
               &nbsp;&nbsp;&nbsp;&nbsp;<b>Lot Id:</b> ^ca_object_lots.idno_stub &nbsp;&nbsp;&nbsp;&nbsp;
               <b>Accession Id(s):</b> ^ca_objects.idno%delimiter=,_ <br/>
            </span>
            $deed_of_gift_description
         </div>		
         $legal_footer
      </div>
      <div id='deedofgift_sigblock'> 
         Donor Signature: _________________________________________________  Date: ________________ <br/><br/>
         &nbsp;&nbsp;&nbsp;&nbsp;Printed Name:__________________________________________________<br/><br/><br/><br/>
         <b>Accepted by the Pelham Historical Society:</b><br/><br/>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature: _________________________________________________   Date: ________________ <br/><br/>
         &nbsp;&nbsp;&nbsp;&nbsp;Printed Name:__________________________________________________Title: ________________ <br/><br/>
       </div>");
	
/**************************************************
 *                Style Sheet                     *
 **************************************************/
  $css= "
<style type='text/css'>
  #deedofgift_title{text-align:center;font-size:35px;position: absolute;
                    top: 30px; left: 290px;}
  #deedofgift_top  {text-align:center; width:730px; height:100px; position: absolute;
                    top: 250px; left: 30px;}
  #deedofgift_accessionl{width:100px; height:30px;font-weight:bold; position: absolute;
                    top: 75px; left: 30px;}
  #deedofgift_accession{width:200px; height:30px;font-size:24px; position: absolute;
                    top: 80px; left: 130px;}
  #deedofgift_datel{font-weight:bold;position: absolute;
                    top: 80px; left: 590px;} 
  #deedofgift_date {position: absolute;
                    top: 80px; left: 630px;} 
  #deedofgift_donorl{width:100px; height:50px; font-weight:bold;  position: absolute;
                    top: 140px; left:30px; } 
  #deedofgift_donor{width:220px; height:50px; position: absolute;
                    top: 140px; left: 130px;}    			
  #deedofgift_body {width:730px; height:380px; position: absolute;
                    top: 320px; left: 30px;}
  #legaltext2{}				
  #deedofgift_descriptionl{font-weight:bold;}
  #deedofgift_description{}	
  #deedofgift_legalfoot{height:80px;text-align:center;}	
  #deedofgift_sigblock{ width:720px; height:80px; position: absolute;
                    top: 720px; left: 30px;}	
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