<?php
/* ----------------------------------------------------------------------
 * app/views/administrate/maintenance/caUtills_landing_html.php : PELHAMHS.ORG
 *  
    requires modified CLIProgressBar.php
 * ----------------------------------------------------------------------
 */    $caUtills_landing_htmlVer = "2.3.136";                             /*
 * ----------------------------------------------------------------------
 */
    require_once("setup.php");
    $command = __CA_LIB_DIR__."/Utils/CLIUtils.php";//CLIUtilsx.php";		
    include($command);
    $PHP_SELF = $_SERVER['PHP_SELF'] ;
   
   
/*  dummy class to allow calling CLIUtils           */  
    if (!is_subclass_of('dummyc', 'BaseApplicationTool')){ 
    Class dummyc extends BaseApplicationTool {
       public $option_list = array();
       public $sizes = array();
       
       public function __construct($po_request =null) { parent::__construct($po_request); }
       
       function getOption($option=null){ 
          if(!$option_list[$option]){$option_list[$option] = false;}
          return array($option => $option_list[$option]);}
   
       function setOption($option,$value){$option_list[ $option ] = $value;}    
    }}// class	
/*--------------------------------------------------------------------------------------*/
/* style sheet                                                     */
echo "<style type='text/css'>
                #caUtillrtn {
                    height: 100%;
                    z-index: 1000;
                    background: #eee;
                    overflow-wrap:anywhere;
                    word-break:break-all;
                    vertical-align:top;
                    padding-left:25px;
                    position:relative;
                }
                #caUtillrtn img {
                   vertical-align:middle;
                   height:50px;
                } 
                 .imgx {
                     height:50px;
                     display:inline-block;
                 }
                .catutilbut {
                   border:1px solid grey;
                   margin: 1px;
                   padding: 5px;
                   border-radius: 10px;
                }
                .catutilbut button {
                  margin:5px;
                }
               #caUtilllog {
                    height: 100%;
                    z-index: 1000;
                    background: #eee;
                    overflow-wrap:anywhere;
                    word-break:break-all;
                    padding:0px 25px 10px 25px;
                    margin-top:-10px;
                
                }
                #caUtilstat {
                    width: 10px;
                    height: 10px;
                    z-index: 1001;
                    background: transparent;
                    position:relative;
                    left:5px;
                   
                }
                
	      </style>";


/*******************************************************************/	    
/*******************************************************************/

    
/*******************************************************************/	
/*******************************************************************/	    

    if($_REQUEST["rebuild"]){
	       echo"<div style='font-size:10px;'>Begin REBUILD ".$_REQUEST["rebuild"]."(caUtills_landing_html V$caUtills_landing_htmlVer)...:<br/>";flush();
	       echo CLIUtils::rebuild_search_index($_REQUEST["rebuild"]);
	       echo"<br/>end REBUILD ".$_REQUEST["rebuild"]."(caUtills_landing_html V$caUtills_landing_htmlVer)</div><br/><br/><br/><br/>";
     }

/*******************************************************************/	
/*******************************************************************/	     
		
 
	 if($_REQUEST["purge_deleted"]){
	        echo"<div style='font-size:10px;'>Begin purge_deleted ".$_REQUEST["purge_deleted"]."(caUtills_landing_html V$caUtills_landing_htmlVer)...:<br/>";
	        echo " <div id='progressbar' ></div><script src='bootstrap/js/jquery.min.js'></script><script type='text/javascript'>jQuery('#progressbar').progressbar({value: 0});</script>
                       <div id='installerLog' class='installStatus'></div>";
                flush();ob_flush();
	        $po_opts = new dummyc;
	        ob_start();
	        CLIUtils::purge_deleted($po_opts);
	        $outs = ob_get_contents();
	        ob_end_clean();
	        ob_flush();flush();
	        echo str_replace("]","]<br/>",$outs);
	        echo"<br/>end purge_deleted ".$_REQUEST["purge_deleted"]."(caUtills_landing_html V$caUtills_landing_htmlVer)</div><br/>";
    }
/*******************************************************************/	
/*******************************************************************/	

    if(!function_exists("get_table_fk") ){
    function get_table_fk($table_name){
    	         // Returns an array of foreign key constrant tables and keys
    	         $fk = array();
    	         $fk_data =new Db();
               $fk_sql ="SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                         FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                         WHERE REFERENCED_TABLE_NAME = '$table_name'";
               $fk_result = $fk_data->query($fk_sql);
               $x=0;
               while($fk_result->nextRow()) {
               	    $fk[$x]['table_name'] = $fk_result->get('TABLE_NAME');
               	    $fk[$x]['column_name'] = $fk_result->get('COLUMN_NAME');
               	    $x++;
               }//while
               return $fk;
    }}//function get_table_fk	 
	
	        
