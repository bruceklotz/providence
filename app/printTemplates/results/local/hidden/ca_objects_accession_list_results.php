<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ca_objects_accession_list_results.php
 * ----------------------------------------------------------------------
 *
 * Generates a pdf:  Full list of Accessioned Objects and various metadata,
 * sorted by Object_Id
 * REQUIRES fpdf.php Libraries to generate pdfs
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Accession List Results V1.0.0
 * @type page
 * @pageSize letter 
 * @pageOrientation portrait
 * @tables *
 *
 * @marginTop 0.5in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
<Object Name><


 */
  global $vers, $date,$title;
  $vers               = "PHS Accession List Results V1.0.0"; 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $vo_result 	      = $this->getVar('result');
  
   $title="Pelham Historical Accessions List";
  $date=date("F j, Y, g:i a");
 // require('../fpdf/fpdf.php');
  require('../fpdf/multicellmax.php');
 // require('../fpdf/WriteTag.php');
  class xPDF extends PDF{ //  class xPDF extends FPDF{
        // Build the page Header:
        function Header(){
           Global $rh, $rl, $title, $date, $vers;
           $this->Image($_ENV["DOCUMENT_ROOT"].'/images/logos/PHS_Letterhead.gif',10,2,30);  
	         $this->SetTitle($title." - ".$date);
	         $this->SetFont('Arial','B',12);
	         $this->Cell($rl,4,$title,0,0,C);
	         $this->Ln();
	         $this->SetFont('Arial','B',8);
	         $this->Cell($rl,3,$date,0,0,C);
           $this->Ln();
    
           $this->SetFont('Arial','B',8);
           $this->Cell(50,2,'',0,0,C);
	         //$this->Cell(50,2,$vers,0,0,C);
	         //$this->SetFont('Arial','B',12);
	         //$this->Cell(100,2,$title." - ".$date,0,0,C);
	         $this->Ln();
	         $this->SetFont('Arial','B',8);
	        // $this->Cell(4);
	         $this->Cell(25,7,"AccessionId");
	         $this->Cell(35,7,"Name / Lexicon");
	         $this->Cell(60,7,"Description");
	         $this->Cell(35,7,"Donor / Donation Date");
	         $this->Cell(35,7,"Last Location / Location Date");
	         $this->Ln();
	         $this->SetLineWidth(0);
	         $this->Cell($rl,1,"",1);
	         $this->Ln(4);
       }
       function Footer(){
       	   Global $vers;
           // Position at 1.5 cm from bottom
           $this->SetY(-10);
           // Arial italic 8
           $this->SetFont('Arial','I',8);
           $this->Cell(50,10,$vers,0,0,C);
           // Page number
           $this->Cell(0,10,' Page '.$this->PageNo().'/{nb}',0,0,'C');
       }
  }

 //***** Begin Here ****
     $pdf=new xPDF();
     $pdf->SetTopMargin(2);
     $pdf->AliasNbPages();
     
     // Stylesheet
