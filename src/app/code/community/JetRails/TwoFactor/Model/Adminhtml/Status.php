<?php

	/**
	 * State.php - 
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Status extends Mage_Core_Model_Abstract {

		/**
		 * These constants define weather or not 2FA is 
		 */
		const ENABLED = 0;
		const DISABLED = 1;

		/**
		 * 
		 * @param
		 * @return 
		 */
		public function isEnabled ( $status ) {
			// Check if status is enabled
			return $status === self::ENABLED;
		}

		public function getString ( $status ) {
			// Based on the status, return string equivalent
			return $this->isEnabled ( $status ) ? "ENABLED" : "DISABLED";
		}

		public function getSeverity ( $status ) {
			// Based on the status, return severity of option
			return $this->isEnabled ( $status ) ? "notice" : "critical";
		}

	}