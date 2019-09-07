<?php
/** ---------------------------------------------------------------------
 * app/lib/Plugins/InformationService/Lexicon4is.php :   Pelhamhs
 * ----------------------------------------------------------------------
 * V1.0.0
 * ----------------------------------------------------------------------
 */

  
    
require_once(__CA_LIB_DIR__."/Plugins/IWLPlugInformationService.php");
require_once(__CA_LIB_DIR__."/Plugins/InformationService/BaseInformationServicePlugin.php");

global $g_information_service_settings_Lexicon4is;
$g_information_service_settings_Lexicon4is = array();/*
	'lang' => array(
		'formatType' => FT_TEXT,
		'displayType' => DT_FIELD,
		'default' => 'en',
		'width' => 30, 'height' => 1,
		'label' => _t('Lexicon4is language'),
		'description' => _t('2- or 3-letter language code for Lexicon4is to use. Defaults to "en". See http://meta.wikimedia.org/wiki/List_of_Lexicon4is')
	),
);*/

class WLPlugInformationServiceLexicon4is Extends BaseInformationServicePlugin Implements IWLPlugInformationService {
	# ------------------------------------------------
	static $s_settings;
	# ------------------------------------------------
	/**
	 *
	 */
	public function __construct() {
		global $g_information_service_settings_Lexicon4is;

		WLPlugInformationServiceLexicon4is::$s_settings = $g_information_service_settings_Lexicon4is;
		parent::__construct();
		$this->info['NAME'] = 'Lexicon4is';
		
		$this->description = _t('Provides access to Lexicon4is service');
	}
	# ------------------------------------------------
	/** 
	 * Get all settings settings defined by this plugin as an array
	 *
	 * @return array
	 */
	public function getAvailableSettings() {
		return WLPlugInformationServiceLexicon4is::$s_settings;
	}
	/** 
	 * Perform lookup on Lexicon4is-based data service
	 *
	 * @param array $pa_settings Plugin settings values
	 * @param string $ps_search The expression with which to query the remote data service
	 * @param array $pa_options Lookup options (none defined yet)
	 * @return array
	 */
	public function lookup($pa_settings, $ps_search, $pa_options=null) {
		 $url ="http://www.nomenclature.info/recherche-search.app?lang=en&q={$ps_search}&ps=50&pid=1&wo=I&ws=INT";
		 $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_ENCODING       => "",     // handle compressed
            CURLOPT_USERAGENT      => "CollectiveAccess web service lookup", // name of client
            CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT        => 120,    // time-out on response
        ); 
     $o_curl = curl_init($url);
     curl_setopt_array($o_curl, $options);
     $vs_results  = curl_exec($o_curl);
    curl_close($o_curl);
    
    
    // Now scrub the returned web page.
    // They will be contained in a table
    //
    $vs_results = substr($vs_results, strpos($vs_results, '<table>')+8,strpos($vs_results, '</table>')-8); 	
    $DOM = new DOMDocument;
    libxml_use_internal_errors(true);
    $DOM->loadHTML($vs_results);

    $out =array();
    $rows = $DOM->getElementsByTagName('tr');
   
