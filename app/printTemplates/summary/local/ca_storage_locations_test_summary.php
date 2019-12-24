<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/summary/local/ca_storage_locations_test_summary.php
 * ----------------------------------------------------------------------
 *

 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Storage Location Font Test V1.0.2
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
  global $rowcount,$rowheight, $rowsperpage,$recursivelimit,$itemcount,$itemspergroup,$showbarcodes;
  $version            = "PHS Storage Location Font Test V1.0.2"; 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $showbarcodes       = $this->getVar('showbarcodes'); // Generate barcodes UNLESS showbarcodes = 'no'
  
  //Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_storage_locations.location_id");  
	$sq =  "PHS_Storage_Locations_Shelf_Tag_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$this->setVar('filename',$sq);
   
  
  
  $mpdf =  new \Mpdf\Mpdf(['orientation' => 'P']);
  $mpdf->SetColumns(8);
  $mpdf->SetDisplayMode('fullpage');
  $pdfout ="<hr/>";
    for( $i = 0000; $i<=10000; $i++ ) {//65536
            $pdfout .= " $i : &#$i;<hr/>"; 
    }
 
  $mpdf->WriteHTML($pdfout);
  $mpdf->Output();
  exit;
  
  
  
  
  // exit;
  echo $pdfout;
 exit;  
 
?>