<?php
/* ----------------------------------------------------------------------
 * controllers/SubmissionsController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2022 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
require_once(__CA_LIB_DIR__.'/Service/GraphQLServiceController.php');
 
 	class SubmissionsController extends ActionController {
 		# -------------------------------------------------------
 		public function __construct(&$request, &$response, $view_paths=null) {
 			parent::__construct($request, $response, $view_paths);
 			
 			AssetLoadManager::register('react');
 			
 			if(!$this->request->isLoggedIn() || (!$this->request->getUser()->canDoAction('can_manage_user_media_submissions'))) {
 				throw new ApplicationException("No access");
 			}
 		}
 		# -------------------------------------------------------
		/** 
		 * 
		 */
 		public function Index() {
 			// API key
			$this->view->setVar('key', GraphQLServices\GraphQLServiceController::encodeJWTRefresh(['id' => $this->request->user->getPrimaryKey()]));
			
 		
 			$this->render("submission_index_html.php");	
 		}
 		# ------------------------------------------------------
 		/**
 		 * 
 		 */
 		public function Info() {
 			return $this->render('widget_sumbission_info_html.php', true);
 		}
 		# -------------------------------------------------------
 	}
