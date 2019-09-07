<?
/**********************************************************************/
/*                                                                    */
/*  Wrapper for caUtils to all running functions from the WEB via     */
/*  an iframe                                                         */
/*  Pelhamhs.org  pelhamhistory.org                                   */
                   $caUtilsxVer = "1.2.15";
/*                                                                    */
/**********************************************************************/

    //$cmd=$_REQUEST["caUtils"]
    class dummyc {
     public $option_list = array();
     public $sizes = array();
     
     function getOption($option=null){ 
        if(!$option_list[$option]){$option_list[$option] = false;}
        return array($option => $option_list[$option]);}
   
     function setOption($option,$value){$option_list[ $option ] = $value;}
    
}// class
	
class dummyd {
     public $option_list = array();
     public $sizes = array();
     
     function getOption($option=null){ 
        if(!$option_list[$option]){$option_list[$option] = false;}
        return array($option => $option_list[$option]);}
   
     function setOption($option,$value){$option_list[ $option ] = $value;}
    
}// class	

    require_once("setup.php");
    $command = __CA_LIB_DIR__."/Utils/CLIUtils.php";
    include($command);

  
	
     if($_REQUEST["rebuild"]){
	
	echo"<div style='font-size:10px;'>Begin REBUILD ".$_REQUEST["rebuild"]."(caUtilsx V$caUtilsxVer)...:<br/>";flush();
	echo CLIUtils::rebuild_search_index($_REQUEST["rebuild"]);
	echo"<br/>end REBUILD ".$_REQUEST["rebuild"]."(caUtilsx V$caUtilsxVer)</div><br/><br/><br/><br/>";
     }
		
     if($_REQUEST["create_ngrams"]){
     $ngram_opts = new dummyc;
     $ngram_opts->setOption('clear',0);
     $ngram_opts->setOption('sizes',null);
     $ngram_opts->sizes = array();	
	echo"<div style='font-size:10px;'>Begin BUILDING NGRAMS (caUtilsx V$caUtilsxVer)...:<br/>";flush();
	ob_start();
	CLIUtils::create_ngrams($ngram_opts);
	$outs = ob_get_contents();
	ob_end_clean();
	echo str_replace("]","]<br/>", $outs);
	
	echo"<br/>end BUILDING NGRAMS(caUtilsx V$caUtilsxVer)</div>";
     }
		
     if($_REQUEST["reprocessmedia"]){
	
	$po_opts = new dummyc;
	echo"<div style='font-size:10px;'>Begin REPROCESSING MEDIAS (caUtilsx V$caUtilsxVer)...:<br/>";flush();
	ob_start();
	CLIUtils::reprocess_media($po_opts );
	$outs = ob_get_contents();
	ob_end_clean();
	echo str_replace("]","]<br/>", $outs);
	echo"<br/>end REPROCESSING MEDIA (caUtilsx V$caUtilsxVer)</div>";
     }		

     if($_REQUEST["compact"]){
        echo"<div style='font-size:10px;'>begin COMPACT remove_unused_media (caUtilsx V$caUtilsxVer):<br/>";
      //  echo " <div id='progressbar' ></div><script src='bootstrap/js/jquery.min.js'></script><script type='text/javascript'>jQuery('#progressbar').progressbar({value: 0});</script>
      //         <div id='installerLog' class='installStatus'></div>";
	$po_opts = new dummyc; //Zend_Console_Getopt("name");
	ob_start();
	CLIUtils::remove_unused_media($po_opts);
	$outs = ob_get_contents();
	ob_end_clean();
	echo str_replace("]","]<br/>", $outs);
	echo"<hr/>
	     Begin COMPACT remove_deleted_representations (caUtilsx V$caUtilsxVer):<br/>";
	ob_start();
	CLIUtils::remove_deleted_representations($po_opts);
	$outs = ob_get_contents();
	ob_end_clean();
	
	echo str_replace("[0m","<br/>",$outs);
	echo"end COMPACT (caUtilsx V$caUtilsxVer)</div>";	    
     }
 
  if($_REQUEST["update_locations"]){
	     	
	     	echo"<div style='font-size:10px;'>Begin Updating Locations ".$_REQUEST["update_locations"]."(caUtilsx V$caUtilsxVer)...:<br/>";flush();
	      ob_start();
	      CLIUtils::reload_object_current_locations($_REQUEST["update_locations"]);
	      $outs = ob_get_contents();
	      ob_end_clean();
	      echo str_replace("]","]<br/>",$outs);
	      echo"<br/>end Updating Locations ".$_REQUEST["update_locations"]."(caUtilsx V$caUtilsxVer)</div><br/><br/><br/><br/>";
  }
 
 ?>