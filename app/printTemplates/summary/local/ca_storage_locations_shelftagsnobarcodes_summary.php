<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/summary/local/ca_storage_locations_shelftagsnobarcodes_summary.php
 * ----------------------------------------------------------------------
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PHS Shelf Tags No Barcodes V1.3.2
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
 */
  $this->setVar('showbarcodes','no');
  include($this->getVar('base_path')."/ca_storage_locations_shelftagsbarcodes_summary.php");
  
?>