<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/results/local/ca_objects_shelftagsbarcode1_results.php
 * ----------------------------------------------------------------------
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Shelf Tags Barcodes Group 1 Results V1.0.5
 * @type page
 * @pageSize letter 
 * @pageOrientation portrait
 * @tables ca_objects, ca_entities, ca_collections, ca_object_lots
 *
 * @marginTop 0.5in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 */
  echo"<iframe src='pelhamhs.org/ca/index.php/find/SearchObjects/export?export_format=_pdf_ca_objects_shelftagsbarcodes_results'></iframe>";
  
  $this->setVar('group',1);
  include($this->getVar('base_path')."/ca_objects_shelftagsbarcodes_results.php");
  
?>