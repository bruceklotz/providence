<?php
/** ---------------------------------------------------------------------
 * app/lib/Plugins/InformationService/Lexicon3.php :   Pelhamhs
 * ---------------------------------------------------------------------- 

 * ----------------------------------------------------------------------
 */

  
    
require_once(__CA_LIB_DIR__."/Plugins/IWLPlugInformationService.php");
require_once(__CA_LIB_DIR__."/Plugins/InformationService/BaseInformationServicePlugin.php");

global $g_information_service_settings_Lexicon3;
$g_information_service_settings_Lexicon3 = array();/*
	'lang' => array(
		'formatType' => FT_TEXT,
		'displayType' => DT_FIELD,
		'default' => 'en',
		'width' => 30, 'height' => 1,
		'label' => _t('Lexicon3 language'),
		'description' => _t('2- or 3-letter language code for Lexicon3 to use. Defaults to "en". See http://meta.wikimedia.org/wiki/List_of_Lexicon3s')
	),
);*/

class WLPlugInformationServiceLexicon3 Extends BaseInformationServicePlugin Implements IWLPlugInformationService {
	# ------------------------------------------------
	static $s_settings;
	# ------------------------------------------------
	/**
	 *
	 */
	public function __construct() {
		global $g_information_service_settings_Lexicon3;

		WLPlugInformationServiceLexicon3::$s_settings = $g_information_service_settings_Lexicon3;
		parent::__construct();
		$this->info['NAME'] = 'Lexicon3';
		
		$this->description = _t('Provides access to Lexicon3 service');
	}
	# ------------------------------------------------
	/** 
	 * Get all settings settings defined by this plugin as an array
	 *
	 * @return array
	 */
	public function getAvailableSettings() {
		return WLPlugInformationServiceLexicon3::$s_settings;
	}
	/** 
	 * Perform lookup on Lexicon3-based data service
	 *
	 * @param array $pa_settings Plugin settings values
	 * @param string $ps_search The expression with which to query the remote data service
	 * @param array $pa_options Lookup options (none defined yet)
	 * @return array
	 */
	public function lookup($pa_settings, $ps_search, $pa_options=null) {
		 $url = "http://pelhamhs.org/lexicon3.php?s={$ps_search}";
		
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
        $vs_result  = curl_exec($o_curl);
    		curl_close($o_curl);
    		
    		if($va_result = json_decode($vs_result, true)){
    		  		
	         	foreach($va_result as $va){
	         		 if( !$va['error'] ){
			           $r_url = $va['Category']." ➔ ".$va['Class'];
			           $r_url .= ($va['Subclass']?" ➔ ".$va['Subclass']:null);
			           $r_url .= ($va['Primary']?" ➔ ".$va['Primary']:null);
			           $r_url .= ($va['Secondary']?" ➔ ".$va['Secondary']:null);
			           $r_url .= ($va['Tertiary']?" ➔ ".$va['Tertiary']:null);
			
                 $va_return['results'][] = array(
				           'label' => $va['Term']." [ $r_url ]",
				           'url' => htmlentities("http://pelhamhs.org/lexicon3.php?k=".$va['key']),
				           'idno' => $va['key']
		  	         );
		  	       }else{
		  	       	//error
		  	       	$va_return['results'][] = array(
				           'label' => $va['error'],
				           'url' => '',
				           'idno' => '');
				       }//if error	
	
	        	}
		
		//if(!isset($va_result['results']['bindings']) || !is_array($va_result['results']['bindings'])) {
	//		return false;
	//	}
	
       }//if $va_result...

		   return $va_return;
	}
	
	# ------------------------------------------------
	/** 
	 * Fetch details about a specific item from a Lexicon3-based data service for "more info" panel
	 *
	 * @param array $pa_settings Plugin settings values
	 * @param string $ps_url The URL originally returned by the data service uniquely identifying the item
	 * @return array An array of data from the data server defining the item.
	 */
	public function getExtendedInformation($pa_settings, $ps_url) {
		$vs_display = "<p><a href='$ps_url' target='_blank'>$ps_url</a></p>";

		$va_info = $this->getExtraInfo($pa_settings, $ps_url);

		$vs_display .= "<p><b>Full Path: </b>".$va_info['full_path']."</p>";
		
		return array('display' => $vs_display);
	}
	# ------------------------------------------------
	public function getExtraInfo($pa_settings, $ps_url) {
		 $va_return = array();
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
        curl_setopt_array($o_curl, $options);
        $vs_result  = curl_exec($o_curl);
    		curl_close($o_curl);
    		if($va_result = json_decode($vs_result, true)){
	         	foreach($va_result as $va){ 
			         $full_path =   $va['Category']." ➔ ".$va['Class'];
			         $full_path .= ($va['Subclass']?" ➔ ".$va['Subclass']:null);
			         $full_path .= ($va['Primary']?" ➔ ".$va['Primary']:null);
			         $full_path .= ($va['Secondary']?" ➔ ".$va['Secondary']:null);
			         $full_path .= ($va['Tertiary']?" ➔ ".$va['Tertiary']:null);
               $va_return['full_path'][] = $full_path;
		  	    }//foreach
		        return array('full_path'=>$full_path); 
	     }//if
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

}
