<?php

	/**
	 * ManageController.php - This controller renders the "Manage 2FA Accounts" page that can be
	 * found in the "JetRails/Two-Factor Authentication" menu tab.  It handles the rendering of that
	 * page as well as all the actions that are submitted with the grid container that the index
	 * action renders.
	 * @version         1.1.5
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Twofactor_ManageController extends Mage_Adminhtml_Controller_Action {

		/**
		 * This method simply asks Magento's ACL if the logged in user is allowed to see the
		 * manage page that belongs to this module.
		 * @return      boolean                                 Is the user allowed to see page?
		 */
		protected function _isAllowed () {
			// Is user allowed to manage 2FA accounts?
			$session = Mage::getSingleton ("admin/session");
			return $session->isAllowed ("jetrails/twofactor/manage");
		}

		/**
		 * This method simply takes in the type of log message and the user id and it then figures
		 * out the username of the currently logged in admin user and the username of the target
		 * user from the target id.  It then logs the message using the notify model.
		 * @param       integer             type                The type of log message
		 * @param       integer             targetId            Target user's id
		 * @return      void
		 */
		protected function _log ( $type, $targetId ) {
			// Get current logged in admin's username, and translate target user id to a username
			$adminUsername = Mage::getSingleton ("admin/session")->getUser ()->getUsername ();
			$targetUsername = Mage::getModel ("admin/user")->load ( $targetId )->getUsername ();
			// Get the notify model and log the action
			$notify = Mage::getModel ("twofactor/notify");
			$notify->log ( $type, array ( $adminUsername, $targetUsername ) );
		}

		/**
		 * This method takes in user ids and returns true if it is a natural number embedded as a
		 * string.  Otherwise false will be returned.  This method is used to sanitize the user ids
		 * that are passed to the action in this controller.
		 * @param       string               id                 The values to evaluate
		 * @return      boolean                                 Is ID valid?
		 */
		protected function _validateId ( $id ) {
			// Make sure the id only contains numeric digits
			return strval ( intval ( $id ) ) === strval ( $id ) &&
				// Make sure the result is a valid integer
				is_int ( intval ( $id ) ) &&
				// Make sure the integer is greater than zero
				intval ( $id ) > 0;
		}

		/**
		 * This action setup the grid container for the manage page.  It defines the page title,
		 * active tab, initializes session methods, and finally renders out the page as defined in
		 * they twofactor.xml layout file.
		 * @return      void
		 */
		public function indexAction () {
			// Set the title for the page
			$this->_title ( $this->__("JetRails") );
			$this->_title ( $this->__("Two-Factor Authentication") );
			$this->_title ( $this->__("Manage 2FA Accounts") );
			// Load layout, add the content, set active tab, and render layout
			$this->loadLayout ();
			$this->_initLayoutMessages ("admin/session");
			$this->_setActiveMenu ("jetrails/twofactor");
			$this->_addContent ( $this->getLayout ()->createBlock ("twofactor/manage_container") );
			$this->renderLayout ();
		}

		/**
		 * This action is used to render the grid out in the table found in the manage page.  It is
		 * also used by Magento through AJAX to update the table dynamically without refresh.
		 * @return      void
		 */
		public function gridAction () {
			// Load the layout
			$this->loadLayout ();
			// Create the manage grid and append it to the body
			$this->getResponse ()->setBody (
				$this->getLayout ()->createBlock ("twofactor/manage_container_grid")->toHtml ()
			);
		}

		/**
		 * This action evaluates the request headers and extracts the passed user ids.  It then
		 * evaluates that the user ids are valid.  Afterwards, it sets the status for the user's
		 * two-factor authentication to enabled.  This will in-turn enforce that the user sets up
		 * and uses a two-factor authentication account.
		 * @return      void
		 */
		public function enableAction () {
			// Load models that are necessary
			$page   = Mage::getModel ("twofactor/page");
			$notify = Mage::getModel ("twofactor/notify");
			$status = Mage::getModel ("twofactor/status");
			$auth   = Mage::getModel ("twofactor/auth");
			// Get all the ids that were passed to action
			$ids = $this->getRequest ()->getParam ("ids");
			// Loop through all the ids
			foreach ( $ids as $id ) {
				// Ignore if passed id is invalid
				if ( $this->_validateId ( $id ) ) {
					// Load the authentication, and preform action
					$auth->load ( $id );
					$auth->setId ( $id );
					$auth->setStatus ( $status::ENABLED );
					$auth->save ();
					// Log the success of this action
					$this->_log ( $notify::LOG_MANUAL_ENABLE, $id );
				}
			}
			// Attach a success message to the admin session
			$this->_getSession ()->addSuccess (
				Mage::helper ("twofactor")->__("Successfully enabled 2FA on selected accounts")
			);
			// Redirect back to the index action from the manage controller
			$this->_redirect ( $page::PAGE_MANAGE_INDEX );
		}

		/**
		 * This action evaluates the request headers and extracts the passed user ids.  It then
		 * evaluates that the user ids are valid.  Afterwards, it sets the status for the user's
		 * two-factor authentication to disabled.  This will in-turn will not enforce that the user
		 * sets up or uses a two-factor authentication account.
		 * @return      void
		 */
		public function disableAction () {
			// Load models that are necessary
			$page   = Mage::getModel ("twofactor/page");
			$notify = Mage::getModel ("twofactor/notify");
			$status = Mage::getModel ("twofactor/status");
			$auth   = Mage::getModel ("twofactor/auth");
			// Get all the ids that were passed to action
			$ids = $this->getRequest ()->getParam ("ids");
			// Loop through all the ids
			foreach ( $ids as $id ) {
				// Ignore if passed id is invalid
				if ( $this->_validateId ( $id ) ) {
					// Load the authentication, and preform action
					$auth->load ( $id );
					$auth->setId ( $id );
					$auth->setStatus ( $status::DISABLED );
					$auth->save ();
					// Log the success of this action
					$this->_log ( $notify::LOG_MANUAL_DISABLE, $id );
				}
			}
			// Attach a success message to the admin session
			$this->_getSession ()->addSuccess (
				Mage::helper ("twofactor")->__("Successfully disabled 2FA on selected accounts")
			);
			// Redirect back to the index action of the manage controller
			$this->_redirect ( $page::PAGE_MANAGE_INDEX );
		}

		/**
		 * This action evaluates the request headers and extracts the passed user ids.  It then
		 * evaluates that the user ids are valid.  Afterwards, it will remove any temporary ban that
		 * a user has on their two-factor authentication account.
		 * @return      void
		 */
		public function unbanAction () {
			// Load models that are necessary
			$page   = Mage::getModel ("twofactor/page");
			$notify = Mage::getModel ("twofactor/notify");
			$state  = Mage::getModel ("twofactor/state");
			$auth   = Mage::getModel ("twofactor/auth");
			// Get all the ids that were passed to action
			$ids = $this->getRequest ()->getParam ("ids");
			// Loop through all the ids
			foreach ( $ids as $id ) {
				// Ignore if passed id is invalid
				if ( $this->_validateId ( $id ) ) {
					// Load the authentication, and preform action
					$auth->load ( $id );
					$auth->setId ( $id );
					// Check to see if the user is in fact banned
					if ( $auth->isBanned () ) {
						// Reset data in authentication model
						$auth->setState ( $state::VERIFY );
						$auth->setAttempts ( 0 );
						$auth->save ();
						// Log the success of this action
						$this->_log ( $notify::LOG_MANUAL_UNBAN, $id );
					}
				}
			}
			// Attach a success message to the admin session
			$this->_getSession ()->addSuccess (
				Mage::helper ("twofactor")->__("Successfully removed temp ban on selected accounts")
			);
			// Redirect back to the index action of the manage controller
			$this->_redirect ( $page::PAGE_MANAGE_INDEX );
		}

		/**
		 * This action evaluates the request headers and extracts the passed user ids.  It then
		 * evaluates that the user ids are valid.  Afterwards, it sets the status of the two-factor
		 * authentication account to the setup state. In addition it removes the TOTP secret, failed
		 * attempts, etc.
		 * @return      void
		 */
		public function resetAction () {
			// Load models that are necessary
			$page   = Mage::getModel ("twofactor/page");
			$notify = Mage::getModel ("twofactor/notify");
			$state  = Mage::getModel ("twofactor/state");
			$auth   = Mage::getModel ("twofactor/auth");
			$totp   = Mage::helper ("twofactor/totp");
			$totp->initialize ();
			// Get all the ids that were passed to action
			$ids = $this->getRequest ()->getParam ("ids");
			// Loop through all the ids
			foreach ( $ids as $id ) {
				// Ignore if passed id is invalid
				if ( $this->_validateId ( $id ) ) {
					// Load the authentication, and preform action
					$auth->load ( $id );
					$auth->setId ( $id );
					$auth->setBackupCodes ( array () );
					$auth->setAttempts ( 0 );
					$auth->setSecret ( $totp->getSecret () );
					$auth->setState ( $state::SCAN );
					$auth->save ();
					// Log the success of this action
					$this->_log ( $notify::LOG_MANUAL_RESET, $id );
				}
			}
			// Attach a success message to the admin session
			$this->_getSession ()->addSuccess (
				Mage::helper ("twofactor")->__("Successfully reset 2FA accounts for selected accounts")
			);
			// Redirect back to the index action of the manage controller
			$this->_redirect ( $page::PAGE_MANAGE_INDEX );
		}

	}
