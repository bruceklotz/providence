<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/summary/local/ca_objects_shelftagsbarcodes_summary.php
 * ----------------------------------------------------------------------
 *
 * To run this report without barcodes (which can stall longer reports) call this report with 'showbarcodes' set to 'no':
 *      $this->setVar('showbarcodes','no');
 *      include($this->getVar('base_path')."/ca_storage_locations_shelftagsbarcodes_summary.php");
 *
 * Report Header
 * Object contained in THIS Storage Location                             ie: object1,object2
 * Storage Locations contained in THIS Storage Location                  ie: Shelf1, Shelf2
 *     Objects contained with Child Storage Location                     ie: object3,object4
 *	  --Storage Locations contained in the Child Storage Location   ie: box1, box2
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Object_Shelf Tags Barcodes Summary V1.0.0
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
  global $rowcount,$rowsperpage,$recursivelimit,$itemcount,$itemspergroup,$showbarcodes, $rowheight, $halfheight;
  $version            = "Object Shelf Tags Barcodes Summary V1.0.0"; 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $showbarcodes       = $this->getVar('showbarcodes'); // Generate barcodes UNLESS showbarcodes = 'no'
  $vo_search          = $this->getVar('search');
  
  if($sq = str_replace(array(' ','*','\''),'',$vo_search)){$sq .="_";}  // Remove illegal characters to add search term to the file name
   $pdfname = "PHS_Object_Shelf_Tags_".$sq.date("M_d_Y").".pdf";
   
  $t_item = $this->getVar('t_subject');
 	$date=date("F j, Y, g:i a");
 	
 	//Build pdf filename (requires modified BaseEditorController.php 
	$path = $t_item->getWithTemplate("^ca_objects.idno");  
	$pdfname =  "PHS_Object_Simple_Label_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
	$title = "Object Label: $path";
  
	 require('../fpdf/multicellmax.php');
	 require('../fpdf/code39.php');
	 require('../fpdf/rotation.php');
	 class yPDF extends PDF_Rotate
   {
   function RotatedText($x,$y,$txt,$angle)
    {
    //Text rotated around its origin
    $this->Rotate($angle,$x,$y);
    $this->Text($x,$y,$txt);
    $this->Rotate(0);
    }

    function RotatedImage($file,$x,$y,$w,$h,$angle)
     {
     //Image rotated around its upper-left corner
     $this->Rotate($angle,$x,$y);
     $this->Image($file,$x,$y,$w,$h);
     $this->Rotate(0);
     }
   }
   class xPDF extends yPDF{
        // Build the page Header:
        function Header(){             	      
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
    
   // Get the current objects's last movement
   $object_id = $t_item->get('ca_objects.object_id'); 
   $the_object = new ca_objects($object_id);
   $the_movement = $the_object->getLastMovement(array('dateElement' => 'removal_date','object_id' =>'$object_id'));
   if( $the_movement != false ){
           $movement_id = $the_movement->get('movement_id');
           $movement_date = $the_movement->get('removal_date');
           //Hack to get the location_id because we need to lookup ca_movements_x_storage_locations by movement_id (and not relationship_id 
           //as New ca_movements_x_storage_locations() requires )
           $o_data = new Db();
           $qr_result = $o_data->query("
               SELECT *
               FROM `ca_movements_x_storage_locations`
               WHERE `movement_id` =".$movement_id);
               while($qr_result->nextRow()) {
                  $location_id = $qr_result->get('location_id');
                  $location_idno = $qr_result->get('idno');
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
   $pdf->AddPage('P');                                  //             img x y w h
   
   $pdf->Image($_ENV["DOCUMENT_ROOT"].'/images/logos/PHS_Letterhead.gif',1,2,65);  
	 $pdf->setXY(1,25);
   
   $vn_line_count = 0;
   $rh=4;
   
        $out="";    
        $objectname = $t_item->get('ca_objects.preferred_labels.name');
        $objectidno = $t_item->get('ca_objects.idno');
        $objectid = $t_item->get('ca_objects.object_id');
        $storage_location_idno = $location_idno;
        $storage_location = $location;
        $type = $t_item->get('ca_objects.type_id',array('convertCodesToDisplayText' => true));
        
        $vs_path = $item['vs_path'];
		    $oneimage = $item['oneimage'];  
             
        $the_object = new ca_objects($t_item->get('ca_objects.object_id'));
        $dateactives = $the_object->get('ca_movements.removal_date',array('returnAsArray' => true, 'sort' => array('ca_movements.removal_date')));
        $dateactive = $dateactives[count($dateactives)-1];
        
                     // Generate Barcode:
                  
		 						
                     $oneimage = $the_object->get('ca_object_representations.media.thumbnail', array('filterNonPrimaryRepresentations' => true ));
              
          
              
                 $oneimagesrc = $item['oneimagesrc'];
                 
                 
           
            //Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])
                 
                 $pdf->SetFont('Arial','',8);
                 $pdf->SetLineWidth(0);
                 $pdf->SetXY(5,30);
                 $pdf->Cell(5,5,' ', 1,0,'',false); // checkbox
                 $pdf->SetLineWidth(30);
             
                 $pdf->SetFont('Arial','',12);
                 
                 $pdf->Rotate(-90,10,50);
                 $pdf->SetXY(10,50);
                 $pdf->Cell(35,4,$objectidno,0,0,'C');
                 $pdf->Rotate(0);
                 
                 
                // $pdf->RotatedText(10,50,$objectidno,-90);
                // $pdf->Cell(35,$rh,$objectidno,0,0,'',$fill);
                 
               //  $pdf->Image($vs_path.png,'','',110,$rh);
                 $pdf->SetFont('Arial','B',10);
                 $pdf->Cell(50,$rh,$location,0,0,'L',0);
                 $pdf->Rotate(-90,40,50);
                  $pdf->Code39(10,$pdf->GetY()+8,$t_item->get('ca_objects.idno'),.75,10);
                 $pdf->Rotate(0);
                  
                 $pdf->SetFont('Arial','',8);
                 $pdf->Ln(4);   
              // $pdf->RotatedImage('circle.png',85,60,40,16,45);
$pdf->RotatedText(100,60,'Hello!',45);
                 $out .= "<table ".$css['itemtable']."><tr ".$css['itemtabletrtd'].">
                            <td ".$css['$td_box']."> <span ".$css['boxi']." >    </span></td>
                            <td ".$css['td_idno'].">$objectidno<br/>
                               <img src='$vs_path.png'/><br/>
                               <span ".$css['casetype'].">$type</span>
                            </td>
                            <td ".$css['td_name'].">$objectname</td>
                            <td ".$css['td_image'].">";
                 if ($oneimagesrc){$out .= "<img src='$oneimagesrc' height='$rowheight' ".$css['td_imageimg']." >&nbsp;</span>";}
               	 $out .= " &nbsp;</td>
                            <td ".$css['td_location'].">$storage_location<br/>
                                <span style='font-size:6px;'> $storage_location_idno <br/><br/><br/><br/>$dateactive</span>
                            </td></tr></table> ";

	
 $css= " 
     .storagepath            {width:100%; text-align:center; font-size:14px;}
     .storagepathdescription {width:100%;	text-align:center; font-size:12px;}
     .itemtable {border-collapse:collapse;height:$rowheight;border:1px solid black;}
     .itemtable tr td {height:$rowheight;}
     .td_box       {width:30px;height:$rowheight;float:left;margin:-10px 0 0 5px}
     .boxi         {border:1px solid black; width:25px; height:25px; margin:5px; }
     .td_idno      {width:165px; font-size:18px;}
     .casetype      {font-size:8px; font-style:italic;}
     .path          {overflow:wrap; break-word; width:$rowheight; height:$rowheight;float:left;border:1px solid red;}
     .td_name      {width:220px;font-size:18px;margin-right:25px;}
     .td_image    { width:110px;}     
     .td_image img    {vertical-align:middle; }               
     .td_location  {width:225px;text-align:right;  font-size:14px;padding-right:25px;}
     .box           {border:1px solid black; width:25px; height:25px; margin:5px; }
     
     .recursivenote {font-size:8px;}
     .casetype      {font:10px; font-style:italic;}
     .casetypebox   {font-weight:extra-bold; font-stretch: expanded; font-size:15px; padding-left:15px;}
     .casetypeshelf {font-weight:extra-bold; font-stretch: expanded; font-size:15px; padding-left:20px; padding-top:15px;}
     .arrow-right { font-weight:bold;font-size:3em;color:green; }
     .arrow-rightx   {width:0; height:0; border-top:8px solid transparent; border-bottom:8px solid transparent; border-left:8px solid green;}
     .arrow-down    {font-weight:bold;font-size:3em;color:red; }
     .arrow-downx    {width:0; height:0; border-left:8px solid transparent; border-right:8px solid transparent; border-top:8px solid #f00; }
     .key            {font-weight:bold;font-size:2em;color:blue;} 
     .building      {font-weight:bold;font-size:3em;color:orange;} 
     .floor      {font-weight:bold;font-size:3em;color:orange;} 
     .room      {font-weight:bold;font-size:3em;color:blue;} 
      .case      {font-weight:bold;font-size:3em;color:red;} 
     .circle        {width:10px; height:10px; background:blue; border-radius:5px;}
     .diamond-shield{width:0; height:0; border-top:8px solid transparent; border-bottom:8px solid transparent; border-left:8px solid green;}
     .diamond-shield::after {width:0; height:0; border-left:8px solid transparent; border-right:8px solid transparent; border-top:8px solid #f00; }
     .pageBreak	      {clear:both;}	
     #version       {font-size:6px; position:absolute; top:950px; left:0px;}	    
";//style sheet
   $pdf->SetFont('Arial','',3);
   
   $pdf->SetFont('Arial','',3);
   $pdf->Cell(65,1,$version,0,1,'L'); 
 	 
 	 $pdf->Output('D',$pdfname);
   exit;  

?>
