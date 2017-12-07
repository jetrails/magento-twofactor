<?php

	/**
	 * Notify.php - This class contains methods that help log and communicate messages.  Messages
	 * can be logged into a custom log file, the logged in admin user can be notified, or all admin
	 * users in the 'Administrators' role can be notified.
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Notify extends Mage_Core_Model_Abstract {

		/**
		 * This method takes in an email, id, and IP address, and it logs it into a custom log file
		 * using a format string.
		 * @param       string              email               Email address to log
		 * @param       integer             id                  Admin user id
		 * @param       string              address             Last IP address for failed auth
		 * @return      void
		 */
		public function logAccountBlock ( $email, $id, $address ) {
			// Construct entry message
			$message = sprintf (
				Mage::helper ("twofactor")->__("Blocked %s with user id %d on ip address %s"),
				$email,
				$id,
				$address
			);
			// Append log entry to module log
			Mage::log ( $message, Zend_Log::WARN, "jetrails.twofactor.log", true );
		}

		/**
		 * This method finds all users in the 'Administrators' role, gets their contact information
		 * and sends the supplied message to them.
		 * @param       string              message             HTML message to send with email
		 * @return      void
		 */
		public function emailAllAdministrators ( $message ) {
			// Get the authentication model and admin role model
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			$auth = Mage::getModel ("twofactor/auth")
				->load ( $admin->getUserId () )
				->setId ( $admin->getUserId () );
			$role = Mage::getModel ("admin/role");
			// Get the role ID for administrator role
			$roleId = $role
				->getCollection ()
				->addFieldToFilter ( "role_name", array ( "eq" => "Administrators" ) )
				->getFirstItem ()
				->getId ();
			// Get the users that belong to the administrator role
			$roleUsers = $role
				->getCollection ()
				->addFieldToFilter ( "parent_id", array ( "eq" => $roleId ) );
			// Loop through all the users belonging to the role
			foreach ( $roleUsers as $roleUser ) {
				// Based on the admin user, format their full name
				$user = Mage::getModel ("admin/user")->load ( $roleUser->getUserId () );
				$fullName  = ucfirst ( $user->getFirstname () ) . " ";
				$fullName .= ucfirst ( $user->getLastname () );
				// Initialize email object and send it after setting options
				$mail = new Zend_Mail ();
				$mail->setSubject ( Mage::helper ("twofactor")->__("An account was blocked") );
				$mail->setBodyHtml ( $message );
				$mail->setFrom (
					"twofactor@jetrails.com",
					Mage::helper ("twofactor")->__("JetRails TwoFactor")
				);
				$mail->addTo ( $user->getEmail (), $fullName );
				$mail->send ();
			}
		}

		/**
		 * This method takes in an HTML message and sends it to the current logged in user's email.
		 * This is to notify the rightful user that their account may be compromised.
		 * @param       string              message             HTML message to send with email
		 * @return      void
		 */
		public function emailUser ( $message ) {
			// Format logged in admin user's full name
			$user = Mage::getSingleton ("admin/session")->getUser ();
			$fullName  = ucfirst ( $user->getFirstname () ) . " ";
			$fullName .= ucfirst ( $user->getLastname () );
			// Initialize email object and send it after setting options
			$mail = new Zend_Mail ();
			$mail->setSubject (
				Mage::helper ("twofactor")->__("Account blocked, failed two-factor authentication")
			);
			$mail->setBodyHtml ( $message );
			$mail->setFrom (
				"twofactor@jetrails.com",
				Mage::helper ("twofactor")->__("JetRails TwoFactor")
			);
			$mail->addTo ( $user->getEmail (), $fullName );
			$mail->send ();
		}

	}