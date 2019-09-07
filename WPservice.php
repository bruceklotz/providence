<?php
/* ----------------------------------------------------------------------
 * WPservice.php :   Pelhamhs.org
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *  This provides simple read-only access to the CollectiveAccess database to Wordpress using
 *  the CollectiveAccess Wordpress plugin.
 *
 *  It MUST be located in the CollectiveAccess root directory.
 * ----------------------------------------------------------------------
 */
     $WPserviceV = "1.0.18";    
     
/*************************************************************
 * SWITCH                                                    *
 *   getVersion  :     Retrieve WPservice Version            *
 *                                                           *
 *   Returns STRING version.                                 *
 *************************************************************/
      if ($q = $_GET['getVersion']){
          echo $WPserviceV; 
          exit;                     
      }//if $q=...
/*************************************************************/
     
     // some defaults:
     $access_public = "accessible to public";
     
     // Setup connections and access to database:
     if (!file_exists('./setup.php')) { print "No setup.php file found!"; exit; }
	   require('./setup.php');
    
     // connect to database
     $o_db = new Db(null, null, false);
     if (!$o_db->connected()) {
	       $opa_error_messages = array("Could not connect to database. Check your database configuration in <em>setup.php</em>.");
	       require_once(__CA_BASE_DIR__."/themes/default/views/system/configuration_error_html.php");
	       exit();
     }
     include_once("./app/lib/Search/SetSearch.php");
     include_once("./app/lib/Search/ObjectSearch.php");
     include_once("./app/lib/Browse/ObjectBrowse.php");
     include_once("./app/lib/Search/StorageLocationSearch.php");
     include_once("./app/lib/Browse/StorageLocationBrowse.php");
     include_once("./app/lib/Search/OccurrenceSearch.php");
     include_once("./app/lib/Browse/OccurrenceBrowse.php");
      
     require_once(__CA_MODELS_DIR__."/ca_objects.php");
     require_once(__CA_MODELS_DIR__."/ca_storage_locations.php");
     require_once(__CA_MODELS_DIR__."/ca_occurrences.php");
     require_once(__CA_MODELS_DIR__."/ca_sets.php");
     require_once(__CA_MODELS_DIR__."/ca_set_items.php");
  
 
