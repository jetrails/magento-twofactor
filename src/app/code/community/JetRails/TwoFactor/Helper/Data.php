<?php

	/**
	 * Data.php - This class contains a method that is used throughout the module.  It essentially
	 * checks to see if the logged in admin user should be authenticated. This method looks into
	 * the authentication model to see if a super admin turned on two-factor authentication for the
	 * user. This class also contains methods to get the configuration data which are extracted
	 * using Magento's getConfig method and supplying the with the XPATH constants which are defined
	 * internally.
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Helper
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Helper_Data extends Mage_Core_Helper_Abstract {

		/**
		 * These constants are used to define the XPATH in the XML config file. These values define
		 * the custom configuration that this plugin has.
		 */
		const XPATH_SCOPE = "default";
		const XPATH_REMEMBER_ME = "twofactor/general/remember_me";
		const XPATH_BAN_ATTEMPTS = "twofactor/general/ban_attempts";
		const XPATH_BAN_TIME = "twofactor/general/ban_time";

		/**
		 * This method checks to see if two-factor authentication is enabled for the currently
		 * logged in user.  It is used in the observer class to ensure proper ACL use.
		 * @return      boolean                                 Should the user be authenticated
		 */
		public function isAllowed () {
			// Load all the models necessary to determine status
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			$status = Mage::getModel ("twofactor/status");
			$auth = Mage::getModel ("twofactor/auth");
			// Load the authentication model, and return if the status is enabled
			$auth->load ( $admin->getUserId () );
			$auth->setId ( $admin->getUserId () );
			return $status->isEnabled ( $auth->getStatus () );
		}

		/**
		 * This method returns the values of all the configuration values as determined by the XPATH
		 * constants in this class.
		 * @return      array                                   Associative array of config values
		 */
		public function getData () {
			// Load the configuration and define the prefix
			$config = Mage::getConfig ();
			$prefix = self::XPATH_SCOPE . "/";
			// Get all values for all the defined XPATH constants
			$rememberMe = $config->getNode ( $prefix . self::XPATH_REMEMBER_ME );
			$banAttempts = $config->getNode ( $prefix . self::XPATH_BAN_ATTEMPTS );
			$banTime = $config->getNode ( $prefix . self::XPATH_BAN_TIME );
			// Return an associative array with all values defined in it
			return array (
				"remember_me" => $rememberMe,
				"ban_attempts" => $banAttempts,
				"ban_time" => $banTime
			);
		}

	}