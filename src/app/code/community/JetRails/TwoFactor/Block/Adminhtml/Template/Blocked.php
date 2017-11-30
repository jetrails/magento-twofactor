<?php

	/**
	 * Blocked.php - This template block is used in hand with the associated template file and it
	 * is used to prepare information for the template file.
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Template
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Template_Blocked extends Mage_Adminhtml_Block_Template {

		/**
		 * This method interfaces with the authentication model and returns the statically defined
		 * constant that defines the max number of failed authentication requests.
		 * @return
		 */
		public function getMaxAttempts () {
			// Get authentication model and return max attempts constant
			$auth = Mage::getSingleton ("twofactor/auth");
			return $auth::MAX_ATTEMPTS;
		}

		/**
		 * This method interfaces with the authentication model and returns the statically defined
		 * constant that defines the number of time in minutes that a user is blocked for.
		 * @return
		 */
		public function getBlockTime () {
			// Get authentication model and return block time constant
			$auth = Mage::getSingleton ("twofactor/auth");
			return $auth::BLOCK_TIME_MINUTES;
		}

	}