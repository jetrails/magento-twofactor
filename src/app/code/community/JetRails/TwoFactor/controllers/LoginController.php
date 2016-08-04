<?php

	class JetRails_TwoFactor_LoginController extends Mage_Adminhtml_Controller_Action {

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

	}

?>