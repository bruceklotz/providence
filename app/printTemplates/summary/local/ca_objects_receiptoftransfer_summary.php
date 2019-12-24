<?php
/* ----------------------------------------------------------------------
 *app/printTemplates/summarys/local/receptoftransfer_summary.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=- 
 * Template configuration:
 *
 * @name PHS Receipt of Transfer V1.0.4 Summary
 * @type page
 * @pageSize letter
 * @pageOrientation portrait
 * @tables ca_objects, ca_object_lots
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
   $version = "PHS Receipt of Transfer V1.0.4 Summary"; 
   $t_item 				= $this->getVar('t_subject');
   $va_bundle_displays 	= $this->getVar('bundle_displays');
   $t_display 				= $this->getVar('t_display');
   $va_placements 			= $this->getVar("placements");
   //$this->setVar('headerTitle','Receipt of Transfer');
   
   //Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_objects.idno");  
	$sq =  "PHS_Receipt_of_Tranfer_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$this->setVar('filename',$sq);
	 
//--- Only retrieve the correct type of record:
   $to_array	= $t_item->get('ca_objects.deed_of_gift', array('convertCodesToDisplayText' => true,'returnWithStructure' => true));
   $type_array	= $t_item->get('ca_objects.deed_of_gift.type', array('convertCodesToDisplayText' => true,'returnWithStructure' => true));
   foreach($to_array as $key=>$toitems){
      foreach($toitems as $ikey=>$toitem){
         if ($type_array[$key][$ikey]['type'] == "give"){
            $deed_of_gift_full_name = $toitem['deed_of_gift_full_name'];
            $deed_of_gift_address_block = $toitem['deed_of_gift_address_block'];
           
            $transferto = "<div id='receptofgift_donorl'>Transfered To:</div> 
                           <div id='receptofgift_donor'>$deed_of_gift_full_name<br/>$deed_of_gift_address_block<br/>".
	                      $toitem['deed_of_gift_phone'].
                           "</div>";
            $deeddate =    "<div id='deedofgift_datel'>Date:</div><div id='deedofgift_date'>".$toitem['deed_of_gift_date']."</div>";
         
            $deed_of_gift_description = $toitem['deed_of_gift_description'];
            
            $legal_text =  "<div id='legaltext2'>".$toitem['legal_text']."</div>";
            
            $legal_footer = "<div id='deedofgift_legalfoot'><br/><br/><br/> ".$toitem['legal_footer']."</div>";
         }//if
      }//foreach
   }//foreach


//--------------------------
   $out = $t_item->getWithTemplate("
      <div class='deedofgift'><div id='version'>$version</div>
         <div id='deedofgift_title'>Receipt of Transfer</div>
         <div id='receptofgift_top'>
            This Receipt acknowledges the transfer of the object(s) described below, from the<br/>
            <b>Pelham Historical Society, Inc.</b> of  <b>Pelham, MA.</b> to <b>$deed_of_gift_full_name</b> of<br/>
	    <b>$deed_of_gift_address_block.</b> 
         </div>		
         <div id='deedofgift_accessionl'>Accession No:</div><div id='deedofgift_accession'>^ca_objects.idno</div>	
         $transferto
         $deeddate
          <div id='deedofgift_body'>			
            $legal_text
            <div id='deedofgift_descriptionl'><br/>Description of Gift/Donation:<br/></div>
            <div id='deedofgift_description'>
               <span style='font-size:10px; font-style:italic;'>
                  &nbsp;&nbsp;&nbsp;&nbsp;<b>Lot Id:</b> ^ca_object_lots &nbsp;&nbsp;&nbsp;&nbsp;
                  <b>Accession Id:</b> ^ca_objects.idno<br/>
               </span>
               $deed_of_gift_description
            </div>		
            $legal_footer
          </div>
          <div id='receptofgift_sigblock'> 
	      <b>I certify that the object(s) transferred are as described, and that I am authorized to represent the Pelham Historical Society, Inc.
	      in this matter: </b><br/><br/>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature: _________________________________________________   Date: ________________ <br/><br/>
              &nbsp;&nbsp;&nbsp;&nbsp;Printed Name:__________________________________________________Title: ________________ <br/><br/><br/>
           
              <b>I certify that I have Received and Accepted the object(s) as described, in accordance to all terms and conditions set forth and described above.
              I further certify that I am authorized to represent $deed_of_gift_full_name in this matter:</b><br/><br/>	
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature: _________________________________________________   Date: ________________ <br/><br/>
              &nbsp;&nbsp;&nbsp;&nbsp;Printed Name:__________________________________________________Title: ________________ <br/><br/>
           </div>		
        </div>");
	
/**************************************************
 *                Style Sheet                     *
 **************************************************/
  $css= "
<style type='text/css'>
  #deedofgift_title{text-align:center;font-size:35px;position: absolute;
                    top: 30px; left: 250px;}
  #deedofgift_top  {text-align:center; width:730px; height:100px; position: absolute;
                    top: 250px; left: 30px;}
  #deedofgift_accessionl{width:100px; height:30px;font-weight:bold; position: absolute;
                    top: 85px; left: 30px;}
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
  #receptofgift_donorl{ width:150px; height:50px; font-weight:bold; position: absolute;
                    top: 140px; left:30px; } 
  #receptofgift_donor{ width:450px; height:50px; position: absolute;
                    top: 140px; left: 140px;}    
  #receptofgift_sigblock{ width:720px; height:80px; position: absolute;
                    top: 640px; left: 30px; }
  #receptofgift_top{text-align:center; width:730px; height:100px;position: absolute;
                    top: 250px; left: 30px;}
    
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