<?php

	/**
	 * LoginController.php - This controller contains an action to render the Verify page after
	 * successful user login and also a function that validates the submitted form from the Verify
	 * page.
	 * @version         1.0.5
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_LoginController extends Mage_Adminhtml_Controller_Action {

		/**
		 * This action simply determines if the the TFA service is enabled, if it is then it renders
		 * the Verify page using the appropriate block and template file.  This action is called
		 * within the TwoFactor observer.
		 * @return      void
		 */
		public function formAction () {
			// Initialize the user id, TOTP helper, and the Data helper
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			$Data = Mage::helper ("twofactor/Data");
			$TOTP = Mage::helper ("twofactor/TOTP");
			// Make sure (double check) that TFA is enabled
			if ( $Data->isEnabled ( $uid ) ) {
				// Create the Verify block
				$block = Mage::app ()->getLayout ()->createBlock ("twofactor/Adminhtml_Template_Verify");
				// Set the template for this block
				$block->setTemplate ("JetRails/TwoFactor/Verify.phtml");
				// Output the HTML
				echo $block->toHtml ();
			}
			// Otherwise, print out error
			else {
				echo "TFA is not enabled, please leave.";
			}
		}

		/**
		 * This action is called within the Verify page as a form action.  This form passes a POST
		 * with the TOTP pin.  This action validates the pin and completes the login process.  If
		 * the pin is invalid, it sets an error and redirects back to the verify page where the
		 * error is displayed.
		 * @return      void
		 */
		public function verifyAction () {
			// Initialize the user id, TOTP helper, and the Data helper
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			$Data = Mage::helper ("twofactor/Data");
			$TOTP = Mage::helper ("twofactor/TOTP");
			// Get the request
			$request = Mage::app ()->getRequest ();
			// Get the passed pin and remember flag from form
			$pin = $request->getPost ( "pin", "" );
			$remember = $request->getPost ( "remember", false );
			// Initialize the user id, TOTP helper, and the Data helper
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			$Data = Mage::helper ("twofactor/Data");
			$TOTP = Mage::helper ("twofactor/TOTP");
			// Make sure (double check) that TFA is enabled
			if ( $Data->isEnabled ( $uid ) ) {
				// Initialize the TOTP object
				$TOTP->initialize ( $Data->getSecret ( $uid ) );
				// Compare if the passed pin matches the correct one
				if ( $pin == $TOTP->pin () ) {
					// Redirect to startup page
					$this->_getSession ()->unsTwoFactorFlag ();
					$this->_redirect ("adminhtml");
					// Check to see if remember flag was set
					if ( $remember === "on" ) {
						// Create cookie with session and some sort of authentication for cookie
						$now = time ();
						$Cookie = Mage::helper ("twofactor/Cookie");
						$Cookie->create (
							$now,
							$TOTP->pin ( $now ),
							Mage::helper ("core/http")->getRemoteAddr ()
						);
					}
					return;
				}
				// Otherwise, it doesn't match! Set error and redirect
				Mage::getSingleton ("core/session")->addError ("Unable to login, invalid verification pin was passed!");
				$this->_redirect ("jetrails_twofactor/login/form");
			}
			// Otherwise, just redirect to the start page
			else {
				// Set redirect to start page
				$this->_redirect ("adminhtml");
			}
		}

		/**
		 * This method needs to be overridden because of the way Magento decided to change the
		 * functionality of this method.  Affective after SUPEE-6285, this method only returns true
		 * if the user has full administrative access.  This means that users with assign roles that
		 * are restrictive will not have access to this controller.  That is why we override this
		 * controller and always allow access to it.
		 * @return 		boolean 								Does the user have access?
		 */
		protected function _isAllowed () {
			// Allow all backend users access to this controller		
			return true;
		}

	}

?>