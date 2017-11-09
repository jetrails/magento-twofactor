<?php

	/**
	 * Admin.php - This block is meant to be used inline with the email template file.  It simply
	 * has a method that is called to fill the plain text body portion of the HTML email.  This
	 * block describes the message that the admin users within the 'Administrators' role will see.
	 * @version         1.0.6
	 * @package         JetRails® TwoFactor
	 * @category        Email
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Email_Admin extends Mage_Adminhtml_Block_Template {

		/**
		 * This method is used within a template file that defines the HTML structure of an email.
		 * This method simply returns the plain text message that will be populated inside the email
		 * body.
		 * @return      string                                  Email plain text body
		 */
		public function getMessage () {
			// Get the authentication model and admin user instance
			$auth = Mage::getSingleton ("twofactor/auth");
			$user = Mage::getSingleton ("admin/session")->getUser ();
			// Return the formated message that will appear in the email body
			return sprintf (
				$this->__(
					"%d unsuccessful two factor authentication attempts have been made for '%s' w" .
					"ith a user id of '%d'.  Last detected authentication attempt was detected on" .
					" '%s' at '%s UTC'.  A %d minute block has been set to the account.  The acco" .
					"unt in question and all other admins in the 'Administrator' role have been n" .
					"otified about this block."
				),
				$auth::MAX_ATTEMPTS,
				$user->getEmail (),
				$user->getUserId (),
				$auth->getLastAddress (),
				$auth->getLastTimestamp (),
				$auth::BLOCK_TIME_MINUTES
			);
		}

	}