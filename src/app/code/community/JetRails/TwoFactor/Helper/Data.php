<?php

	/**
	 * Data.php - This class contains a method that is used throughout the module.  It essentially
	 * checks to see if the logged in admin user should be authenticated.  Since the ACL permission
	 * is currently located in the system configuration section, it may change in the future and it
	 * can be changed in one place.
	 *
	 *
	 *
	 *
	 * 
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Helper
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Helper_Data extends Mage_Core_Helper_Abstract {

		/**
		 * 
		 */
		const XPATH_SCOPE = "default";
		const XPATH_REMEMBER_ME = "twofactor/general/remember_me";
		const XPATH_BAN_ATTEMPTS = "twofactor/general/ban_attempts";
		const XPATH_BAN_TIME = "twofactor/general/ban_time";

		/**
		 *
		 *
		 *
		 *
		 * 
		 * @return      boolean                                 Should the user be authenticated
		 */
		public function isAllowed () {

			$admin = Mage::getSingleton ("admin/session")->getUser ();
			$status = Mage::getModel ("twofactor/status");
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $admin->getUserId () );
			$auth->setId ( $admin->getUserId () );
			return $status->isEnabled ( $auth->getStatus () );
		}

		/**
		 * 
		 * @return   	object
		 */
		public function getData () {
			$config = Mage::getConfig ();
			$prefix = self::XPATH_SCOPE . "/";

			$rememberMe = $config->getNode ( $prefix . self::XPATH_REMEMBER_ME );
			$banAttempts = $config->getNode ( $prefix . self::XPATH_BAN_ATTEMPTS );
			$banTime = $config->getNode ( $prefix . self::XPATH_BAN_TIME );

			return array (
				"remember_me" => $rememberMe,
				"ban_attempts" => $banAttempts,
				"ban_time" => $banTime
			);
		}

	}