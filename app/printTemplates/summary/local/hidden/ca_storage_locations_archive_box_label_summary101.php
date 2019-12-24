<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ca_storage_locations_archive_box_label_summary.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Storage Locations Archive Box Label Summary V1.0.1
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
	$version = "PHS Storage Locations Archive Box Label Summary V1.0.1"; 
 	$t_item = $this->getVar('t_subject');
	$path = $t_item->getWithTemplate("^ca_storage_locations.idno");  
	$pdfname =  "Archive_Box_Label_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters in the file name
	$this->setVar('showPagenumber',false);

 require('../fpdf/multicellmax.php');
	 require('../fpdf/code39.php');
   class xPDF extends PDF_Code39{
        // Build the page Header:
        function Header(){   }
        function Footer(){   }
   }
 //***** Begin Here ****
    $pdf=new xPDF();
   $pdf->SetTopMargin(2);
   $pdf->AliasNbPages(); 
   $pdf->SetAutoPageBreak(1,10);
   $pdf->AddPage('P');
   $pdf->SetXY(1,1); 
   $pdf->Cell(66,130,'',1,0); // Outer Box around Label
  
     $locationid = $t_item->get('ca_storage_locations.idno');
     $location = htmlspecialchars_decode($t_item->get('ca_storage_locations.preferred_labels.name'));
     $locationh = htmlspecialchars_decode($t_item->get('ca_storage_locations.hierarchy.preferred_labels.name',array('delimiter' => ' > ')));
     $locationd = htmlspecialchars_decode($t_item->get('ca_storage_locations.description'));
     $locationt = $t_item->get('ca_storage_locations.type_id',array('convertCodesToDisplayText' => true));
     $path = $t_item->getWithTemplate($root."^ca_storage_locations.idno");
	 //  $vs_path = caGenerateBarcode($path, array('checkValues' => $t_item->opa_check_values, 'type' => 'code128', 'height' => 3)).".png";
	                                     //             img x y w h
   $pdf->Image($_ENV["DOCUMENT_ROOT"].'/images/logos/PHS_Letterhead.gif',2,2,63);  
	 $pdf->setXY(1,25);
        
   $pdf->SetFont('Arial','B',25);
   //              w  h   txt   bdr ln aln fill
   $pdf->MultiCell(65,8,$location,0,'C');
   
   $pdf->SetFont('Arial','',15);
   $pdf->SetX(1);
   $pdf->Cell(65,8,$locationid,0,1,'C');
   $pdf->SetFont('Arial','I',15);
   $pdf->SetX(1);
  // $pdf->SetXY(1,50);
   $pdf->Cell(65,8,$locationt,0,1,'C');
   $pdf->SetX(1);
   $pdf->MultiCell(65,8,$location,0,'C');
   error_log("ca_storage_....  $locationid");
   //           x  y    code, baseline height
   $pdf->Code39(10,60,$locationid,.75,10);
$pdf->Output('D',$pdfname);
exit;
   if($img = $t_item->get('ca_object_representations.media.thumbnail.url')){
     //Image(string file [, float x [, float y [, float w [, float h [, string type [, mixed link]]]]]])
     $pdf->Image($img,15,75,40);  
   }
   
   //  $pdf->SetFont('Arial','',19);
   //  $pdf->MultiCell(80,7,$location,0,'C','');
   //  $pdf->Ln(2); 
   //  $pdf->SetFont('Arial','',10);
   //  $pdf->MultiCell(75,4,$locationd,0,'C','');       
   //  $pdf->Ln(4); 
   //  $x = $pdf->getX();
   //  $y = $pdf->getY();
   //  $pdf->Image($vs_path,10,null,75,25); 
   //  //$pdf->Ln(1); 
   //  $pdf->setXY($x,$y+11);
   //  $pdf->SetFont('Arial','',7);
   //  $pdf->MultiCell(75,4,$path,0,'C',''); 
   //  $pdf->Ln(1); 
     $pdf->SetFont('Arial','',2);
     $pdf->MultiCell(95,2,$version,0,'C',''); 
         
    
     $pdf->Output('D',$pdfname);
exit;
?>