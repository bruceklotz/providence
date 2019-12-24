<?php 
/* ----------------------------------------------------------------------
 * app/printTemplates/summary/local/ca_storage_locations_shelftagsbarcodes2_summary.php
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
 * @name PHS Shelf Tags Barcodes Summary V1.2.1
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
  global $rowcount,$rowsperpage,$recursivelimit,$itemcount,$itemspergroup,$showbarcodes, $rowheight, $halfheight,$rh,$pdf, $css;
  $version            = "PHS Shelf Tags Barcodes Summary V1.2.1"; 
  $t_item             = $this->getVar('t_subject');
  $va_bundle_displays = $this->getVar('bundle_displays');
  $t_display          = $this->getVar('t_display');
  $va_placements      = $this->getVar("placements");
  $showbarcodes       = $this->getVar('showbarcodes'); // Generate barcodes UNLESS showbarcodes = 'no'
   
  $rowcount=1;
  $rowsperpage=1000;
  $rowheight = "60px";
  $halfheight = "30px";
  //$itemcount=1;
  //$itemspergroup=19;
  $recursivelimit=4;	
  $recursive = $recursivelimit;	
  $path = $t_item->getWithTemplate("^ca_storage_locations.idno"); 
  $pdfname =  "PHS_Shelf_Tags_Barcodes_Summary_".str_replace(array(' ','*','\''),'',$path).".pdf";// Remove illegal characters to add search term to the file name
  if ($showbarcodes =="no"){$version .= " - No Barcodes";}
 // $this->setVar('reportversion',$version);
  $this->setVar('version',$version);
  $this->setVar('headerTitle',$t_item->getWithTemplate("^ca_storage_locations.preferred_labels")."<div class='recursivenote'>Recursive Limit: $recursivelimit</div>");
  $this->setVar('showPagenumber',1);


  $title="Pelham Historical Accessions: Objects & Storage Locations";
  $date=date("F j, Y, g:i a");
  require('../fpdf/fpdf.php');
  require('../fpdf/WriteTag.php');
  //require('../fpdf/html_table.php');
  class xPDF extends PDF_WriteTag{  //class PDF extends FPDF{
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










  /*******************************************\
 *               Functions                     *
  \*******************************************/
  if (!function_exists('makeListofObjectsInThisStorageLocation')){
    function makeListofObjectsInThisStorageLocation($this_location){
        global $rowcount,$rowsperpage,$showbarcodes, $rowheight, $halfheight,$rh,$pdf,$css;//$itemcount,$itemspergroup;
        $parent_locationid = $this_location->get('ca_storage_locations.location_id');
        $out="";
       
        if($this_contents = $this_location->getLocationContents('ca_movements'))
                {
            	//In order to sort, we first load an array with all the data, then sort, then display...
                $objectsSorted =[];
                while( $this_contents->nextHit() != NULL ) {
                     $objectindex =$this_contents->get('ca_objects.idno');
                     $objectsSorted[$objectindex]['objectname'] = $this_contents->get('ca_objects.preferred_labels.name');
                     $objectsSorted[$objectindex]['objectidno'] = $this_contents->get('ca_objects.idno');
                     $objectsSorted[$objectindex]['objectid'] = $this_contents->get('ca_objects.object_id');
                     $objectsSorted[$objectindex]['storage_location_idno'] = $this_location->get('ca_storage_locations.idno'); 
                     $objectsSorted[$objectindex]['storage_location'] = $this_location->get('ca_storage_locations.preferred_labels');
                     $objectsSorted[$objectindex]['type'] = $this_contents->get('ca_objects.type_id',array('convertCodesToDisplayText' => true));
                 
                     $the_object = new ca_objects($objectsSorted[$objectindex]['objectid']);
                     
                     $dateactives = $the_object->get('ca_movements.removal_date',array('returnAsArray' => true, 'sort' => array('ca_movements.removal_date')));
                     $objectsSorted[$objectindex]['dateactive'] = $dateactives[count($dateactives)-1];
                 
                     // Generate Barcode:
                     if ($showbarcodes !="no"){
                         $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/objects/ObjectEditor/Edit/object_id/";
                         //$path =$root.$objectsSorted[$objectindex]['objectid'];
                          $path =$objectsSorted[$objectindex]['objectid'];
                         $objectsSorted[$objectindex]['vs_path'] = caGenerateBarcode($path, array( 'checkValues' => $this_contents->opa_check_values,'type' => 'code128', 'height' => $halfheight));
		     }//if $showbarcodes
		 						
                     $objectsSorted[$objectindex]['oneimage'] = $the_object->get('ca_object_representations.media.thumbnail', array('filterNonPrimaryRepresentations' => true ));
                     // get the src for that image
                     preg_match( "@src='([^']+)'@" , $objectsSorted[$objectindex]['oneimage'], $matches);
                     $objectsSorted[$objectindex]['oneimagesrc'] = array_pop($matches);
                   
              }//while ...nextHit()
              
              //Now sort
              ksort( $objectsSorted);
             
              //Now retrieve and display...
              foreach ( $objectsSorted as $objectindex => $item){
                 $rowcount++;            
                 $objectname = $item['objectname'];
                 $objectidno = $item['objectidno'];
                 $objectid = $item['objectid'];
                 $storage_location_idno = $item['storage_location_idno'];
                 $storage_location = $item['storage_location'];
                 $type = $item['type'];
                 $dateactive = $item['dateactive'];
                 $vs_path = $item['vs_path'];
		             $oneimage = $item['oneimage'];
		             $oneimagesrc = $item['oneimagesrc'];
                 
                 $pdf->SetFont('Arial','',8);
                 $pdf->SetLineWidth(0);
                 $pdf->Cell(5,$rh,' ', 1,0,'',false); // checkbox
                 $pdf->SetLineWidth($rh);
             
                 $pdf->SetFont('Arial','',12);
                 $pdf->Cell(35,$rh,$objectidno,0,0,'',$fill);
                 
               //  $pdf->Image($vs_path.png,'','',110,$rh);
                 $pdf->SetFont('Arial','B',10);
                 $pdf->Cell(50,$rh,$location,0,0,'L',0);
                 $pdf->SetFont('Arial','',8);
                 $pdf->Ln(4);   
               
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
                   //$pdf->WriteHTML($out);
                     
                 if($rowcount > $rowsperpage){ //check to see if we need a new page?
                     $rowcount =1;
                     //$itemcount =1;
                     $out .= "<div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><br/>";
                 }
                 // if ($itemcount > $itemspergroup){$itemcount=1; $out .= "</table><hr style='height:10px;color:green;'/><table class='tag_table'>";}
            
              }//foreach $objectsSorted
        }// if $this_contents = $this_location->getLocationContents('ca_movements'))...
        return $out;
    }//end function	
  }//if function exists
