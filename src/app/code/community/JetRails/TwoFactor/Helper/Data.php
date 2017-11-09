<?php

	/**
	 * Data.php - This class contains a method that is used throughout the module.  It essentially
	 * checks to see if the logged in admin user should be authenticated.  Since the ACL permission
	 * is currently located in the system configuration section, it may change in the future and it
	 * can be changed in one place.
	 * @version         1.0.6
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
			// Allow users who's role includes twofactor authentication access to this controller
			$session = Mage::getSingleton ("admin/session");
			$resourceLookup = "admin/system/config/jetrails_twofactor";
			$resourceId = $session->getData ("acl")->get ( $resourceLookup )->getResourceId ();
			return $session->isAllowed ( $resourceId );
		}

	}