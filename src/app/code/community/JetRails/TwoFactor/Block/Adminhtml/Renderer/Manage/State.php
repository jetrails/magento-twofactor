<?php

	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Manage_State extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		public function render ( Varien_Object $row ) {
			// Get the admin user object
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			// Load the authentication entry
			$auth = Mage::getModel ("twofactor/auth")
				->load ( $row->getUserId () )
				->setId ( $row->getUserId () );


			$stateString = "N/A";
			$stateClass = "grid-severity-major";
			switch ( $auth->getState () ) {
				case $auth::STATE_SCAN:
					$stateString = "NEEDS SETUP";
					$stateClass = "grid-severity-major";
					break;
				case $auth::STATE_BACKUP:
					$stateString = "CONFIRM BACKUP CODES";
					$stateClass = "grid-severity-minor";
					break;
				case $auth::STATE_VERIFY:
					$stateString = "SUCCESSFULLY SETUP";
					$stateClass = "grid-severity-notice";
					break;
				case $auth::STATE_BLOCKED:
					$stateString = "TEMPORARILY BLOCKED";
					$stateClass = "grid-severity-critical";
					break;
			}
			return "<span class='$stateClass' ><span>$stateString</span></span>";
		}

	}