/***************************************************/
 
  if (!function_exists('makeListofStorageInThisStorageLocation')){
    function makeListofStorageInThisStorageLocation($this_location,$recursive){
        global $rowcount,$rowsperpage,$recursivelimit,$showbarcodes,$rowheight,$halfheight,$rh, $pdf,$css;//$itemcount,$itemspergroup;
        $out="";
        
        // Now get all children storage_locations within THIS Storage_Location
        $this_storage =$this_location->get('ca_storage_locations.preferred_labels',array('convertCodesToDisplayText' => true));
        $this_storageid =$this_location->get('ca_storage_locations.idno',array('convertCodesToDisplayText' => true));
        $va_storage_children = $this_location->get('ca_storage_locations.children.location_id', array('returnAsArray' => true));
      
        foreach ($va_storage_children as $va_key => $va_storage_children_id) {
            $t_storage_location = new ca_storage_locations($va_storage_children_id);
	    
            #information about this (child) storage location
            $storage_locationsidno = $t_storage_location->get('ca_storage_locations.idno');
            $storage_locationname =$t_storage_location->get('ca_storage_locations.preferred_labels');
            $vs_storage_type = $t_storage_location->get('ca_storage_locations.type_id', array('convertCodesToDisplayText' => true));
            
            $storagetypehtml="<span class='casetype'>$vs_storage_type</span>"; //default casetype style
            if ($vs_storage_type == "building") {$storagetypehtml="<span class='building'>&#x27F0;</span><span class='casetypebox'>$vs_storage_type</span>";}
            if ($vs_storage_type == "floor") {$storagetypehtml="<span class='floor'>&#187;</span><span class='casetypebox'>$vs_storage_type</span>";}
            if ($vs_storage_type == "room") {$storagetypehtml="<span class='room'>&#10228;</span><span class='casetypebox'>$vs_storage_type</span>";}
            if ($vs_storage_type == "case / display") {$storagetypehtml="<span class='case'> &#187;</span><span class='casetypebox'>$vs_storage_type</span>";}
            if ($vs_storage_type == "box") {$storagetypehtml="<span class='arrow-right'>&#9660;</span><span class='casetypebox'>$vs_storage_type</span>";}
            if ($vs_storage_type == "shelf") {$storagetypehtml = "<span class='arrow-down'>&#9658;</span><span class='casetypeshelf'> $vs_storage_type</span>";}
            if ($vs_storage_type == "key") {$storagetypehtml = "<span class='key'>&#8691;&nbsp;</span><span class='casetypeshelf'>$vs_storage_type</span>";}
	    
	          //$dateactives = $t_storage_location->get('ca_objects_x_storage_locations.effective_date',array('returnAsArray' => true)); 
            //$dateactive = $dateactives[$va_key];//error_log("L174 ".print_r($dateactives,1));
                	
            // Generate Barcode:	editor/storage_locations/StorageLocationEditor/
            if($showbarcodes !="no"){
               // $root = "http://".$_SERVER["HTTP_HOST"].__CA_URL_ROOT__."/index.php/editor/storage_locations/StorageLocationEditor/Edit/location_id/";
                $path =$t_storage_location->get('ca_storage_locations.location_id');
                $vs_path = caGenerateBarcode($path, array( 'checkValues' => $t_storage_location->opa_check_values,'type' => 'code128', 'height' => $halfheight));
            }//$showbarcodes
            			
            // Hack because $t_storage_location->get('ca_object_representations.related.media.thumbnail', array('filterNonPrimaryRepresentations' => true )) doesn't work.
            $oneimage =$t_storage_location->get('ca_object_representations.media.thumbnail', array('returnAsArray' => true ));
            // get the src for that image
            preg_match( "@src='([^']+)'@" , $objectsSorted[$objectindex]['oneimage'], $matches);
            $oneimagesrc = array_pop($matches);
            
				    $rowcount++;            
            $t_storage_location = new ca_storage_locations($va_storage_children_id);
			      
			      $pdf->SetFont('Arial','',8);
                 $pdf->SetLineWidth(0);
                 $pdf->Cell(5,$rh,' ', 1,0,'',false); // checkbox
                 $pdf->SetLineWidth($rh);
             
                 $pdf->SetFont('Arial','',12);
                 $pdf->Cell(35,$rh,$storage_locationsidno,0,0,'',false);
                 
                $pdf->Image($vs_path.png,'','',110,$rh);
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(50,$rh,$location,0,0,'L',0);
                $pdf->SetFont('Arial','',8);
                $pdf->Ln(4); 
			      
			      $out .="<table ".$css['itemtable']." ><tr>
                            <td ".$css['td_box']."> <span ".$css['boxi']." >    </span></td>
                            <td ".$css['td_idno'].">$storage_locationsidno<br/>
                               <img src='$vs_path.png'/><br/>
                               <span ". $css['casetype'].">$storagetypehtml</span>
                            </td>
                            <td style='width:220px;font-size:18px;margin-right:25px;'>$storage_locationname</td>
                            <td style='width:110px;'>";
                 if ($oneimagesrc){$out .= "<img src='$oneimagesrc' height='$rowheight'style='vertical-align:middle;' >&nbsp;</span>";}
               	 $out .= " &nbsp;</td>
                            <td style='width:225px;text-align:right;  font-size:14px;padding-right:25px;'>".print_r($this_storage,1)."<br/>
                                <span style='font-size:6px;'> $storage_location_idno <br/><br/><br/><br/>$dateactive</span>
                            </td></tr></table> ";
            //   $pdf->WriteHTML($out);             
              //     $pdf->Ln(4);   
            if($rowcount > $rowsperpage){
                 $rowcount =1;
                // $itemcount =1;
                 $out .= "<div class='pageBreak' style='page-break-before: always;'>&nbsp;</div><br/><br/>";
            }
            
            //if ($itemcount > $itemspergroup){$itemcount=1;$out .= "</table><hr style='height:10px;color:green;'/><table class='tag_table'>";}
            
            $out .= makeListofObjectsInThisStorageLocation($t_storage_location);
            if($recursive > 0){$recursive--; $out .= makeListofStorageInThisStorageLocation($t_storage_location,$recursive);}else{$recursive=$recursivelimit;}
           
          }//foreach $va_storage_children
        return $out;
    }//function getListofStorageInThisStorageLocation
  }//function_exists getListofStorageInThisStorageLocation
