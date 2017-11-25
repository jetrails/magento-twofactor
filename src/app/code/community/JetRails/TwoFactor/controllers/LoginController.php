<?php

	/**
	 * LoginController.php - This controller contains all actions that relate to authenticating
	 * their two factor authentication account.  Actions that render the verification page as well
	 * as the blocked page that a user will see on failed login are all encapsulated within this
	 * controller.
	 * @version         1.0.9
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_LoginController extends Mage_Adminhtml_Controller_Action {

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
		 * This method is a helper function that simply logs when a user is banned.  It also
		 * constructs the emails for the admin user that is logged in and all admin users in the
		 * 'Administrator' role.  It then also sends out said emails.
		 * @return      void
		 */
		protected function _notifyAccountBlock () {
			// Get the authentication and notify models, as well as the logged in admin user
			$auth = Mage::getSingleton ("twofactor/auth");
			$notify = Mage::getSingleton ("twofactor/notify");
			$user = Mage::getSingleton ("admin/session")->getUser ();
			// Append block as entry to the log
			$notify->logAccountBlock (
				$user->getEmail (),
				$user->getId (),
				$auth->getLastAddress ()
			);
			// Initialize admin and user messages
			$adminMessage = $this
				->getLayout ()
				->createBlock ("twofactor/adminhtml_email_admin")
				->setTemplate ("twofactor/email.phtml")
				->toHtml ();
			$userMessage = $this
				->getLayout ()
				->createBlock ("twofactor/adminhtml_email_user")
				->setTemplate ("twofactor/email.phtml")
				->toHtml ();
			// Send the emails to the administrators and the user
			$notify->emailAllAdministrators ( $adminMessage );
			$notify->emailUser ( $userMessage );
		}

		/**
		 * This method checks to see what startup page an admin user is configured to go to after
		 * successful login.  It then redirects the user to that page.  If the flag that is passed
		 * is true, then the startup notification is shown in the startup page.
		 * @param       boolean             changePageAfterLogin        Show alert (first login)
		 * @return      void
		 */
		protected function _redirectToStartUpPage ( $changePageAfterLogin ) {
			// Get the admin session and get the startup page that is configured for user
			$session = Mage::getSingleton ("admin/session");
			$url = $session->getUser ()->getStartupPageUrl ();
			// Check flag and set if we want to see the first page notification after redirect
			if ( $changePageAfterLogin ) $session->setIsFirstPageAfterLogin ( true );
			// Redirect the user to the configured startup page
			$this->_redirect ( $url );
		}

		/**
		 * This action simply renders out the page structure that is defined in twofactor.xml under
		 * the jetrails_twofactor_login_blocked handle.
		 * @return      void
		 */
		public function blockedAction () {
			// Load layout and render it
			$this->loadLayout ();
			$this->renderLayout ();
		}

		/**
		 * This action ultimately renders out a page using the layout defined in twofactor.xml. This
		 * page is the page that is seen once two factor authentication is setup and the user is
		 * trying to login.  The authentication page is displayed and the user is prompted to
		 * authenticate using their setup authentication account.  It also handles the form
		 * submission for said page.
		 * @return      void
		 */
		public function verifyAction () {
			// Get authentication model and register an attempt
			$auth = Mage::getSingleton ("twofactor/auth");
			// Check to see if a form was submitted
			if ( $this->getRequest ()->getPost () ) {
				// Register an attempt
				$auth->registerAttempt ();
				// Check to see if we are submitting a pin
				if ( $this->getRequest ()->getPost ("pin") ) {
					// Check to see if the supplied pin is correct
					if ( $auth->verify ( $this->getRequest ()->getPost ("pin") ) ) {
						// Check to see if we requested to be remembered
						if ( $this->getRequest ()->getPost ( "remember", "off" ) === "on" ) {
							// Get cookie helper and create a cookie
							$cookie = Mage::helper ("twofactor/cookie");
							$time = ( new Zend_Date () )->toString ();
							$pin = intval ( $this->getRequest ()->getPost ("pin") );
							$pin = str_pad ( $pin, 6, "0", STR_PAD_LEFT );
							$address = Mage::helper ("core/http")->getRemoteAddr ();
							$cookie->create ( $time, $pin, $address );
						}
						// Reset the number of attempts, and allow access to admin area
						$auth->setAttempts ( 0 );
						$auth->setState ( $auth::STATE_VERIFY );
						$auth->save ();
						Mage::getSingleton ("admin/session")->setTwoFactorAllow ( true );
						// Redirect page to the startup page for admin area
						return $this->_redirectToStartUpPage ( true );
					}
				}
				// Check to see if we are submitting a backup code
				else if ( $this->getRequest ()->getPost ("code") ) {
					// Clean supplied code, and get available codes
					$code = intval ( $this->getRequest ()->getPost ("code") );
					$code = str_pad ( $code, 8, "0", STR_PAD_LEFT );
					$codes = $auth->getBackupCodes ();
					// Check to see if the code exists
					if ( in_array ( "$code", $codes ) ) {
						// Check to see if we requested to be remembered
						if ( $this->getRequest ()->getPost ( "remember", "off" ) === "on" ) {
							// Get cookie helper and create a cookie
							$totp = Mage::helper ("twofactor/totp");
							$totp->initialize ( $auth->getSecret () );
							$cookie = Mage::helper ("twofactor/cookie");
							$time = ( new Zend_Date () )->toString ();
							$pin = intval ( $totp->pin () );
							$pin = str_pad ( $pin, 6, "0", STR_PAD_LEFT );
							$address = Mage::helper ("core/http")->getRemoteAddr ();
							$cookie->create ( $time, $pin, $address );
						}
						// Remove that backup code, reset attempts, allow access to admin area
						Mage::getSingleton ("admin/session")->setTwoFactorAllow ( true );
						array_splice ( $codes, array_search ( $code, $codes ), 1 );
						$auth->setBackupCodes ( $codes );
						$auth->setAttempts ( 0 );
						$auth->save ();
						// Redirect page to the startup page for admin area
						return $this->_redirectToStartUpPage ( true );
					}
				}
				// Only attach an error if we aren't on our last attempt
				if ( $auth->getAttempts () < $auth::MAX_ATTEMPTS ) {
					// Attach fail message to session
					$pin = $this->getRequest ()->getPost ("pin");
					$type = !empty ( $pin ) ? "pin" : "code";
					$attempts = $auth::MAX_ATTEMPTS - $auth->getAttempts ();
					$value = intval ( $this->getRequest ()->getPost ( $type ) );
					$value = str_pad ( $value, $type === "pin" ? 6 : 8, "0", STR_PAD_LEFT );
					$message = $this->__("invalid authentication attempt, %d attempt(s) left");
					$message = sprintf ( $message, $attempts );
					$message = array (
						"type" => $type,
						"value" => $value,
						"message" => $message
					);
					Mage::getSingleton ("core/session")->addError ( json_encode ( $message ) );
				}
			}
			// Check to see if current admin user if blocked
			if ( $auth->getState () == $auth::STATE_BLOCKED ) {
				// Notify about account block and redirect (observer will block)
				$this->_notifyAccountBlock ();
				return $this->_redirectToStartUpPage ( false );
			}
			// Load the layout and render setup page
			$this->loadLayout ();
			$this->_initLayoutMessages ("admin/session");
			$this->renderLayout ();
		}

	}