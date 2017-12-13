<?php

	/**
	 * ManageController.php -
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_ManageController extends Mage_Adminhtml_Controller_Action {

		/**
		 * 
		 */
		protected function _isAllowed () {
			// Is user allowed to manage 2FA accounts?
			$session = Mage::getSingleton ("admin/session");
			return $session->isAllowed ("jetrails/twofactor/manage");
		}

		/**
		 * 
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
		 * 
		 */
		protected function _validateId ( $id ) {
			// Make sure the id only contains numeric digits
			return strval ( intval ( $id ) ) === strval ( $id ) &&
				// Make sure the result is a valid integer
				is_int ( intval ( $id ) ) &&
				// Make sure the integer is greater than zero
				intval ( $id ) > 0;
		} 

		public function indexAction () {
			// Set the title for the page
			$this->_title ( $this->__("JetRails") );
			$this->_title ( $this->__("Two-Factor Authentication") );
			$this->_title ( $this->__("Manage 2FA Accounts") );
			// Load layout, add the content, set active tab, and render layout
			$this->loadLayout ();
			$this->_setActiveMenu ("jetrails/twofactor");
			$this->_addContent ( $this->getLayout ()->createBlock ("twofactor/manage_container") );
			$this->renderLayout ();
		}

		public function gridAction () {
			// Load the layout
			$this->loadLayout ();
			// Create the manage grid and append it to the body
			$this->getResponse ()->setBody ( 
				$this->getLayout ()->createBlock ("twofactor/manage_container_grid")->toHtml ()
			);
		}

		public function enableAction () {
			//
			$page   = Mage::getModel ("twofactor/page");
			$notify = Mage::getModel ("twofactor/notify");
			$status = Mage::getModel ("twofactor/status");
			$auth   = Mage::getModel ("twofactor/auth");
			//
			$ids = $this->getRequest ()->getParam ("ids");
			//
			foreach ( $ids as $id ) {
				//
				if ( $this->_validateId ( $id ) ) {
					//
					$auth->load ( $id );
					$auth->setId ( $id );
					$auth->setStatus ( $status::ENABLED );
					$auth->save ();
					// Log the success of this action
					$this->_log ( $notify::LOG_MANUAL_ENABLE, $id );
				}
			}
			//
			$this->_getSession ()->addSuccess (
				Mage::helper ("twofactor")->__("Successfully enabled 2FA on selected accounts")
			);
			//
			$this->_redirect ( $page::PAGE_CONFIGURE_INDEX );
		}

		public function disableAction () {
			//
			$page   = Mage::getModel ("twofactor/page");
			$notify = Mage::getModel ("twofactor/notify");
			$status = Mage::getModel ("twofactor/status");
			$auth   = Mage::getModel ("twofactor/auth");
			//
			$ids = $this->getRequest ()->getParam ("ids");
			//
			foreach ( $ids as $id ) {
				//
				if ( $this->_validateId ( $id ) ) {
					//
					$auth->load ( $id );
					$auth->setId ( $id );
					$auth->setStatus ( $status::DISABLED );
					$auth->save ();
					// Log the success of this action
					$this->_log ( $notify::LOG_MANUAL_DISABLE, $id );
				}
			}
			//
			$this->_getSession ()->addSuccess (
				Mage::helper ("twofactor")->__("Successfully disabled 2FA on selected accounts")
			);
			//
			$this->_redirect ( $page::PAGE_CONFIGURE_INDEX );
		}

		public function unbanAction () {
			//
			$page   = Mage::getModel ("twofactor/page");
			$notify = Mage::getModel ("twofactor/notify");
			$state  = Mage::getModel ("twofactor/state");
			$auth   = Mage::getModel ("twofactor/auth");
			//
			$ids = $this->getRequest ()->getParam ("ids");
			//
			foreach ( $ids as $id ) {
				//
				if ( $this->_validateId ( $id ) ) {
					//
					$auth->load ( $id );
					$auth->setId ( $id );
					//
					if ( $auth->isBanned () ) {
						//
						$auth->setState ( $state::VERIFY );
						$auth->setAttempts ( 0 );
						$auth->save ();
						// Log the success of this action
						$this->_log ( $notify::LOG_MANUAL_UNBAN, $id );
					}
				}
			}
			//
			$this->_getSession ()->addSuccess (
				Mage::helper ("twofactor")->__("Successfully removed temp ban on selected accounts")
			);
			//
			$this->_redirect ( $page::PAGE_CONFIGURE_INDEX );
		}

		public function resetAction () {
			//
			$page   = Mage::getModel ("twofactor/page");
			$notify = Mage::getModel ("twofactor/notify");
			$state  = Mage::getModel ("twofactor/state");
			$auth   = Mage::getModel ("twofactor/auth");
			//
			$ids = $this->getRequest ()->getParam ("ids");
			//
			foreach ( $ids as $id ) {
				//
				if ( $this->_validateId ( $id ) ) {
					//
					$auth->load ( $id );
					$auth->setId ( $id );
					$auth->setBackupCodes ( array () );
					$auth->setAttempts ( 0 );
					$auth->setSecret ("");
					$auth->setState ( $state::SCAN );
					$auth->save ();

					// Log the success of this action
					$this->_log ( $notify::LOG_MANUAL_RESET, $id );
				}
			}
			//
			$this->_getSession ()->addSuccess (
				Mage::helper ("twofactor")->__("Successfully reset 2FA accounts for selected accounts")
			);
			//
			$this->_redirect ( $page::PAGE_CONFIGURE_INDEX );
		}

	}