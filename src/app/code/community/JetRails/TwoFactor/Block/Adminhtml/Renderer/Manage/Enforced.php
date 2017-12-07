<?php

	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Manage_Enforced extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		public function render ( Varien_Object $row ) {
			// Get the admin user object
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			// Load the authentication entry
			$auth = Mage::getModel ("twofactor/auth")
				->load ( $row->getUserId () )
				->setId ( $row->getUserId () );

			$enforceString = "N/A";
			$enforceClass = "grid-severity-major";
			switch ( $auth->getEnforced () ) {
				case $auth::ENFORCED_NO:
					$enforceString = "NOT ENFORCED";
					$enforceClass = "grid-severity-major";
					break;
				case $auth::ENFORCED_YES:
					$enforceString = "ENFORCED";
					$enforceClass = "grid-severity-notice";
					break;
			}
			return "<span class='$enforceClass' ><span>$enforceString</span></span>";
		}

	}