/*************************************************************
 *   Function                                                *
 *   getobject = object_id: Get Objects by object_id         *
 *                                                           *
 *   Returns JSON encoded list of object_ids.                *
 *   Data access restricted to Public_access                 *
 *   Available fields are currently hardcoded.               *
 *                                                           *
 *************************************************************/
    function getobject($q,$access=null){    	       
             $qr_results = new ca_objects($q);
             if ($access == $qr_results->getWithTemplate('^ca_objects.access')){
             	   $size = $_GET['size'];
                 $url = $qr_results->get('ca_object_representations.media.'.$size,array('returnURL'=>true));

             	   $arr = array('display_text' => $qr_results->getWithTemplate('^ca_objects.display_text'),
             	             'description'  => $qr_results->getWithTemplate('^ca_objects.description'),
             	             'ca_object_representations' => $qr_results->getWithTemplate('^ca_object_representations.media.medium'),
             	             'ca_object_representations_url' => $url,
             	             'idno' => $qr_results->getWithTemplate('^ca_objects.idno'),
             	             'object_id' => $qr_results->getWithTemplate('^ca_objects.object_id'),
             	             'name' => $qr_results->getWithTemplate('^ca_objects.preferred_labels.name')
                 );
                   
             }//if access  
             return $arr;
}
/*************************************************************
 *   Function                                                *
 *   getstoragelocation = location_id: Get Storage Location  *
 *   by location_id                                          *
 *                                                           *
 *   Returns JSON encoded list of object_ids.                *
 *   Data access restricted to Public_access                 *
 *   Available fields are currently hardcoded.               *
 *                                                           *
 *************************************************************/
    function getstoragelocation($q,$access=null){    	       
             $qr_results = new ca_storage_locations($q);
             if ($access == $qr_results->getWithTemplate('^ca_storage_locations.access')){
             	   $size = $_GET['size'];
                 $url = $qr_results->get('ca_object_representations.media.'.$size,array('returnURL'=>true));
             	   
             	   if( !$qr_results->getWithTemplate('^ca_object_representations.media.medium')){ ;
             	            $t_type = $qr_results->getTypeInstance();
             	            if ($t_type) { $vs_icon = $t_type->getMediaTag('icon', 'icon'); }
	                                 $vs_icon = $t_type->getMediaTag('icon', 'icon');
	                        if ($vs_icon){ $media ="{$vs_icon}";}	//error_log("LWPservice L102 media $media");
	               }else{
	               	        $media = $qr_results->getWithTemplate('^ca_object_representations.media.medium');
	               }		
             	   $arr = array('display_text' => $qr_results->getWithTemplate('^ca_storage_locations.display_text'),
             	             'description'  => $qr_results->getWithTemplate('^ca_storage_locations.description'),
             	             'ca_object_representations' => $media,
             	             'ca_object_representations_url' => $url,
             	             'idno' => $qr_results->getWithTemplate('^ca_storage_locations.idno'),
             	             'location_id' => $qr_results->getWithTemplate('^ca_storage_locations.location_id'),
             	             'name' => $qr_results->getWithTemplate('^ca_storage_locations.preferred_labels.name')
                 );
                   
             }//if access  
             return $arr;
}
/*************************************************************
 *   Function                                                *
 *   getoccurrence = occurrence_id: Get Occurrence           *
 *   by occurrence_id                                        *
 *                                                           *
 *   Returns JSON encoded list of occurrence_ids.            *
 *   Data access restricted to Public_access                 *
 *   Available fields are currently hardcoded.               *
 *                                                           *
 *************************************************************/
    function getoccurrence($q,$access=null){    	       
             $qr_results = new ca_occurrences($q);
             if ($access == $qr_results->getWithTemplate('^ca_occurrences.access')){
             	   $size = $_GET['size'];
                 $url = $qr_results->get('ca_object_representations.media.'.$size,array('returnURL'=>true));
             	   
             	   if( !$qr_results->getWithTemplate('^ca_object_representations.media.medium')){ ;
             	            $t_type = $qr_results->getTypeInstance();
             	            if ($t_type) { $vs_icon = $t_type->getMediaTag('icon', 'icon'); }
	                                 $vs_icon = $t_type->getMediaTag('icon', 'icon');
	                        if ($vs_icon){ $media ="{$vs_icon}";}	//error_log("LWPservice L102 media $media");
	               }else{
	               	        $media = $qr_results->getWithTemplate('^ca_object_representations.media.medium');
	               }		
             	   $arr = array('display_text' => $qr_results->getWithTemplate('^ca_storage_locations.display_text'),
             	             'description'  => $qr_results->getWithTemplate('^ca_occurrences.description'),
             	             'ca_object_representations' => $media,
             	             'ca_object_representations_url' => $url,
             	             'idno' => $qr_results->getWithTemplate('^ca_occurrences.idno'),
             	             'occurrence_id' => $qr_results->getWithTemplate('^ca_occurrences.occurrence_id'),
             	             'name' => $qr_results->getWithTemplate('^ca_occurrences.preferred_labels.name')
                 );                  
             }//if access  
             return $arr;
}  
/*************************************************************
 *   SWITCH                                                  *
 *   getrandomfromset = set_id:Get Objects from a Set        *
 *                             by set_id                     *
 *                                                           *
 *   Returns JSON encoded list of object_ids.                *
 *   Data access restricted to Public_access                 *
 *   Available fields are currently hardcoded.               *
 *                                                           *
 *************************************************************/
   if ($q = $_GET['getrandomfromset']){ 
             // $s_search = new SetSearch();
             // $cr_res = $s_search->search("*");
             $t_set = new ca_sets();
		         if(!$t_set->load($q)){
			          throw new SoapFault("Server", "Invalid set_id");
		         }
		         $sset= $t_set->getItems();
		         $scount = count($sset);
		          
            $pnt = mt_rand(1,$scount);//error_log("L81 ".print_r($sset,1));
            $i=0;
             foreach($sset as $vn_set_id => $va_set){ 
                   $i++;
              	   if ($i == $pnt){
              	       $idno = $va_set[1]["object_id"];//error_log("L85 idno $idno <<<pnt $pnt scount:$scount  vn_set_id:$vn_set_id ");
              	       $arrx[]= getobject($idno,$access_public);
              	   }
             }
}//if getrandomfromset      
/*************************************************************
 *   SWITCH                                                  *
 *   getobjectset = set_id: Get Objects from a Set by set_id *
 *                                                           *
 *   Returns JSON encoded list of object_ids.                *
 *   Data access restricted to Public_access                 *
 *   Available fields are currently hardcoded.               *
 *                                                           *
 *************************************************************/
   if ($q = $_GET['getobjectset']){ 
             // $s_search = new SetSearch();
             // $cr_res = $s_search->search("*");
             $t_set = new ca_sets();
		         if(!$t_set->load($q)){
			          throw new SoapFault("Server", "Invalid set_id");
		         }
             $sset= $t_set->getItems();
		
             foreach($sset as $vn_set_id => $va_set){ 
              	
              	   $idno = $va_set[1]["object_id"];
              	   $arrx[]= getobject($idno,$access_public);
             }
}//if getobjectset            
/*************************************************************
 *   SWITCH                                                  *
 *   findobjects = query : Find Objects by search query      *
 *                                                           *
 *   Returns JSON encoded list of object_ids.                *
 *   Data access restricted to Public_access                 *
 *   Available fields are currently hardcoded.               *
 *                                                           *
 *************************************************************/ 
     if ($q = $_GET['findobjects']){
             // do a search and print out the titles of all found objects
             $o_search = new ObjectSearch();
             $qr_results = $o_search->search($q);
                                                 
             while($qr_results->nextHit()) {                 
             	   if(getobject($qr_results->getWithTemplate('^ca_objects.object_id'),$access_public)){// this prevents empty results from outputting.
             	       $arrx[] = getobject($qr_results->getWithTemplate('^ca_objects.object_id'),$access_public);
             	   }                
             }//while           
      }//if $q=...      