/*******************************************************************/	
/*******************************************************************/	
	
	
/*******************************************************************/
/* Menu                                                            */
/*******************************************************************/
   if (  !$_REQUEST["form_spam"] and 
         !$_REQUEST["form_multipart_idno_sequences"] 
      ){//prevent header for self contained functions on this page   A hack....
       echo " <div class='catutilbut'>
	               <button type='button' class='callutilsx' id = 'check_history_future' name ='Future History'>Check Future History</button>
	               <button type='button' class='callutilsx' id = 'reload_history_policy' name ='Reloading History Policy'>Reload History Policy</button> 
	               <button type='button' class='callutilsx' id = 'reload_history name ='Reload History'>Reload History</button> Reload / Rebuild Object History Tables
	            </div>";
	     echo " <div class='catutilbut'>
	               <button type='button' class='callutilsx' id = 'compact' name='compacting database'>Compact Database</button>Remove Soft Deleted Items
	               <button type='button' class='callutilsx' id = 'remove_deleted' name='Remove Deleted items'>Remove Deleted items</button>Remove Soft Deleted Objects, Entities, etc.
	               <button type='button' class='callutilsx' id = 'purge_deleted' name='Purge Deleted items'>Purge Deleted items</button>Remove Soft Deleted Objects, Entities, etc.
	            </div> ";
	  
	   
	     echo " <div class='catutilbut'> 
	               <button type='button' class='callutils' id = 'form_spam' name='Mantain Spam'>Mantain Spam</button>
	               <button type='button' class='callutils' id = 'form_multipart_idno_sequences' name='Mantain Multipart Idnos'>Mantain Multipart Idno's</button> Reset or change Multipart idno Sequences, currently only used by movements.
	            </div> ";
	     echo " <div class='catutilbut'> <button type='button' class='callutilsx' id = 'create_ngrams' name='Create ngramse'>Create ngramse</button></div> ";
	
	     echo " <div class='catutilbut'> <button type='button' class='callutilsx' id = 'reprocessmedia' name='Reprocess Media'>Reprocess Media</button></div> ";
	
	       echo  "<div class='catutilbut'>
	                 Current Tmp file: ".__CA_BASE_DIR__."/app/tmp<br/>
	                 <button type='button' class='callutilsc' value ='tmpdir=".__CA_BASE_DIR__."/app/tmp' name ='Purge ca TMP files'>Purge ca TMP files</button>
	                 <button type='button' class='callutilsc' value ='tmpdir=".__CA_BASE_DIR__."/app/tmp&amp;now=1' name ='Purge ca TMP files now'>Purge ca TMP files Now</button>
	                 <button type='button' class='callutilsc' value ='tmpdir=/home/users/web/b1269/ipw.pelhamhs/public_html/pa2/app/tmp&amp;now=1' name ='Purge pa2 TMP files'>Purge pa2 TMP files NOW!</button>
	              </div>";
         echo "<div class='catutilbut'> <button type='button' class='callutilsx' id = 'scan-site-page' name='Scan Site Pages'>Scan Site Pages</button>Scan Site pages and Templates</div> ";
     
     
         echo "<script type='text/javascript'>  
                              
                 function show_log(){
                     var date=new Date();
                     $.ajax({
                        type: \"GET\",
                        dataType: 'text',
                        url: \"".__PB_URL__."?\"+ date.getTime()+ \"=\" +date.getTime(),
                        success: function(data) {
                           
                           if( data.length > 2){
                              $('#caUtilllog').html(data);
                           }
                           
                          if ( $('#caUtilstat').text() == '♔'){
                               $('#caUtilstat').text('♕');
                           }else{
                               $('#caUtilstat').text('♔');
                           }                        
                           
                           if(doneflg ==1){
                               window.clearInterval(loginterval);
                               $('#caUtilstat').empty();
                           }
                        },
                        error: function(xhr){
                          if ( $('#caUtilstat').text() == '♢'){
                               $('#caUtilstat').text('♦');
                           }else{
                               $('#caUtilstat').text('♢'); 
                           }                        
                        }
                     });                  
                }
  
                                  
             
                $('.callutilsx').click(function() {
                   $('#caUtillrtn').html('Running Utilsx....'+ this.name + '<img src=\'".__CA_THEME_URL__."/graphics/loading_jax.gif\'></div>');
                   $('#caUtilllog').empty();
                   doneflg = 0;
                   var nm= this.name
                   loginterval = window.setInterval(\"show_log();\",250); 
                   $.ajax({
                      type: \"GET\",
                      dataType: 'text',
                      url: \"https://pelhamhs.ipower.com".__CA_URL_ROOT__."/caUtilsx.php?\"+this.id+\"=1\",
                      success: function(data) {
                          window.clearInterval(loginterval);
                          doneflg = 1;
                          $('#caUtillrtn').html('Done '+ nm +'.<img class=\'imgx\'> </img>');
                          $('#caUtilstat').empty();
                      },
                      error: function(xhr){
                          $('#caUtillrtn').text(\"An error occured: \" + xhr.status + \" \" + xhr.statusText);
                          window.clearInterval(loginterval);
                          $('#caUtilstat').text(\". \");
                      }
                   });
                });
           

            

           $('.callutils').click(function() {
               $('#caUtillrtn').html('Running....'+ this.name + '<img src=\'".__CA_THEME_URL__."/graphics/loading_jax.gif\'></div>');
               $('#caUtilllog').empty();
               doneflg = 0;
               var nm= this.name
               loginterval = window.setInterval(\"show_log();\",250);
               $.ajax({
                  type: \"GET\",
                  dataType: 'text',
                  url: \"".$PHP_SELF."?\"+this.id+\"=1\",
                  success: function(data) {
                     window.clearInterval(loginterval);
                     doneflg = 1;
                     $('#caUtillrtn').html(data);
                     $('#caUtilstat').empty();
                  },
                  error: function(xhr){
                     $('#caUtillrtn').text(\"An error occured: \" + xhr.status + \" \" + xhr.statusText);
                     window.clearInterval(loginterval);
                     $('#caUtilstat').text(\". \");
                  }
               });
            }); 
            
           $('.callutilsc').click(function() {
               $('#caUtillrtn').html('Running cron...".$callutilscurl."'+ this.name + '<img src=\'".__CA_THEME_URL__."/graphics/loading_jax.gif\'></div>');
               $('#caUtilllog').empty();
               doneflg = 0;
               var nm= this.name
               loginterval = window.setInterval(\"show_log();\",250);
               $.ajax({
                  type: \"GET\",
                  dataType: 'text',
                  url: \"http://pelhamhs.org/cronEmptyTmp.php?\"+this.value,
                  success: function(data) {
                     $('#caUtillrtn').html(data);
                     window.clearInterval(loginterval);
                     doneflg = 1;
                     $('#caUtilstat').empty();
                  },
                  error: function(xhr){
                     $('#caUtillrtn').text(\"An error occured: \" + xhr.status + \" \" + xhr.statusText);
                     window.clearInterval(loginterval);
                     $('#caUtilstat').text(\". \");
                  }
               });
            });   
                 
                   </script>";
  }//if  !$_REQUEST["form_spam"] and !$_REQUEST["form_multipart_idno_sequences"]
  
  if (!$_REQUEST["post_multipart_idno_sequences"] and
      !$_REQUEST["post_spam_update"] ){
           echo "<hr/><div id='caUtillrtn'></div></hr><div id='caUtilstat'></div><div id='caUtilllog'></div>";
           echo "<div style='height:100px;'></div>";
  }

	

/*******************************************************************/
/* Local self contained functions                                  */
/*******************************************************************/
  if($_REQUEST["post_multipart_idno_sequences"]){
  	  $mp_data =new Db();
      $mpud_sql = "UPDATE `ca_multipart_idno_sequences` SET 
                   `idno_stub` = '".$_REQUEST['idno_stub']."',
                   `format` = '".$_REQUEST['format']."',
                   `element` = '".$_REQUEST['element']."',
                   `seq` = '".$_REQUEST['seq']."'
                    WHERE
                    CONVERT( `idno_stub` USING utf8 ) = '".$_REQUEST['oidno_stub']."' AND
                    CONVERT( `format` USING utf8 ) = '".$_REQUEST['oformat']."' AND
                    CONVERT( `element` USING utf8 ) = '".$_REQUEST['oelement']."' LIMIT 1" ;
       echo "<hr/><div id='caUtillrtn'></div></hr>";
       if( $mpup_results = $mp_data->query($mpud_sql)){echo "Updated OK";}else{echo "UPDATE FAILED!";}
  	   $_REQUEST["form_multipart_idno_sequences"] = 1;
}//	
/*******************************************************************/			
	if($_REQUEST["form_multipart_idno_sequences"]){
      // Display the form
	    $mp_data =new Db();
      $mpsel_sql = "SELECT * FROM `ca_multipart_idno_sequences`";
      $mpsel_results = $mp_data->query($mpsel_sql);
      $out .= "<table style=' border-collapse: collapse;'>
                <tr><th>idno_stub</th><th>format</th><th>element</th><th>seq</th></tr></table>";
      while($mpsel_results->nextRow()) {
      	  $out .= "<form action = '$PHP_SELF' method='post'><table style=' border-collapse: collapse;'><tr>
      	            <td><input type='hidden' name = 'post_multipart_idno_sequences' value='post_multipart_idno_sequences'>
      	                 <input type='text' name='idno_stub' value='".$mpsel_results->get('idno_stub')."' />
      	                 <input type='hidden' name='oidno_stub' value='".$mpsel_results->get('idno_stub')."' /></td>
      	            <td><input type='text' name='format' value='".$mpsel_results->get('format')."' />
      	                <input type='hidden' name='oformat' value='".$mpsel_results->get('format')."' /></td>
      	            <td><input type='text' name='element' value='".$mpsel_results->get('element')."' />
      	                <input type='hidden' name='oelement' value='".$mpsel_results->get('element')."' /></td>
      	            <td><input type='text' name='seq' value='".$mpsel_results->get('seq')."' />
      	                <input type='hidden' name='oseq' value='".$mpsel_results->get('seq')."' /></td>
      	            <td><input type='submit' value ='Update'></td></tr></table></form>";
		  }//while
		 
		  echo $out;  
		        
  }//end form_multipart_idno_sequences		        
/*******************************************************************/	
/*******************************************************************/		
  if($_REQUEST["post_spam_update"]){
  	  $mp_data =new Db();
      $mpud_sql = "UPDATE `ca_users_banned_ips` SET 
                   `ip` = '".$_REQUEST['ip']."',
                   `hostname` = '".$_REQUEST['hostname']."',
                   `notes` = '".$_REQUEST['notes']."',
                   `date_banned` = '".$_REQUEST['date_banned']."',
                   `date_expire` = '".$_REQUEST['date_expire']."',
                   `date_last` = '".$_REQUEST['date_last']."',
                   `count` = '".$_REQUEST['count']."'
                    WHERE
                    CONVERT( `ip` USING utf8 ) = '".$_REQUEST['oip']."'" ;
       echo "<hr/><div id='caUtillrtn'></div></hr>";
       if( $mpup_results = $mp_data->query($mpud_sql)){echo "Updated OK";}else{echo "UPDATE FAILED!";}
  	   $_REQUEST["form_spam"] = 1;
}//
/*******************************************************************/				
if($_REQUEST["post_spam_add"]){
  	  $mp_data =new Db();
      $mpud_sql = "INSERT INTO `ca_users_banned_ips` SET 
                   `ip` = 'new'" ;
       if( $mpup_results = $mp_data->query($mpud_sql)){echo "Add OK";}else{echo "Add FAILED!";}
  	   $_REQUEST["form_spam"] = 1;
}//	
/*******************************************************************/			
	if($_REQUEST["form_spam"]){
      // Display the form
	    $mp_data =new Db();
      $mpsel_sql = "SELECT * FROM `ca_users_banned_ips` order by `ip`";
      $mpsel_results = $mp_data->query($mpsel_sql);
      $out .= "<table style=' border-collapse: collapse;'>
                <tr><th style='width:20em'>ip</th><th style='width:20em'>hostname</th><th style='width:50em'>notes</th><th></th></tr>
                <tr><th style='width:20em'>date_banned</th><th style='width:20em'>date_expire</th><th style='width:20em'>date_last</th><th style='width:20em'>count</th></tr></table>";
      while($mpsel_results->nextRow()) {
      	  $out .= "<form action = '$PHP_SELF' method='post'><table style=' border-collapse: collapse;'><tr>
      	            <td style='border-bottom: 2pt solid black;'><input type='hidden' name = 'post_spam_update' value='post_spam_update'>
      	                <input width='20' type='text' name='ip' value='".$mpsel_results->get('ip')."' />
      	                <input type='hidden' name='oip' value='".$mpsel_results->get('ip')."' /></br>
      	                <input size='20' type='text' name='date_banned' value='".$mpsel_results->get('date_banned')."' /></td>
      	            <td style='border-bottom: 2pt solid black;'><input type='text' name='hostname' value='".$mpsel_results->get('hostname')."' /></br>
      	                <input type='text' name='date_expire' value='".$mpsel_results->get('date_expire')."' /></td>
      	            <td style='border-bottom: 2pt solid black;'><input size='50' type='text' name='notes' value='".$mpsel_results->get('notes')."' /></br>
      	                <input size='20' type='text' name='date_last' value='".$mpsel_results->get('date_last')."' /></td>
      	            <td style='border-bottom: 2pt solid black;'><input size='6'  type='text' name='count' value='".$mpsel_results->get('count')."' /></br>
      	                <input type='submit' value ='Update'></td></tr></table></form>";
		  }//while
		  $out .= "";
		  $out .= "<form action = '$PHP_SELF' method='post'><input type='hidden' name = 'post_spam_add' value='post_spam_add'><input type='submit' value ='Add'></form>";
		 
		  echo $out;  
		      
  }//end form_spam	
/*******************************************************************/
/*******************************************************************/

	
?>