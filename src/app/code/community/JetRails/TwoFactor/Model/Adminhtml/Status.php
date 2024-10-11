<?php

	/**
	 * State.php - This class contains the model that defines the status of a user's authentication.
	 * This basically means if the status is set to enabled for a user, then two-factor
	 * authentication is enforced for the user.  The values as they relate to the user can be found
	 * in the authentication model.
	 * @version         1.1.5
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Status extends Mage_Core_Model_Abstract {

		/**
		 * These constants define the two states that the status can be in. The status can be
		 * enabled or disabled.  If the status is enabled then two-factor authentication is enforced
		 * on the user.  The value for the status as it effects the user can be found in the
		 * authentication model's table.
		 */
		const ENABLED = 0;
		const DISABLED = 1;

		/**
		 * This method takes in a status and simply checks to see if the value is associated with
		 * the status being enabled.
		 * @param       integer             status              Status to evaluate
		 * @return      boolean                                 Is the passed status enabled?
		 */
		public function isEnabled ( $status ) {
			// Check if status is enabled
			return $status === self::ENABLED;
		}

		/**
		 * This method takes in a status and based on the status, it returns the string value that is
		 * associated with this passed status.  This value will be displayed inside a badge in the
		 * manage page for this plugin.
		 * @param       integer             status              Status to evaluate
		 * @return      string                                  String representation of status
		 */
		public function getString ( $status ) {
			// Based on the status, return string equivalent
			return $this->isEnabled ( $status ) ? "ENABLED" : "DISABLED";
		}

		/**
		 * This method takes in a status and bases on the status, it returns the severity value that
		 * is associated with the status.
		 * @param       integer             status              Status to evaluate
		 * @return      string                                  Severity string in terms of status
		 */
		public function getSeverity ( $status ) {
			// Based on the status, return severity of option
			return $this->isEnabled ( $status ) ? "notice" : "critical";
		}

	}
