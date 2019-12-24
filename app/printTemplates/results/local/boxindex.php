<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/boxindex.php PELHAMHS.ORG
 * ----------------------------------------------------------------------
 
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Box Index Results V1.0.3
 * @type page
 * @pageSize letter
 * @pageOrientation portrait
 * @tables ca_storage_locations
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */
	$version = "PHS Box Index Results V1.0.3"; 
 	$t_item 				= $this->getVar('t_subject');
	$va_bundle_displays 	= $this->getVar('bundle_displays');
	$t_display 				= $this->getVar('t_display');
	$va_placements 			= $this->getVar("placements");
	
	print $this->render("pdfStart.php");
	print $this->render("header.php");
	print $this->render("footer.php");

 	$vo_result = $this->getVar('result');
 	
 	$vo_result->seek(0);
	$vo_result->nextHit(); 
	
	//$mediaimg="<img src='".$vo_result->getMediaPath('ca_object_representations.media', 'preview')."'>";

$out = str_replace("zzzzz","&raquo;",$vo_result->getWithTemplate("
								<div class='storagetitle'>^ca_storage_locations.preferred_labels</div>
								<div class='storagepath'>[ ^ca_storage_locations.hierarchy.preferred_labels.name%removeFirstItems=1%delimiter=_zzzzz_  ]</div>
								<div class='version'>$version</div>
								<ifdef code='ca_storage_locations.description'>^ca_storage_locations.description<br/></ifdef>"));


$out .= $vo_result->getWithTemplate("<div class='summary'>
	<table>
	{{{<unit relativeTo='ca_objects' maxLevelsFromTop='0' delimiter='_->_'>}}}
   		<tr>
   			<td ><div class='box'>&nbsp;</div></td>
   			<td class='case'>^ca_objects.idno<div class='casetype'>^ca_objects.type_id</div></td>
   			<td>^ca_objects.preferred_labels</td>
   			<td>^ca_object_representations.related.media.tiny</td>
   		</tr>
  {{{</unit>}}}
   		
	{{{<unit relativeTo='ca_storage_locations.children'>}}}
	 	<tr>
			<td ><div class='box'>&nbsp;</div></td>
			<td class='case'>
				<if rule='^ca_storage_locations.type_id=~/box/'><span class='casetypebox'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></if>^ca_storage_locations.idno<div class='casetype'>
				<if rule='^ca_storage_locations.type_id=~/box/'><span class='casetypebox'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></if>^ca_storage_locations.type_id</div>
			</td>
   			<td>^ca_storage_locations.preferred_labels<div class='casetype'>^ca_storage_locations.preferred_labels</div></td>
   			<td>^ca_object_representations.media.tiny</td>
   		</tr>
   	
   		{{{<unit relativeTo='ca_objects'>}}}
   		<tr>
   			<td ><div class='box'>&nbsp;</div></td>
   			<td class='case'>^ca_objects.idno<div class='casetype'>^ca_objects.type_id</div></td>
   			<td>^ca_objects.preferred_labels<div class='casetype'>^ca_storage_locations.preferred_labels.name</div></td>
   			<td>^ca_object_representations.media.tiny</td>
   		</tr>
   		{{{</unit>}}}
   	{{{</unit>}}}");

	 
//	 
//	 <tr>
//			
//			<td class='case'>^ca_objects.idno<div class='casetype'>^ca_objects.type_id</div></td>
  // 		<td>^ca_objects.preferred_labels</td>");
   		
   		//<td>aa ".$vo_result->getMediaPath('ca_object_representations.media.tiny', 'tiny')." bb ^ca_object_representations.tiny xx ");
//   			$t_rep = new ca_object_representations();

	//print $t_rep->getMediaUrl("media", "original");yy</td>
//   </tr>
 //  {{{</unit>}}}
   		
 $out .= " </table></div>";

	echo $out;
	print $this->render("pdfEnd.php");
	
	?>