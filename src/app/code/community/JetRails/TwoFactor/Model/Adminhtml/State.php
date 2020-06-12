<?php

	/**
	 * State.php - This class contains the model that defines the state of a user's authentication.
	 * This basically means that based on the state of authentication, we can redirect user's to the
	 * proper pages to ensure that their two-factor authentication accounts are setup. The values as
	 * they relate to the user can be found in the authentication model.
	 * @version         1.1.4
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_State extends Mage_Core_Model_Abstract {

		/**
		 * These constants define the different states of authentication that the user can be in.
		 * They are used throughout the module and accessed statically.  The HIDDEN state is never
		 * set in the database and is never contained within the authentication model.  Instead it
		 * is used only for the state renderer for the manage account page.
		 */
		const SCAN = 0;
		const BACKUP = 1;
		const VERIFY = 2;
		const BANNED = 3;
		const HIDDEN = 4;

		/**
		 * This method takes in a state and based on the state, it returns the string value that is
		 * associated with this passed state.  This value will be displayed inside a badge in the
		 * manage page for this plugin.
		 * @param       int                 state               State defined from constants above
		 * @return      string                                  The state descriptor as string
		 */
		public function getString ( $state ) {
			// Based on the state, return the state descriptor
			switch ( $state ) {
				case self::SCAN:    return "REQUIRES SETUP";
				case self::BACKUP:  return "REQUIRES SETUP";
				case self::VERIFY:  return "COMPLETED";
				case self::BANNED:  return "TEMP BAN";
				default:            return "";
			}
		}

		/**
		 * This method takes in a state and bases on the state, it returns the severity value that
		 * is associated with the state.
		 * @param       int                 state               State defined from constants above
		 * @return      string                                  The state severity as string
		 */
		public function getSeverity ( $state ) {
			// Based on the state, return the severity as a string
			switch ( $state ) {
				case self::SCAN:    return "minor";
				case self::BACKUP:  return "minor";
				case self::VERIFY:  return "notice";
				case self::BANNED:  return "critical";
				default:            return "";
			}
		}

	}
