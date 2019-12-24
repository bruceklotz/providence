<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ca_storage_locations_archive_box_label_summary.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Storage Locations Archive Box Label Summary V1.1.1
 * @type page
 * @pageSize letter
 * @pageOrientation portrait
 * @tables ca_storage_locations
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 * ----------------------------------------------------------------------
 */
	$version = "PHS Storage Locations Archive Box Label Summary V1.1.1"; 
 	$t_item = $this->getVar('t_subject');
	$path = $t_item->getWithTemplate("^ca_storage_locations.idno");  
	$pdfname =  "Archive_Box_Label_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters in the file name
	$this->setVar('filename',$pdfname);
	$this->setVar('showPagenumber',false);
	$this->setVar('hideHeader',true);
  $logo = "/hermes/bosnaweb28a/b1269/ipw.pelhamhs/public_html/images/logos/PHSLogo2017L.jpg";
  
  $locationid = $t_item->get('ca_storage_locations.idno');
  $location_id = $t_item->get('ca_storage_locations.location_id');
  $location = nl2br(htmlspecialchars_decode($t_item->get('ca_storage_locations.preferred_labels.name')));
  $locationh = htmlspecialchars_decode($t_item->get('ca_storage_locations.hierarchy.preferred_labels.name',array('delimiter' => ' > ')));
  $locationd = htmlspecialchars_decode($t_item->get('ca_storage_locations.description'));
  $locationt = $t_item->get('ca_storage_locations.type_id',array('convertCodesToDisplayText' => true));
  
  $path = $t_item->getWithTemplate($root."^ca_storage_locations.idno");
  $vs_path = caGenerateBarcode($locationid, array('checkValues' => $this->opa_check_values, 'type' => 'code128', 'height' => 30));
	
	$root = "http://".$_SERVER["HTTP_HOST"]."/pa2/index.php/Detail/storage_locations/";
	$qr_path = caGenerateBarcode($root.$location_id, array('checkValues' => $this->opa_check_values, 'type' => 'qrcode', 'height' => 1));
	                                     
  $out = " <div class='outsidebox'>
            <div class='logo'><img src = '$logo'></div>
            <div class='name'>$location</div>
            <div id='barcode'><img src='$vs_path.png'/></div> 
	          <div class='id'>$locationid</div>
	          <div id='qr'><img src='$qr_path.png'/>$root.$location_id</div> 
	          <div class='version'>$version</div>";        
            
   $css= "
    <style type='text/css'>
      .outsidebox	{
          height:380px;
          width:280px;
          border:1px solid black;
          padding:1px;      	
      }      
      .logo {
          text-align:center;
          padding-top:4px; 
      }   
      .logo img{
         width:80%;
         height:auto;
         display:block;
      } 
      .name {
        font-family:'Times New Roman';
        font-size:22px;
        font-weight:bold;
        text-align:center;
        margin:10px;
      }  
      .id {
        height:10%;
        font-family:'Times New Roman';
        font-size:16px;
        font-style:italic;
        text-align:center;
        vertical-align:top;
        margin:0px;
      }
      #barcode {
        height:auto;
        font-family:'Times New Roman';
        width:100%;
        text-align:center;
        margin:0px;
        vertical-align:bottom;
      }
      #barcode img{
        width:100%;
      }
      #qr {
        border:1px solid green;
        height:auto;
        font-family:'Times New Roman';
        font-size:7px;
        width:80px;
        text-align:center;
        word-wrap: break-word;
        position:absolute;
        top:385px;
        left: 100px;
      }
      .version{
    	  font-size:6px;
    	  position:absolute;
    	  top:372px;
    	  left:23%;
    	  text-align:center;
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