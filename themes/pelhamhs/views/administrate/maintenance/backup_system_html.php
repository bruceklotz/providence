<?php
/********************************************************************************/
/*                                                                              */
/*	/themes/default/views/administrate/maintenance/backup_system_html.php       */
/*	Bruce Klotz - PELHAMHS.ORG                                                  */
/*                                                                              */
/*	Requires:                                                                   */
/*	/app/config/local/backup.conf                                               */
/*	/app/controllers/administrate/maintenance/BackupSystemController.php        */
/*	/themes/default/views/administrate/maintenance/backup_system_html.php       */
/*	/app/widgets/backup                                                         */
/*      /themes/default/css/local.css                                           */
/*                                                                              */
/*	modified: navigation.conf, user_actions.conf                                */
/*                                                                              */
/********************************************************************************/

 $backup_system_htmlVer ="1.3.39 theme:default"; //11.20.16
 
 
 echo"
 	<SCRIPT TYPE='text/javascript'>
 	<!--
  	function confirmToContinue(Msg){ if ( confirm(Msg) ){ return true; } return false; }   
  -->
	</script>";
	
echo"
<style type='text/css' media='all'>
	table.dirtable {
    		background-color: #CDCDCD;
    		margin:10px 0pt 10px;
    		font-size: 8pt;
    		width: 100%;
    		text-align: left;
    		clear:both;
    		border-collapse: collapse;
}   
	table.dirtable tr, table.dirtable tr td, table.dirtable tr th {
		border: 1px solid #ccc;
}
	table.dirtable tbody td {
    		color: #3D3D3D;
    		padding: 1px;
    		background-color: #FFF;
    		vertical-align: middle;
    		width:42px;
}

	table.dirtable tbody td .fa {
		/*padding-left:5px;*/
		display:inline-block;
	}
	.note {font-size:7pt;font-style:italic;}
	.showall {display:inline-block;}
 a.prevnext,a:link.prevnext,a:active.prevnext,a:visited.prevnext {
         text-decoration:none;font-size:25px;color:green;vertical-align:middle; }
