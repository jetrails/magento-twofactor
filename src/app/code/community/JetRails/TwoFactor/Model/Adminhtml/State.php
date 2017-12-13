<?php

	/**
	 * State.php - 
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_State extends Mage_Core_Model_Abstract {

		/**
		 * These constants define the different states of authentication that the user can be in.
		 * They are used throughout the module and accessed statically.
		 */
		const SCAN = 0;
		const BACKUP = 1;
		const VERIFY = 2;
		const BANNED = 3;
		const HIDDEN = 4;

		/**
		 *
		 *
		 *
		 * 
		 * @param 		int 				state 				State defined from constants above
		 * @return      string 									The state descriptor as string
		 */
		public function getString ( $state ) {
			// Based on the state, return the state descriptor
			switch ( $state ) {
				case self::SCAN: 	return "REQUIRES SETUP";
				case self::BACKUP: 	return "REQUIRES SETUP";
				case self::VERIFY: 	return "COMPLETED";
				case self::BANNED:  return "TEMP BAN";
				default: 			return "";
			}
		}

		/**
		 *
		 *
		 *
		 *
		 * 
		 * @param 		int 				state 				State defined from constants above
		 * @return      string 									The state severity as string
		 */
		public function getSeverity ( $state ) {
			// Based on the state, return the severity as a string
			switch ( $state ) {
				case self::SCAN: 	return "minor";
				case self::BACKUP: 	return "minor";
				case self::VERIFY: 	return "notice";
				case self::BANNED:  return "critical";
				default: 			return "";
			}
		}

	}