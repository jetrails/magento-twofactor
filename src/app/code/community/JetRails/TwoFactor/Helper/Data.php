<?php

	/**
	 * Data.php - This class contains a method that is used throughout the module.  It essentially
	 * checks to see if the logged in admin user should be authenticated.  Since the ACL permission
	 * is currently located in the system configuration section, it may change in the future and it
	 * can be changed in one place.
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Helper
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Helper_Data extends Mage_Core_Helper_Abstract {

		/**
		 * This method checks to see if the logged in admin user should be authenticated based on
		 * the ACL.  The authentication permission is located under the system config section of the
		 * admin area.  This method is used for every controller and observer.
		 * @return      boolean                                 Should the user be authenticated
		 */
		public function isAllowed () {
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $admin->getUserId () );
			$auth->setId ( $admin->getUserId () );
			return $auth->getEnforced () === $auth::ENFORCED_YES;
		}

	}