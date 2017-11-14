<?php

	/**
	 * User.php - This block is meant to be used inline with the email template file.  It simply
	 * has a method that is called to fill the plain text body portion of the HTML email.  This
	 * block describes the message that the admin who's account is currently being used will see.
	 * @version         1.0.8
	 * @package         JetRails® TwoFactor
	 * @category        Email
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Email_User extends Mage_Adminhtml_Block_Template {

		/**
		 * This method is used within a template file that defines the HTML structure of an email.
		 * This method simply returns the plain text message that will be populated inside the email
		 * body.
		 * @return      string                                  Email plain text body
		 */
		public function getMessage () {
			// Get the authentication model
			$auth = Mage::getSingleton ("twofactor/auth");
			// Return the formated message that will appear in the email body
			return sprintf (
				$this->__(
					"Your Magento administrator account has been locked after %d unsuccessful two" .
					"-factor authentication attempts. Additional emails have been sent to the adm" .
					"inistrators regarding this failed authentication attempt. If this was you, t" .
					"ry logging into the system in %d minutes. If this was not you, please contac" .
					"t your system administrators immediately."
				),
				$auth::MAX_ATTEMPTS,
				$auth::BLOCK_TIME_MINUTES
			);
		}

	}