/*$pdf->SetStyle("b","courier","B",12,"10,100,250",15);
$pdf->SetStyle("p","courier","N",12,"10,100,250",15);
$pdf->SetStyle("h1","times","N",18,"102,0,102",0);
$pdf->SetStyle("a","times","BU",9,"0,0,255");
$pdf->SetStyle("pers","times","I",0,"255,0,0");
$pdf->SetStyle("place","arial","U",0,"153,0,0");
$pdf->SetStyle("vb","times","B",0,"102,153,153");
   */  
     
     
     $pdf->SetTitle($title." - ".$date);
     $pdf->AddPage('P');
     $pdf->SetFont('Arial','',10);
     $pdf->SetAutoPageBreak(1,10);
     $vo_result->seek(0);
     $vn_line_count = 0;
     $rh=7;//20
     $lh = 6;

     while( $vo_result->nextHit()){
          $object_id = $vo_result->get('ca_objects.object_id');
          $the_object = new ca_objects($object_id);
          
          if(!$the_object->get('ca_objects.is_deaccessioned')){// We only want to deal with non-deaccessioned objects...
            if($vn_line_count++ > 21){$vn_line_count=1;	$pdf->AddPage('P');}
           
            $idno = htmlspecialchars_decode($vo_result->get('ca_objects.idno')); 
            $name = htmlspecialchars_decode($vo_result->get('ca_objects.preferred_labels.name'));
            
// Get the current objects's last movement
            if( $the_movement = $the_object->getLastMovement(array('dateElement' => 'removal_date','object_id' =>'$object_id'))){
                $movement_id = $the_movement->get('movement_id');
                if(!$movement_date = $the_movement->get('effective_date')){$movement_date="";}
                
                //Hack to get the location_id because we need to lookup ca_movements_x_storage_locations by movement_id (and not relationship_id as New ca_movements_x_storage_locations() requires )
                $o_data = new Db();
                $qr_result = $o_data->query("
                     SELECT *
                     FROM `ca_movements_x_storage_locations`
                     WHERE `movement_id` =".$movement_id);
                while($qr_result->nextRow()) {
                     $location_id = $qr_result->get('location_id');
                    
                }
           
                $this_location = new ca_storage_locations($location_id);
                $locationh = htmlspecialchars_decode($this_location->get('ca_storage_locations.hierarchy.preferred_labels.name',array('delimiter' => ' > ')));
                $location = htmlspecialchars_decode($this_location->get('ca_storage_locations.preferred_labels.name'));
            }else{$location="";$locationh="";$movement_date="";}
           
                   
           //$pdf->MultiCell(0,$rh,$locationh,0,'L',$fill);
           $description = htmlspecialchars_decode($vo_result->get('ca_objects.description'))."\n\n\n\n";
           $lexicon3Top = htmlspecialchars_decode($vo_result->getWithTemplate('^ca_objects.lexicon3.hierarchy.preferred_labels',array('maxLevelsFromBottom'=>1)));
           $lexicon3 = htmlspecialchars_decode($vo_result->get('ca_objects.lexicon3',array('convertCodesToDisplayText' => true)));
           $donor = htmlspecialchars_decode($vo_result->getWithTemplate("<unit relativeTo='ca_entities' delimiter=', ' restrictToRelationshipTypes='donor'>^ca_entities.preferred_labels.displayname</unit>"));
          
           $date_array	= $vo_result->get('ca_objects.date', array('convertCodesToDisplayText' => true,'returnWithStructure' => true));
           foreach($date_array as $key=>$dateitems){
             foreach($dateitems as $ikey=>$dateitem){
               if( strcasecmp($dateitem['dc_dates_types'],"Date accepted") ==0 ){$datereceived = $dateitem['dates_value'];}else{$datereceived="";}
             }//foreach
           }//foreach
           $datereceived = htmlspecialchars_decode($vo_result->get('ca_objects.date.dates_value' ,array('convertCodesToDisplayText' => true)));
           $datereceivedtype = htmlspecialchars_decode($vo_result->get('ca_objects.date.dc_dates_types' ,array('convertCodesToDisplayText' => true)));
           $datereceived = str_replace("–","-",$datereceived);
          
           $pdf->SetFillColor(210);
           $pdf->SetLineWidth($rh);
           if ($vn_line_count % 2 == 0){$fill=true;}else{$fill=false;}
           
           $pdf->SetFont('Arial','',8);
           $pdf->SetLineWidth(0);
           
           //$pdf->Cell(5,4,' ', 1,0,'',false); // checkbox
           $pdf->SetLineWidth($rh);
         
           $x = $pdf->GetX();
           $y = $pdf->GetY();
           $pdf->SetFont('Arial','',8);
           $pdf->MultiCell(25,$lh,$idno."\n\n\n",0,'L',$fill,2);
           //$pdf->Cell(25,$rh,$idno,0,0,'',$fill);
            
           //$x = $pdf->GetX();
           //$y = $pdf->GetY();
           $pdf->SetXY($x+25, $y);
           $pdf->SetFont('Arial','',8);
           $pdf->MultiCell(35,$lh,$name."\n\n\n",0,'L',$fill,1);
          // $pdf->Cell(35,$rh,$name,0,2,'L',$fill);
        
           $pdf->SetXY($x+25, $y+4);
           $pdf->SetFont('Arial','',6);
           $pdf->Cell(35,4,$lexicon3Top,0,0,'L',$fill);
           $pdf->SetFont('Arial','B',6);
           $pdf->SetXY($x+25, $y+7);
           $pdf->Cell(35,5,$lexicon3,0,0,'L',$fill);
           $x = $pdf->GetX();
           $pdf->SetXY($x, $y);
           
          // $x = $pdf->GetX();
         //  $y = $pdf->GetY();
          // $pdf->SetXY($x, $y);
           
           $pdf->SetFont('Arial','',7);
           $pdf->SetcMargin(0);
           $pdf->MultiCell(60,$lh,$description,0,'J',$fill,2);
          
           $pdf->SetXY($x + 60, $y);
          
           $x = $pdf->GetX();
           $y = $pdf->GetY();
           $pdf->MultiCell(35,$lh,"  ".$donor."\n\n\n",0,'L',$fill,2);
          // $pdf->Cell(35,$rh,$donor,0,'','L',$fill);
           $pdf->SetXY($x, $y+5);
           $pdf->MultiCell(35,$lh,"  ".$datereceived."\n\n\n",0,'L',$fill,1);
           //$pdf->Cell(35,4,$datereceived,0,0,'L',$fill);
           $pdf->SetXY($x+35, $y);
           
            //$pdf->MultiCell(35,$lh,$notes."\n\n\n",0,'J',$fill,0);
           $movement_date .= " ".$pdf->GetcMargin;
           $pdf->Cell(35,$lh,$location."\n\n\n",0,0,'L',$fill);
           $pdf->SetXY($x+35, $y+5);
           $pdf->Cell(35,7,$movement_date."\n\n\n",0,0,'L',$fill);
           $pdf->Ln($rh); 
                  
      }//if!deaccessioned 
    }//while
// exit; 
  $pdf->Output();
  exit;        
  

?>