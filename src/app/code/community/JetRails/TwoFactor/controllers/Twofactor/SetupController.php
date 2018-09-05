<?php

	/**
	 * SetupController.php - This controller contains all actions that relate to setting up the two
	 * factor authentication on an admin user's account.  Actions that render both the scan and
	 * backup pages for setup, as well as an action that resets the whole process for an admin user.
	 * @version         1.1.1
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Twofactor_SetupController extends Mage_Adminhtml_Controller_Action {

		/**
		 * This method asks the data helper to determine if the logged in admin user is allowed to
		 * see the contents of this controller action.  It is based on the ACL permissions that the
		 * admin user is assigned to based on the role they are in.
		 * @return      boolean                                 Is admin allowed to use controller
		 */
		protected function _isAllowed () {
			// Is module config in admin user's ACL
			return Mage::helper ("twofactor")->isAllowed ();
		}

		/**
		 * This action ultimately renders out a page using the layout defined in twofactor.xml. This
		 * page is the first page that an admin will see in the setup process.  It also handles the
		 * form submission for said page.
		 * @return      void
		 */
		public function scanAction () {
			// Check to see if a form was submitted
			if ( $this->getRequest ()->getPost () ) {
				// Get authentication model
				$admin = Mage::getSingleton ("admin/session")->getUser ();
				$state = Mage::getModel ("twofactor/state");
				$auth = Mage::getModel ("twofactor/auth");
				$auth->load ( $admin->getUserId () );
				$auth->setId ( $admin->getUserId () );
				// Check to see if the supplied pin is correct
				if ( $auth->verify ( $this->getRequest ()->getPost ("pin") ) ) {
					// Change state from setup to normal
					$auth->registerAttempt ();
					$auth->setAttempts ( 0 );
					$auth->setState ( $state::BACKUP );
					$auth->save ();
					// Redirect page to the startup page for admin area
					$session = Mage::getSingleton ("admin/session");
					$url = $session->getUser ()->getStartupPageUrl ();
					$session->setIsFirstPageAfterLogin ( true );
					return $this->_redirect ( $url );
				}
				else {
					// Set an error message and render page again
					$message = array (
						"type" => "pin",
						"value" => intval ( $this->getRequest ()->getPost ("pin") ),
						"message" => $this->__("verification pin didn't match, try again")
					);
					Mage::getSingleton ("core/session")->addError ( json_encode ( $message ) );
				}
			}
			// Load the layout and render setup page
			$this->loadLayout ();
			$this->_initLayoutMessages ("admin/session");
			$this->renderLayout ();
		}

		/**
		 * This action ultimately renders out a page using the layout defined in twofactor.xml. This
		 * page is the second page that an admin will see in the setup process.  This page shows
		 * backup codes after the user successfully proved that they setup their 2FA account. It
		 * also handles the form submission for said page.
		 * @return      void
		 */
		public function backupAction () {
			// Check to see if a form was submitted
			if ( $this->getRequest ()->getPost () ) {
				// Allow user to the backend
				Mage::getSingleton ("admin/session")->setTwoFactorAllow ( true );
				$admin = Mage::getSingleton ("admin/session")->getUser ();
				$state = Mage::getModel ("twofactor/state");
				$auth = Mage::getModel ("twofactor/auth");
				$auth->load ( $admin->getUserId () );
				$auth->setId ( $admin->getUserId () );
				$auth->setState ( $state::VERIFY );
				$auth->save ();
				// Redirect to admin saved startup page
				$session = Mage::getSingleton ("admin/session");
				$url = $session->getUser ()->getStartupPageUrl ();
				$session->setIsFirstPageAfterLogin ( true );
				return $this->_redirect ( $url );
			}
			// Load the layout and render setup page
			$this->loadLayout ();
			$this->renderLayout ();
		}

	}
