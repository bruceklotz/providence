<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ca_objects_label_summery.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Objects Label V1.0.1
 * @type page
 * @pageSize letter
 * @pageOrientation portrait
 * @tables ca_objects, ca_storage_locations
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
	$version = "PHS Objects Label V1.0.1 Summary"; 
 	$t_item = $this->getVar('t_subject');
 	$date=date("F j, Y, g:i a");
 	
 	//Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_objects.idno");  
	$pdfname =  "PHS_Object_Simple_Label_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$title = "Object Label: $path";
  
	 require('../fpdf/multicellmax.php');
	 require('../fpdf/code39.php');
   class xPDF extends PDF_Code39{
        // Build the page Header:
        function Header(){   }
        function Footer(){   }
   }
//***** Begin Here ****
    
   // Get the current objects's last movement
   $object_id = $t_item->get('ca_objects.object_id'); 
   $the_object = new ca_objects($object_id);
   $the_movement = $the_object->getLastMovement(array('dateElement' => 'removal_date','object_id' =>'$object_id'));
   if( $the_movement != false ){
           $movement_id = $the_movement->get('movement_id');
           $movement_date = $the_movement->get('ca_movements.removal_date'); //echo  "<pre>".var_dump($the_movement)."</pre>";exit;
          
           //Hack to get the location_id because we need to lookup ca_movements_x_storage_locations by movement_id (and not relationship_id 
           //as New ca_movements_x_storage_locations() requires )
           $o_data = new Db();
           $qr_result = $o_data->query("
               SELECT *
               FROM `ca_movements_x_storage_locations`
               WHERE `movement_id` =".$movement_id);
               while($qr_result->nextRow()) {
                  $location_id = $qr_result->get('location_id');
                  $location_idno = $qr_result->get('idno');//
               }
           
               $this_location = new ca_storage_locations($location_id);
               $locationh = htmlspecialchars_decode($this_location->get('ca_storage_locations.hierarchy.preferred_labels.name',array('delimiter' => ' > ')));
               $location = htmlspecialchars_decode($this_location->get('ca_storage_locations.preferred_labels.name'));
               $location_idno = $this_location->get('idno');
   }else{$location="";$locationh="";$location_idno="";}
             
    
   $pdf=new xPDF();
   $pdf->SetTopMargin(2);
   $pdf->AliasNbPages(); 
   $pdf->SetAutoPageBreak(1,10);
   $pdf->AddPage('P');
   $pdf->SetXY(1,1); 
   $pdf->Cell(66,130,'',1,0); // Outer Box around Label
                                       //             img x y w h
   $pdf->Image($_ENV["DOCUMENT_ROOT"].'/images/logos/PHS_Letterhead.gif',2,2,63);  
	 $pdf->setXY(1,25);
        
   $pdf->SetFont('Arial','B',25);
   //         w  h                                            txt        bdr ln aln fill
   $pdf->Cell(65,8,htmlspecialchars_decode($t_item->get('ca_objects.idno')),0,1,'C');
   
   $pdf->SetFont('Arial','',20);
   $pdf->SetX(1);
   $pdf->MultiCell(65,8,htmlspecialchars_decode($t_item->get('ca_objects.preferred_labels.name')),0,'C');
   $pdf->SetFont('Arial','I',8);
   $pdf->SetXY(1,50);
   $pdf->Cell(65,8,$t_item->get('ca_objects.type_id',array('convertCodesToDisplayText' => true)),0,1,'C');
   
   //           x  y                                 code,      baseline height
   $pdf->Code39(10,60,$t_item->get('ca_objects.idno'),.75,10);

   if($img = $t_item->get('ca_object_representations.media.thumbnail.url')){
     //Image(string file [, float x [, float y [, float w [, float h [, string type [, mixed link]]]]]])
     $pdf->Image($img,15,75,40);  
   }
     
   $pdf->SetXY(1,114);
   $pdf->SetFont('Arial','',6);
   $pdf->MultiCell(65,3,"Location as of $movement_date:  $location_idno",0,'C');
     
   $pdf->SetFont('Arial','B',7);
   //MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false, $maxline=0)
   $pdf->MultiCell(50,2,$location,0,'C');
  
   $pdf->SetFont('Arial','',5);
   if( $locationh ){
      $pdf->MultiCell(50,2,"( ".$locationh." )",0,'C');
   }
  
   $pdf->SetFont('Arial','',3);
   $pdf->Cell(65,1,$version,0,1,'L'); 
 	
 	 $pdf->Output('D',$pdfname);
   exit;     
	?>