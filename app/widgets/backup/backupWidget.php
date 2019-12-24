<?php
/********************************************************************************/
/*										                                                          */
/*	/app/widgets/backup/backupWidget.php		                              			*/
/*	Bruce Klotz - PELHAMHS.ORG			V1.4.10                                  		*/
/*									                                                          	*/
/*	Requires:							                                                    	*/
/*	/app/config/local/backup.conf		                                    				*/
/*	/app/controllers/administrate/maintenance/BackupSystemController.php        */
/*	/themes/default/views/administrate/maintenance/backup_system_html.php	      */
/*	/app/widgets/backup 						                                          	*/
/*									                                                          	*/
/*	modified: navigation.conf, user_actions.conf			                        	*/
/*									                                                          	*/
/********************************************************************************/
/*
 * Creates a quick link to backing up
 * ----------------------------------------------------------------------
 */
 	require_once(__CA_LIB_DIR__.'/BaseWidget.php');
 	require_once(__CA_LIB_DIR__.'/IWidget.php');
 	
 	if(file_exists( __CA_CONF_DIR__.'/local/backup.conf')){
 	    require_once( __CA_CONF_DIR__.'/local/backup.conf');
 	}elseif(file_exists(__CA_CONF_DIR__.'/backup.conf')){
 	    require_once( __CA_CONF_DIR__.'/backup.conf');
 	}else{echo "BackupWidget.php L27 -<b> ERROR - backup.conf NOT found!</b>";}
 
	class backupWidget extends BaseWidget implements IWidget {
		# -------------------------------------------------------
		private $opo_config;
		static $s_widget_settings = array();
		# -------------------------------------------------------
		public function __construct($ps_widget_path, $pa_settings) {
			$this->title = _t('Backup');
			$this->description = _t('Shortcut to make a backup');
			parent::__construct($ps_widget_path, $pa_settings);
			$this->opo_config = Configuration::load($ps_widget_path.'/conf/backupWidget.conf');
		}
		# -------------------------------------------------------
		/**
		 * Override checkStatus() to return true
		 */
		public function checkStatus() {
			return array(
				'description' => $this->getDescription(),
				'errors' => array(),
				'warnings' => array(),
				'available' => ((bool)$this->opo_config->get('enabled'))
			);
		}
		# -------------------------------------------------------
		/**
		 *
		 */
		public function renderWidget($ps_widget_id, &$pa_settings) {
				parent::renderWidget($ps_widget_id, $pa_settings);
			$this->opo_view->setVar('request', $this->getRequest());
			$message =str_replace("%_user_%",$this->request->user->getName(),$pa_settings['message']) ; // Replace %_user_% with username
			$message =str_replace("%_datetime_%",date('D M d Y h:i A'),$message ); // Replace %_datetime_% with username
			$this->opo_view->setVar('message', $message);
			return $this->opo_view->render('main_html.php');
		}
		# -------------------------------------------------------
			/**
		 * Add widget user actions
		 */
		public function hookGetRoleActionList($pa_role_list) {
			$pa_role_list['widget_message'] = array(
				'label' => _t('Backup widget'),
				'description' => _t('Actions for backup widget'),
				'actions' => backupWidget::getRoleActionList()
			);

			return $pa_role_list;
		}
		
		/**
		 * Get widget user actions
		 */
		static public function getRoleActionList() {
				return array(
				'can_edit_message' => array(
					'label' => _t('Can edit backup note'),
					'description' => _t('User can edit system-wide backup note in backup widget.')
				)
			);
		}
		# -------------------------------------------------------
	}
	BaseWidget::$s_widget_settings['backupWidget'] = array(
			'message' => array(
				'formatType' => FT_TEXT,
				'displayType' => DT_FIELD,
				'width' => 55, 'height' => 3,
				'takesLocale' => false,
				'default' => '',
				'label' => _t('Quick Backup Message'),
				'scope' => 'application',
				'requires' => 'can_backup_system',
				'description' => _t('Message for Quick Backup. %user%, %datetime%  ')
			),
	);
	
?>