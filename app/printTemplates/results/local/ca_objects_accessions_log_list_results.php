<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ca_objects_accessions_log_list_results.php
 * ----------------------------------------------------------------------
 *
 * Generates a pdf:  Full list of Accessioned Objects and their current Storage Locations,
 * sorted by Object_Id
 *
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Accessions Log List Results V1.1.2
 * @type page
 * @pageSize letter 
 * @pageOrientation portrait
 * @tables ca_objects
 *
 * @marginTop 0.5in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
  global $vers, $date,$title;
  $vers               = "PHS Accessions Log List Results V1.1.2"; 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $vo_result 	        = $this->getVar('result');
  $vo_search          = $this->getVar('search');
  $date=date("F j, Y, g:i a");
  if($sq = str_replace(array(' ','*','\''),'',$vo_search)){$sq .="_";}  // Remove illegal characters to add search term to the file name
  $title="Pelham Historical Accessions: Objects & Storage Locations ($vo_search)";
  
  $pdfname = "PHS_Accession_List_".$sq.date("M_d_Y").".pdf";
  
  //require('../fpdf/fpdf.php');
  require('../fpdf/multicellmax.php');
  class xPDF extends PDF{
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
	         $this->SetFont('Arial','B',10);
	         $this->Cell(4);
	         $this->Cell(35,7,"AccessionId");
	         $this->Cell(7,7,"Cnt");
	         $this->Cell(75,7,"Name");
	         $this->Cell(0,7,"Current Location");
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
     $pdf->SetTitle($title." - ".$date);
     $pdf->AddPage('L');
     $pdf->SetFont('Arial','',10);
     $pdf->SetAutoPageBreak(1,10);
     $vo_result->seek(0);
     $vn_line_count = 0;
     $rh=4;

     while( $vo_result->nextHit()){
          $object_id = $vo_result->get('ca_objects.object_id');
          $the_object = new ca_objects($object_id);
          
          if(!$the_object->get('ca_objects.is_deaccessioned')){// We only want to deal with non-deaccessioned objects...
            $vn_line_count++;	
            $pdf->SetFillColor(210);
           
            $pdf->SetLineWidth($rh);
            if ($vn_line_count % 2 == 0){$fill=true;}else{$fill=false;}
            $pdf->SetFont('Arial','',8);
            $pdf->SetLineWidth(0);
            $pdf->Cell(5,$rh,' ', 1,0,'',false); // checkbox
             $pdf->SetLineWidth($rh);
             
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(35,$rh,htmlspecialchars_decode($vo_result->get('ca_objects.idno')),0,0,'',$fill);
            
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(7,$rh,$vn_line_count,0,0,'',$fill);  // running count
            
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(75,$rh,htmlspecialchars_decode($vo_result->get('ca_objects.preferred_labels.name')),0,'L','',$fill);
            
            
            // Get the current objects's history based on last movement
            if( $the_movements = $the_object->getHistory(array('dateElement' => 'removal_date','object_id' =>'$object_id','currentOnly' => true))){
            	  // step through the returned array looking for CURRENT
            	  foreach($the_movements as $key=>$the_movement){
                   if($the_movement[0]['status'] == "CURRENT"){ $movement_id = $the_movement[0]['id'];}
                }   
                //Hack to get the location_id because we need to lookup ca_movements_x_storage_locations by movement_id (and not relationship_id 
                //as New ca_movements_x_storage_locations() requires )
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
            }else{$location="";$locationh="";}
           
           // save our location so we can reset it if the location name is longer than one row of text
           // because MultiCell leases the pointer at the end of the cell   
           $y = $pdf->getY();
           $x = $pdf->getX();
           $pdf->SetFont('Arial','B',10);
           $pdf->MultiCell(50,$rh,$location,0,'L',$fill);
           // move to just after the MultiCell:
           $pdf->setXY($x+50,$y);
           $pdf->SetFont('Arial','',8);
           $pdf->MultiCell(0,$rh,$locationh,0,'L',$fill);
           $pdf->Ln(4);          
      }//if!deaccessioned 
    }//while
  
  $pdf->Output('D',$pdfname);
  exit;        
?>