/*************************************************************
 * SWITCH                                                    *
 *   getobject = object_id : Retrieve Object by object_id    *
 *                                                           *
 *   Returns JSON encoded.                                   *
 *   Data access restricted to Public_access                 *
 *   Available fields are currently hardcoded.               *
 *                                                           *
 *************************************************************/
      if ($q = $_GET['getobject']){      
            $arrx[] = getobject($q,$access_public);                     
      }//if $q=...
/*************************************************************
 *   SWITCH                                                  *
 *   findstoragelocation = query : Find Storage Locations    *
 *   by search query                                         *
 *                                                           *
 *   Returns JSON encoded list of object_ids.                *
 *   Data access restricted to Public_access                 *
 *   Available fields are currently hardcoded.               *
 *                                                           *
 *************************************************************/ 
     if ($q = $_GET['findstoragelocation']){
             // do a search and print out the titles of all found objects
             $s_search = new StorageLocationSearch();
             $qr_results = $s_search->search($q);
              
             while($qr_results->nextHit()) {
                 
             	   if(getstoragelocation($qr_results->getWithTemplate('^ca_storage_locations.location_id'),$access_public)){// this prevents empty results from outputting.
             	       $arrx[] = getstoragelocation($qr_results->getWithTemplate('^ca_storage_locations.location_id'),$access_public);
             	   }               
             }//while            
      }//if $q=...      
/*************************************************************
 *   SWITCH                                                  *
 *   findoccurrence = query : Find Occurrence                *
 *   by search query                                         *
 *                                                           *
 *   Returns JSON encoded list of occurrence_ids.                *
 *   Data access restricted to Public_access                 *
 *   Available fields are currently hardcoded.               *
 *                                                           *
 *************************************************************/ 
     if ($q = $_GET['findoccurrence']){
             // do a search and print out the titles of all found objects
             $s_search = new OccurrenceSearch();
             $qr_results = $s_search->search($q);
              
             while($qr_results->nextHit()) {
                 
             	   if(getoccurrence($qr_results->getWithTemplate('^ca_occurrences.occurrence_id'),$access_public)){// this prevents empty results from outputting.
             	       $arrx[] = getoccurrence($qr_results->getWithTemplate('^ca_occurrences.occurrence_id'),$access_public);
             	   }               
             }//while
      }//if $q=...      
    
      $out = json_encode($arrx); 
      echo $out;  
?>