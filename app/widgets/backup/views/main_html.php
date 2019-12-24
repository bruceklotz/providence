<?php
/********************************************************************************/
/*			                                                          							*/
/*	/app/widgets/backup/views/main_html.php 			                              */
/*	Bruce Klotz - PELHAMHS.ORG						                                      */
/*										                                                          */
/*	Requires:								                                                    */
/*	/app/config/local/backup.conf						                                    */
/*	/app/controllers/administrate/maintenance/BackupSystemController.php	      */
/*	/themes/default/views/administrate/maintenance/backup_system_html.php	      */
/*	/app/widgets/backup 							                                          */
/*										                                                          */
/*	modified: navigation.conf, user_actions.conf				                        */
/*										                                                          */
/********************************************************************************/
/* ----------------------------------------------------------------------
 * main_html V 1.2.4
 * ----------------------------------------------------------------------
 */
 	$po_request 			  = $this->getVar('request');
	$va_instances			  = $this->getVar('instances');
	$va_settings			  = $this->getVar('settings');
	$vs_widget_id 		  = $this->getVar('widget_id');		
	$backupversion      =	$this->getVar('backupsystemver');
	$can_restore_system = $this->getVar('can_restore_system');
	$vs_message =  str_replace(" ","&nbsp;",$this->getVar('message')); // This prevents spaces from turning into +'s

  echo "<div class='dashboardWidgetContentContainer'>"
        . caNavButton($this->request,
                       __CA_NAV_ICON_GO__,
                       _t(" Make a Quick System Back Up."),
                       'backupIcon',
                       'administrate/maintenance', 'BackupSystem', 'Index', 
                       array('backupnotes' => $vs_message,'backup' => TRUE),
                       array(),
                       array('icon_position' => __CA_NAV_ICON_ICON_POS_LEFT__,
                             'use_class' => 'list-button',
                             'no_background' => true,
                             'dont_show_content' => false
                       )
                     )."</div>";
	?>