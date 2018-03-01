<?php

	/**
	 * Notify.php - This class contains methods that help log and communicate messages.  Messages
	 * can be logged into a custom log file, the logged in admin user can be notified, or all admin
	 * users in the 'Administrators' role can be notified.
	 * @version         1.1.1
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Notify extends Mage_Core_Model_Abstract {

		/**
		 * These constants define the module's custom log file name as well as all the different log
		 * messages that a user can append into that log file.
		 */
		const LOG_FILENAME          = "jetrails.twofactor.log";
		const LOG_AUTOMATIC_BAN     = "Automatically banned user '%s' while on IP address '%s'";
		const LOG_MANUAL_UNBAN      = "User '%s' manually removed temp ban for user '%s'";
		const LOG_MANUAL_ENABLE     = "User '%s' manually enabled 2FA for user '%s'";
		const LOG_MANUAL_DISABLE    = "User '%s' manually disabled 2FA for user '%s'";
		const LOG_MANUAL_RESET      = "User '%s' manually reset 2FA for user '%s'";

		/**
		 * This method takes in the log message type and the values to merge with the message. It
		 * then simply logs it into the module specific log file.
		 */
		public function log ( $type = "", $values = array () ) {
			// Construct the message and append to custom log file
			$message = vsprintf ( $type, $values );
			Mage::log ( $message, Zend_Log::WARN, self::LOG_FILENAME, true );
		}

		/**
		 * This method loads the twofactor_admin email template and sends it to every user that
		 * belongs to the Administrator role. Proper data is also passed to the template in order
		 * to give the admin user sufficient information.
		 * @return      void
		 */
		public function emailAllAdministrators () {
			// Get the authentication model and admin role model
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $admin->getUserId () );
			$auth->setId ( $admin->getUserId () );
			$role = Mage::getModel ("admin/role");
			// Get the role ID for administrator role
			$roleId = $role;
			$roleId = $roleId->getCollection ();
			$roleId = $roleId->addFieldToFilter ( "role_name", array ( "eq" => "Administrators" ) );
			$roleId = $roleId->getFirstItem ();
			$roleId = $roleId->getId ();
			// Get the users that belong to the administrator role
			$roleUsers = $role;
			$roleUsers = $roleUsers->getCollection ();
			$roleUsers = $roleUsers->addFieldToFilter ( "parent_id", array ( "eq" => $roleId ) );
			// Loop through all the users belonging to the role
			foreach ( $roleUsers as $roleUser ) {
				// Load the data helper class and get user instance
				$data = Mage::helper ("twofactor/data");
				$user = Mage::getModel ("admin/user")->load ( $roleUser->getUserId () );
				// Format timestamp date and time
				$timestamp = $auth->getLastTimestamp ();
				$timestampDate = "-";
				$timestampTime = "-";
				if ( $timestamp !== null ) {
					$timestampDate = Mage::getModel ("core/date")->date (
						"m/d/Y", strtotime ( $timestamp )
					);
					$timestampTime = Mage::getModel ("core/date")->date (
						"h:i:s A", strtotime ( $timestamp )
					);
				}
				// Construct the user contact's full name
				$fullName  = ucfirst ( $user->getFirstname () ) . " ";
				$fullName .= ucfirst ( $user->getLastname () );
				// Construct and send out ban notice email to user
				$template = Mage::getModel ("core/email_template")->loadDefault ("twofactor_admin");
				$template->setSenderName ("JetRails 2FA Module");
				$template->setType ("html");
				$template->setSenderEmail (
					Mage::getStoreConfig ("trans_email/ident_general/email")
				);
				$template->setTemplateSubject (
					Mage::helper ("twofactor")->__("2FA ban notice for user ") .
					$admin->getUsername ()
				);
				$test = $template->send ( $user->getEmail (), $fullName,
					array (
						"base_url" => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB ),
						"ban_attempts" => $data->getData () ["ban_attempts"],
						"ban_time" => $data->getData () ["ban_time"],
						"last_address" => $auth->getLastAddress (),
						"last_timestamp_date" => $timestampDate,
						"last_timestamp_time" => $timestampTime,
						"username" => $admin->getUsername (),
						"year" => date ("Y")
					)
				);
			}
		}

		/**
		 * This method loads the twofactor_user email template and sends it to the currently logged
		 * in user. Proper data is also passed to the template in order to give the user sufficient
		 * information.
		 * @return      void
		 */
		public function emailUser () {
			// Load the data helper class and get user instance
			$data = Mage::helper ("twofactor/data");
			$user = Mage::getSingleton ("admin/session")->getUser ();
			// Load the authentication model that belongs with logged in user
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $user->getUserId () );
			$auth->setId ( $user->getUserId () );
			// Construct the user contact's full name
			$fullName  = ucfirst ( $user->getFirstname () ) . " ";
			$fullName .= ucfirst ( $user->getLastname () );
			// Format timestamp date and time
			$timestamp = $auth->getLastTimestamp ();
			$timestampDate = "-";
			$timestampTime = "-";
			if ( $timestamp !== null ) {
				$timestampDate = Mage::getModel ("core/date")->date (
					"m/d/Y", strtotime ( $timestamp )
				);
				$timestampTime = Mage::getModel ("core/date")->date (
					"h:i:s A", strtotime ( $timestamp )
				);
			}
			// Construct and send out ban notice email to user
			$template = Mage::getModel ("core/email_template")->loadDefault ("twofactor_user");
			$template->setSenderEmail ( Mage::getStoreConfig ("trans_email/ident_general/email") );
			$template->setSenderName ("JetRails 2FA Module");
			$template->setType ("html");
			$template->setTemplateSubject (
				Mage::helper ("twofactor")->__("Your Magento admin account is locked")
			);
			$template->send ( $user->getEmail (), $fullName,
				array (
					"base_url" => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB ),
					"last_timestamp_date" => $timestampDate,
					"last_timestamp_time" => $timestampTime,
					"ban_attempts" => $data->getData () ["ban_attempts"],
					"ban_time" => $data->getData () ["ban_time"],
					"username" => $user->getUsername (),
					"year" => date ("Y")
				)
			);
		}

	}