</style>";

	$backupversion =	$this->getVar('backupsystemver');
	$backupconfigversion =	__PHS_BACKUP_CONF_V__;
	$can_restore_system = $this->getVar('can_restore_system');
	$can_upgrade_system = $this->getVar('can_upgrade_system');
	$restoreSQLpath=	$this->getVar('restoreSQLpath');
	$backuprestorepath=	$this->getVar('backuprestorepath');
	global $pagesize;
	$pagesize=35;
		
	function listdir($request,$myDir,$showallfiles,$can_restore_system, $can_upgrade_system,$restoreSQLpath,$backuprestorepath){
		global $pagesize; 
		if(!$page =$request->getParameter('page', pInteger)){$page=1;}
		// set the displayed text for the Empty Database requirement. 
		if(__PHS_BACKUP_RESTORE_SQL_DATABASE_EMPTY__){$requireempty="yes";}else{$requireempty="no";}
		
 		// set the icons and status text for short/long file list
 		if($showallfiles==0){$pagesize=35;
 			$showall = caNavButton($request,__CA_NAV_ICON_EXPAND__, _t("[ All ]"), '', 'administrate/maintenance', 'BackupSystem','Index', array('showallfiles'=>1,'icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button'),array('title'=>'Show All Files')); 
 		}else{$pagesize=1000;
 			$showall = caNavButton($request, __CA_NAV_ICON_COLLAPSE__, _t("[Page]"), '', 'administrate/maintenance', 'BackupSystem','Index', array('showallfiles'=>0,'icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true),array('title'=>'Hide Files')); 
 		}
 	
 		// read the passed directory into an array.
 		$myDirectory = opendir($myDir);
		while($entryName = @readdir($myDirectory)) {	$dirArray[] = $entryName;	}
		@closedir($myDirectory);

		$listdir = "<div>";

		// sort 'em
		@rsort($dirArray);
		
		$itemcount=0;
		$countmin = ($page - 1) * $pagesize;
		$countmax = $countmin + $pagesize;

		// print 'em
	
		// loop through the array of files and print them all
		//for($index=0; $index < $indexCount; $index++) {
	        if($dirArray){	
		   foreach ($dirArray as $index=>$file){ 
			if (preg_match("/(.sql|.zip|.tgz)$/i", $file)){  
	    
			$itemcount++;					
			if(($countmin <= $itemcount) and ($itemcount <= $countmax)){					
								
								$listdir .= "<tr><td style='width:400px;' >$dirArray[$index]";
								
								// now display the notes file's content...or if it is already a txt file, then its contents.
								$notesfile = $myDir."/".$dirArray[$index];
								if(substr("$dirArray[$index]", -3) != "txt"){$notesfile .= ".txt";} //if it is already a .txt 
								
								$listdir .= "<div class='note'>".@file_get_contents($notesfile)."</div></td>";
								$listdir .= "<td>".filetype($myDir."/".$dirArray[$index])."</td>";
								$listdir .= "<td style='text-align:right;'>".number_format(filesize($myDir."/".$dirArray[$index]))."</td>";
								$listdir .= "<td style='width:85px;'>".date("F d Y H:i:s.", filectime($myDir."/".$dirArray[$index]))."</td>";
								
								$listdir .= "<td style='width:100px'>";
								if ($can_restore_system){
									$listdir .= "<span Onclick='return confirmToContinue(\"Delete $dirArray[$index]?\");'>". caNavButton($request, __CA_NAV_ICON_DELETE__, '', '', 'administrate/maintenance', 'BackupSystem','Index', array('delete'=>$dirArray[$index],'page'=>$page,'icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true),array('title'=>'DELETE Backup'))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>"; 
									
									$listdir .= "<span Onclick='return confirmToContinue(\"Download $dirArray[$index]?\");'>".caNavButton($request, __CA_NAV_ICON_DOWNLOAD__, '', '', 'administrate/maintenance', 'BackupSystem','Index', array('save'=>$dirArray[$index],'icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true),array('title'=>'Download Backup'))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>"; 
									$listdir .= "<span Onclick='return confirmToContinue(\"RESTORE $dirArray[$index]? \\n \\n******************************************************************\\n****     WARNING!!!! THIS CAN NOT BE UNDONE!!!!    ****\\n******************************************************************\\n\\nRESTORE DATABASE: $restoreSQLpath \\nRESTORE PATH: $backuprestorepath\\nREQUIRE EMPTY DATABASE: $requireempty \");'>".caNavButton($request, __CA_NAV_ICON_CHANGE__,'', '', 'administrate/maintenance', 'BackupSystem','Index', array('restore'=>$dirArray[$index],'icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true),array('title'=>'Restore Backup'))."</span>"; 
									
								}
									
								$listdir .= "</td>";
								$listdir .= "</tr>";
						}// if...
					}//if page..	
		   }//foreach
		}//if($dirArray...

    // paging...
    $pagecount = round(($itemcount/$pagesize)+.5);	// how many pages do we have
		$nextpage=$page+1;if($nextpage>$pagecount){$nextpage=$pagecount;} 
 		$prevpage=$page-1;if($prevpage<1){$prevpage=1;}
 		if($showallfiles){$countmax=$itemcount;}

 		//$showpage = caNavButton($request, __CA_NAV_ICON_COLLAPSE__, _t(""), '', 'administrate/maintenance', 'BackupSystem','Index', array('page'=>$prevpage,'icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true),array('title'=>'Previous Page')); 
 		$showpage .=  caNavLink($request, _t('< '), 'prevnext', 'administrate/maintenance', 'BackupSystem','Index', array('page'=>$prevpage),array('title'=>'Previous Page: '.$prevpage)); 
 		$showpage .="Page: ";

 		for($pagenumber=1;$pagenumber<=$pagecount;$pagenumber++){
       if($pagenumber == $page){$showpage .= "$page - ";}else{ 			 		
 			 		$showpage .=" ". caNavLink($request, _t($pagenumber), '', 'administrate/maintenance', 'BackupSystem','Index', array('page'=>$pagenumber),array('title'=>$pagenumber))." - "; 
 			 }
 		}
 		$showpage .= caNavLink($request, _t(' >'), 'prevnext', 'administrate/maintenance', 'BackupSystem','Index', array('page'=>$nextpage),array('title'=>'Next Page: '.$nextpage)); 
 			 
 		// caNavButton($request, __CA_NAV_ICON_EXPAND__, _t(""), '', 'administrate/maintenance', 'BackupSystem','Index', array('page'=>$nextpage,'icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__, 'use_class' => 'list-button', 'no_background' => true, 'dont_show_content' => true),array('title'=>'Next Page')); 
 		if($countmax>$itemcount){$countmax=$itemcount;}//this prevents the count from displaying larger than the max.
 		$showpage =$showall.$showpage."<div style='color:blue;'>[$countmin-$countmax of $itemcount ]</div>";
		// $showpage .=  $showall;
		$listhead= "<tr><th colspan=2><b>Backup Storage Path: </b><i>$myDir</i></th><th colspan=3>$showpage</th></tr>
			     <tr><th>Filename</th><th>Filetype</th><th>Filesize</th><th style='width:85px;'>Date/Time</th><th style='width:200px;'></th></tr>\n";
	
		$listout .= "<table class='dirtable'>$listhead $listdir</table></div>";	
 		return $listout;
  }//function
	// set the displayed text for the Empty Database requirement. 
		if(__PHS_BACKUP_RESTORE_SQL_DATABASE_EMPTY__){$requireempty="yes";}else{$requireempty="no";}
//$page=2;
	echo "<div class='sectionBox'>
				<h1>"._t("Backup System")."</h1>\n"; 
	echo"<div> <b>V$backupversion </b>( $backup_system_htmlVer )<b>Config:</b>$backupconfigversion</div>
			 <div><b> Backup Storage Path:</b> ".$this->getVar('backuppath')."<br/><b>Restore Backup To Path:</b> $backuprestorepath <br/>
			 <b>Restore Database:</b> $restoreSQLpath <b> Require Empty Database: </b>$requireempty</div>";
			 
	echo "<div>".$this->getVar('statusmessage')."</div><br/><br/><br/>";
	echo listdir($this->request,$this->getVar('backuppath'),$this->request->getParameter('showallfiles', pString),$can_restore_system,$can_upgrade_system,$restoreSQLpath,$backuprestorepath);
	print caFormTag($this->request, 'Index', 'backup', null, 'post', 'multipart/form-data', '_top', array('backup' => true)); 
	
	print caFormControlBox(
		'<div class="simple-search-box">'._t('Notes').': <input type="text" id="backupnotes" name="backupnotes" value="'.'" size="50"/>
				<input type="hidden" id="backup" name="backup" value="1" />','',
				caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Backup System"), 'backup',array('title'=>'Restore Backup'))
	);
       
       echo "</div></form>";
       
       print caFormTag($this->request, 'Index', 'upgradebackup', null, 'post', 'multipart/form-data', '_top', array('upgradebackup' => true)); 	
       print caFormControlBox(
		'<div class="simple-search-box">'._t('Notes').': <input type="text" id="backupnotes" name="backupnotes" value="'.'" size="50"/>
		<input type="hidden" id="upgradebackup" name="upgradebackup" value="1" />','',
				caFormSubmitButton($this->request, __CA_NAV_ICON_GO__, _t("Upgrade Backup System"), 'upgradebackup',array('title'=>'Upgrade Backup'))
	);
        echo "</div></form>";
 	
 
  
  
	echo "<div>".$this->getVar('message')."</div><br/><br/><br/>";
 	echo "</div>
	      <div class='editorBottomPadding'><!-- empty --></div>";
?>	      