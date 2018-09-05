<?php

	/**
	 * Banned.php - This template block is used in hand with the associated template file and it
	 * is used to prepare information for the template file.
	 * @version         1.1.2
	 * @package         JetRails® TwoFactor
	 * @category        Template
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Template_Banned extends Mage_Adminhtml_Block_Template {

		/**
		 * This method gets an instance of the default data helper for this module.  Using this
		 * helper, we get the configured number of failed authentication requests that a user has
		 * before said user is banned.
		 * @return      int                                     Max number of failed auth attempts
		 */
		public function getBanAttempts () {
			// Get instance of data helper and return max ban attempts
			$data = Mage::helper ("twofactor/data");
			return $data->getData () ["ban_attempts"];
		}

		/**
		 * This method gets an instance of the default data helper for this module.  Using this
		 * helper, we get the configured number of minutes that a temporary ban lasts for
		 * @return      int                                     Minutes a temporary ban lasts for
		 */
		public function getBanTime () {
			// Get instance of data helper and return temp ban time in minutes
			$data = Mage::helper ("twofactor/data");
			return $data->getData () ["ban_time"];
		}

	}
