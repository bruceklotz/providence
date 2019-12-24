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
 * @name PHS Receipt of Transfer V1.0.2 Summary
 * @type page
 * @pageSize letter
 * @pageOrientation portrait
 * @tables *
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
	$version = "PHS Receipt of Transfer V1.0.2 Summary"; 
 	$t_item 				= $this->getVar('t_subject');
	$va_bundle_displays 	= $this->getVar('bundle_displays');
	$t_display 				= $this->getVar('t_display');
	$va_placements 			= $this->getVar("placements");
	//$this->setVar('headerTitle','Receipt of Transfer');
	
	
 
 	//$vo_result = $this->getVar('result');	
 	//$vo_result->seek(0);
	//$vo_result->nextHit();  //while.....

	$out = $t_item->getWithTemplate("<div class='deedofgift'><div id='version'>$version</div>");
	$root = 'http://pelhamhs.org/ca/index.php/editor/objects/ObjectEditor/Edit/object_id/';
	$path = $t_item->getWithTemplate($root."^ca_objects.object_id");
	$vs_path = caGenerateBarcode($path, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 3));
		
	$out .= $t_item->getWithTemplate("
	<div id='deedofgift_title'>
			Receipt of Transfer
	</div>
	
	<div id='receptofgift_top'>
			This Receipt acknowledges the transfer of the object(s) described below, from the<br/>
			 <b>Pelham Historical Society, Inc.</b> of  <b>Pelham, MA.</b> to <b>^ca_objects.deed_of_gift.deed_of_gift_full_name</b> of<br/>
			 <b>^ca_objects.deed_of_gift.deed_of_gift_address_block.</b> 
	</div>		

	<div id='deedofgift_accessionl'>Accession No:</div><div id='deedofgift_accession'>^ca_objects.idno</div>	
	
	<div id='receptofgift_donorl'>Transfered To:</div> 
	<div id='receptofgift_donor'>
			^ca_objects.deed_of_gift.deed_of_gift_full_name <br/> 
			^ca_objects.deed_of_gift.deed_of_gift_address_block <br/> 
			^ca_objects.deed_of_gift.deed_of_gift_phone
	</div>	
	<div id='deedofgift_datel'>Date:</div><div id='deedofgift_date'>^ca_objects.deed_of_gift.deed_of_gift_date</div>

	<div id='deedofgift_body'>			
			<div id='legaltext2'> ^ca_objects.deed_of_gift.legal_text</div>
			<div id='deedofgift_descriptionl'><br/>Description of Object(s):<br/></div>
			<div id='deedofgift_description'>
				<span style='font-size:10px; font-style:italic;'>
				&nbsp;&nbsp;&nbsp;&nbsp;<b>Lot Id:</b> ^ca_object_lots &nbsp;&nbsp;&nbsp;&nbsp;
				<b>Accession Id:</b> ^ca_objects.idno<br/>
				 </span>
				 ^ca_objects.deed_of_gift.deed_of_gift_description
			</div>		
			<div id='deedofgift_legalfoot'><br/><br/><br/> ^ca_objects.deed_of_gift.legal_footer</div>
	</div>
	<div id='receptofgift_sigblock'> 
	
  <b>I (we) certify that the object(s) transferred are as described, and that I(we) am(are) authorized to represent the Pelham Historical Society, Inc. in this matter. </b><br/><br/>
	.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature: _________________________________________________  Date: ______________ <br/>
	&nbsp;&nbsp;&nbsp;&nbsp;Printed Name: __________________________________________________<br/><br/>
	
	.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature: _________________________________________________  Date: ______________ <br/>
	&nbsp;&nbsp;&nbsp;&nbsp;Printed Name: __________________________________________________<br/><br/>
	<br/>
	
 <b>I (we) certify that I(we) have Received and Accepted the object(s) as described, in accordance to all terms and conditions set forth and described above. I(we) further certify that I(we) am(are) authorized to represent ^ca_objects.deed_of_gift.deed_of_gift_full_name in this matter:</b><br/><br/>	
	.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature: _________________________________________________  Date: ______________ <br/>
	&nbsp;&nbsp;&nbsp;&nbsp;Printed Name: __________________________________________________<br/><br/>
	
	.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature: _________________________________________________  Date: ______________ <br/>
	&nbsp;&nbsp;&nbsp;&nbsp;Printed Name: __________________________________________________<br/><br/>
	<br/><br/>
	
	</div>		
	
	</div>");
	
		
	
/**************************************************
 *                Style Sheet                     *
 **************************************************/
  $css= "
<style type='text/css'>

/*****************************************/
/***          Deed of Gift             ***/
/*****************************************/
#deedofgift_title{ text-align:center;font-size:24px;
	  position: absolute; top: 30px; left: 330px;
}
#deedofgift_top{text-align:center; width:730px; height:100px;
	  position: absolute; top: 190px; left: 30px;
}
#deedofgift_accessionl{ width:100px; height:30px;
		font-weight:bold;
	  position: absolute; top: 40px; left: 30px;
}
#deedofgift_accession{ width:200px; height:30px;
	  position: absolute; top: 40px; left: 130px;
}

#deedofgift_datel{ font-weight:bold;position: absolute; top: 40px; left: 590px;} 
#deedofgift_date{ position: absolute; top: 40px; left: 630px;} 

#deedofgift_donorl{ width:100px; height:50px; font-weight:bold;  position: absolute; top: 90px; left:30px; } 
#deedofgift_donor{ width:220px; height:50px; position: absolute; top: 90px; left: 130px; }    			

#deedofgift_body{ width:730px; height:380px; position: absolute; top: 250px; left: 30px;}
#legaltext2{}				
#deedofgift_descriptionl{font-weight:bold;}
#deedofgift_description{}	
#deedofgift_legalfoot{height:80px;text-align:center;}	
#deedofgift_sigblock{ width:720px; height:80px; position: absolute; top: 650px; left: 30px; }	

#receptofgift_donorl{ width:150px; height:50px; font-weight:bold;  position: absolute; top: 90px; left:30px; } 
#receptofgift_donor{ width:450px; height:50px; position: absolute; top: 90px; left: 140px;}    
#receptofgift_sigblock{ width:720px; height:80px; position: absolute; top: 550px; left: 30px; }

 
#version       {font-size:6px;position: absolute;
                 top: 950px;left:0px;}	
                 #receptofgift_top{text-align:center; width:730px; height:100px;
	  position: absolute; top: 180px; left: 30px;
}

		
</style>";//style sheet	
	
	
	print $this->render("pdfStart.php");
	echo $css;
	print $this->render("header.php");
	print $this->render("footer.php");
	echo $out;
	print $this->render("pdfEnd.php");
	?>