/***************************************************/

     $pdf=new xPDF();
     $pdf->SetTopMargin(2);
     $pdf->AliasNbPages();
  
     $pdf->SetTitle($title." - ".$date);
     $pdf->AddPage('P');
     $pdf->SetFont('Arial','',10);
     $pdf->SetAutoPageBreak(1,10);
     //$vo_result->seek(0);
     
      /*// Stylesheet
$pdf->SetStyle("b","courier","B",12,"10,100,250",15);
$pdf->SetStyle("p","courier","N",12,"10,100,250",15);
$pdf->SetStyle("h1","times","N",18,"102,0,102",0);
$pdf->SetStyle("a","times","BU",9,"0,0,255");
$pdf->SetStyle("pers","times","I",0,"255,0,0");
$pdf->SetStyle("place","arial","U",0,"153,0,0");
$pdf->SetStyle("vb","times","B",0,"102,153,153");
   
     */
     
     
    // .storagepath            {width:100%; text-align:center; font-size:14px;}
    // .storagepathdescription {width:100%;	text-align:center; font-size:12px;}
     $css['itemtable'] ="style='border-collapse:collapse;height:$rowheight;border:1px solid black;'";
     $css['itemtabletrtd']= "style='height:$rowheight;'";
     $css['td_box'] = "style='width:30px;height:$rowheight;float:left;margin:-10px 0 0 5px;'";
     $css['boxi'] = "style='border:1px solid black; width:25px; height:25px; margin:5px;'";
     $css['td_idno'] = "style='width:165px; font-size:18px;'";
     $css['casetype'] ="style='font-size:8px; font-style:italic;'";
     /*
     .path          {overflow:wrap; break-word; width:$rowheight; height:$rowheight;float:left;border:1px solid red;}
     */
     $css['td_name'] = "style='width:220px;font-size:18px;margin-right:25px;'";
     $css['td_image'] = "style='width:110px;'";     
     $css['td_imageimg'] ="style='vertical-align:middle;'";               
     $css['td_location'] ="style='width:225px;text-align:right;  font-size:14px;padding-right:25px;'";
     $css['box'] = "style='border:1px solid black; width:25px; height:25px; margin:5px;'";
     /*
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
 $css = "<style type='text/css'>$css</style>" ;
     */
     
     $vn_line_count = 0;
     $rh=4;
     
     $out .= makeListofObjectsInThisStorageLocation( $t_item);
     $out .= makeListofStorageInThisStorageLocation( $t_item,$recursive);


 //$pdf->Output('D',$pdfname);
  exit;   


 	
 // Report header/title
 $out = str_replace("zzzzz","&raquo;",$t_item->getWithTemplate("
      <div class='storagepath'><br/><br/>[ ^ca_storage_locations.hierarchy.preferred_labels.name%removeFirstItems=1%delimiter=_zzzzz_  ]</div>
      <ifdef code='ca_storage_locations.description'><div class='storagepathdescription'>^ca_storage_locations.description<br/></div></ifdef>
  "));
 $rowcount++; //room for header

 // First get all objects within THIS Storage_Location
 $out .= getListofObjectsInThisStorageLocation($t_item);
 $out .= getListofStorageInThisStorageLocation($t_item,$recursive);
	
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
 
 
   $pageHeader = "<table style='width: 100%;'><tr>";
   
   if(file_exists($this->request->getThemeDirectoryPath()."/graphics/logos/".$this->request->config->get('report_img'))){
		   $pageHeader .= "<td rowspan='2'>
		                      <img style='width:351px;height:56px;' src='"
		                      .$this->request->getThemeDirectoryPath()."/graphics/logos/".$this->request->config->get('report_img')."'></td>";
	 }
	 $pageHeader .= "<td style='width: 360px; font-size: 30px; margin: 0px 0px 0px 0px; vertical-align:bottom;height:55px;line-height:0.9em;' >"
	                .$this->getVar('headerTitle')."</td></tr><tr><td>".$this->getVar('reportversion')."</td></tr></table>";
 
    $vs_footer = "<table class='footerText' style='width: 100%;'><tr>";
		$vs_footer .= "<td id='version'>".$this->getVar('version')."</td>";
		$vs_footer .= "<td class='footerText'  style='font-family: \"Sans Light\"; font-size: 12px; text-align: center;'>".caGetLocalizedDate(null, array('dateFormat' => 'delimited'))."</td>";
		$vs_footer .= "<td class='footerText'  style='font-family: \"Sans Light\"; font-size: 12px; text-align: center;'>{PAGENO} of {nbpg}</td>";
		$vs_footer .= "</tr></table>";
    $pageFooter ="
       <!--BEGIN FOOTER-->
       <!DOCTYPE html>
         <html>
           <head>
	           <link type='text/css' href='<?php print $this->getVar('base_path'); ?>/PHSpdf.css' rel='stylesheet' />
           </head>
           <body>$vs_footer</body>
         </html>
       <!--END FOOTER-->";
 
 
//echo $out; exit;

 	if(file_exists('PHSpdf2.css')){$pdfcss = file_get_contents('PHSpdf2.css');}
  $this->setVar('PDFRenderer','domPDF');
  $pdfout =  $this->render("pdfStart.php");
 // $pdfout .= $css.$pdfcss;
  $header = $this->render("header2.php");
  $footer .= $this->render("footer.php");
  $pdfout .= $out;
  $pdfout .= $this->render("pdfEnd.php");
 // echo $header;exit;
  set_include_path("/mpdf");
  include("../mpdf/mpdf.php");
  //mPDF ([$mode, $format, $default_font_size, $default_font, $margin_left, $margin_right, $margin_top, $margin_bottom, $margin_header, $margin_footer, $orientation)
  $mpdf=new mPDF('','','','','5','5','0','0','0','0','p');
  $mpdf->use_kwt = true;
  $mpdf->setAutoTopMargin = true;
  $mpdf->setAutoBottomMargin = true;
  $mpdf->WriteHTML($pdfcss,1);//set style sheet 
  $mpdf->WriteHTML($css,1);//set style sheet 
  //$mpdf->SetDisplayMode('fullpage');
  $mpdf->SetHTMLHeader($pageHeader);
  $mpdf->SetHTMLFooter($pageFooter);
  $mpdf->WriteHTML($pdfout);
//  $mpdf->WriteHTML($pdfout);
 $filename = $t_item->getWithTemplate("^ca_storage_locations.idno_stub");
  $mpdf->Output();
  exit;
  
  
  
  
  // exit;
  echo $pdfout;
// exit;  
 
?>