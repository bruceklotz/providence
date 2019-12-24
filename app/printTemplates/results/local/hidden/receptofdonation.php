<?php
/* ----------------------------------------------------------------------
 *app/printTemplates/results/local/receptoftransfer.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Recept of Donation V1.0.1 Results
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
	$version = "PHS Recept of Transfer V1.0.1 Results"; 
 	$t_item 				= $this->getVar('t_subject');
	$va_bundle_displays 	= $this->getVar('bundle_displays');
	$t_display 				= $this->getVar('t_display');
	$va_placements 			= $this->getVar("placements");
	//$this->setVar('headerTitle','Recept of Transfer');
	
	
 
 	$vo_result = $this->getVar('result');	
 	$vo_result->seek(0);
	$vo_result->nextHit();  //while.....

	$out = $vo_result->getWithTemplate("<div class='deedofgift'><div id='version'>$version</div>");
	$root = 'http://pelhamhs.org/ca/index.php/editor/objects/ObjectEditor/Edit/object_id/';
	$path = $vo_result->getWithTemplate($root."^ca_objects.object_id");
	$vs_path = caGenerateBarcode($path, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 3));
		
	$out .= $vo_result->getWithTemplate("
	<div id='deedofgift_title'>
			Recept of Transfer
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
	
	print $this->render("pdfStart.php");
	print $this->render("header.php");
	print $this->render("footer.php");
	echo $out;
	print $this->render("pdfEnd.php");
	?>