    foreach ($rows as $row) {
        $cells = $row->getElementsByTagName('td');
        $i=1;
        foreach ($cells as $cell) {
        	switch($i){
    				case 1: // 1st Cell = idno,
    				    $label = $cell->nodeValue;
    				    //Now extract the id from the returned link.  This in fact returns only the last link in the cell (why would there be more?)
    				    $alines = $cell->getElementsByTagName('a');
                foreach ($alines as $aline){
                    $hrefraw = $aline->getAttribute('href');
                    $parts = parse_url($hrefraw);
                    parse_str($parts['query'], $query);
                    $id=trim($query['id']);
                    $idno=trim($aline->nodeValue);
                }
    				    break;
    				case 2: //2nd Cell = Alt Name
    				    $altlabel = $cell->nodeValue;
    				     break;
    				case 3; //3rd Cell = Concept
    				     $concept = $cell->nodeValue;
    				     break;
    				case 4; //4th Cell = Image
    				    $imglines = $cell->getElementsByTagName('img');
                foreach ($imglines as $imgline){
                    $image = $imgline->getAttribute('src');//?$imgline->getAttribute('src'):$image;//error_log("L202 image".$image);
                   
                }
        		     //$image = $cell->nodeValue;
        		     
        		     $i=0;
        	}//switch
        	$i++;	             
     }//foreach cell
     $url = "http://www.nomenclature.info/parcourir-browse.app?lang=en&ws=INT&wo=I&id=$id";
     
     $va_return['results'][] = array(
			          'label' => $idno."[ $altlabel - $concept ]",
			          'url' => $url,
			          'idno' => $idno
			         );
     }//foreach row
				return $va_return;
	}	
	# ------------------------------------------------
	/** 
	 * Fetch details about a specific item from a Lexicon4is-based data service for "more info" panel
	 *
	 * @param array $pa_settings Plugin settings values
	 * @param string $ps_url The URL originally returned by the data service uniquely identifying the item
	 * @return array An array of data from the data server defining the item.
	 */ 
	public function getExtendedInformation($pa_settings, $ps_url) {

		$va_info = $this->getExtraInfo($pa_settings, $ps_url);

		$vs_display .= "<p><b>For more Information: </b><a href='$ps_url' target='_blank'>$ps_url</a></p>";
		$vs_display .= "<p><b>Label </b>".$va_info['label']." - <img src='".$va_info['image']."'></p>";
		$vs_display .= "<p><b>Full Path:</b> ".$va_info['tree'].$va_info['label']."</p>";
		$vs_display .= "<p><b>Definition: </b>".$va_info['definition']."</p>";
		
		return array('display' => $vs_display);
	}
	# ------------------------------------------------
	public function getExtraInfo($pa_settings, $ps_url) {
		 $va_return = array();
		 $ps_url = html_entity_decode($ps_url); 
		 $link_root="http://www.nomenclature.info/";

		 $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_ENCODING       => "",     // handle compressed
            CURLOPT_USERAGENT      => "CollectiveAccess web service lookup", // name of client
            CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT        => 120,    // time-out on response           
        ); 
    $o_curl = curl_init($ps_url);
    curl_setopt_array($o_curl, $options);//error_log("L175 ".$ps_url);
    $vs_results  = curl_exec($o_curl);
   	curl_close($o_curl);
   	
   	//label
   	$labelraw = substr($vs_results, strpos($vs_results, 'Preferred Primary Term'));
   	$label = trim(substr($labelraw, strpos($labelraw,'<h2 class="mrgn-tp-0">')+22,40));//error_log("L177 label $label");
   	
   	//definition
    $definitionraw = substr($vs_results, strpos($vs_results, '<dt>Definition</dt>'),strpos($vs_results, '</dd>'));
    $definition = substr($definitionraw, strpos($definitionraw, '<dd>')-4);
    	
    //Full path
    $treeraw = substr($vs_results, strpos($vs_results, '<section id="nomeclature-tree-content">'),strpos($vs_results, '</section>'));
    $treeraw = substr($treeraw, strpos($treeraw, '<li class="list-unstyled">')+26);
    $treecooking = explode('<li class="list-unstyled down">',$treeraw);
    $altname="";
    foreach($treecooking as $tc){error_log("L186 tc ".count($treecooking)." - ".print_r($tc,1));
        $tDOM = new DOMDocument;
        libxml_use_internal_errors(true);
        $tDOM->loadHTML($tc);
        $i=0;
           foreach ($tDOM->getElementsByTagName('a') as $node){
              if ($i==0){
                  $tree .= "<a href='$link_root".$node->getAttribute("href")."' target='_blank'>".$node->nodeValue."</a> ➔ ";
              }else{
              		$altname .="Alt: <a href='$link_root".$node->getAttribute("href")."' target='_blank'>".$node->nodeValue."</a> <br/> ";
              }	
              $i++;	
           }
    } 
    $tree = trim(substr($tree,0,-4));//remove last arrow 
    $tree .= "<br/>".$altname;
  
  
  
  
  
   // $treeraw = substr($treeraw, strpos($treeraw, '<li class="list-unstyled down">'),strpos($treeraw, '</ul>'));	 
 /*   $tDOM = new DOMDocument;
    libxml_use_internal_errors(true);
    $tDOM->loadHTML($treeraw);
    
    $finder = new DomXPath($tDOM);
    $spaner = $finder->query("//*[contains(@class, 'list-unstyled down')]");
    foreach ($spaner as $span){
      foreach ($span->getElementsByTagName('a') as $node)
        {  $tree .= "<a href='$link_root".$node->getAttribute("href")."' target='_blank'>".$node->nodeValue."</a> ➔ ";
      }
    }     
    //$tree = trim(substr($tree,0,-4));//remove last arrow
   */ 
   
   
    //Image
    $vs_result = substr($vs_results, strpos($vs_results, '<section id="nomeclature-detail-content">'),strpos($vs_results, '</section>')); 	
    $DOM = new DOMDocument;
    libxml_use_internal_errors(true);
    $DOM->loadHTML($vs_result);
    
    $imglines = $DOM->getElementsByTagName('img');
    $image = $imglines[0]?$imglines[0]->getAttribute('src'):"";
       
     $va_return = array(
			          'label' => $label,/*."( ".$idno.'[ '.$altlabel.' - '.$concept.' ] )',*/
			          'full_path' => $ps_url,
			          'definition'=> $definition,
			          'tree' => $tree,
			          'idno' => $idno,
			          'image'=> $image
			         ); 
    return $va_return;
 
	}
	# ------------------------------------------------
	/**
	 * Get display value
	 * @param string $ps_text
	 * @return string
	 */
	public function getDisplayValueFromLookupText($ps_text){
		  // Strip the full path from the '[':
		  if(!$ps_text) { return ''; }
		  return substr($ps_text, 0, strpos($ps_text, '['));
	}
	# ------------------------------------------------
	private static function getPageTitleFromURI($ps_uri) {
		if(preg_match("/\/([^\/]+)$/", $ps_uri, $va_matches)) {
			return $va_matches[1];
		}

		return false;
	}
	# ------------------------------------------------

}//class
?>
