<?php

	class JetRails_TwoFactor_Model_Adminhtml_Observer_TwoFactor {

		public function afterAdminAuthenticate ( Varien_Event_Observer $observer ) {
			// Initialize the user id and the data  and cookie helpers
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			$Data = Mage::helper ("twofactor/Data");
			$Cookie = Mage::helper ("twofactor/Cookie");
			// Check to see if we should route to the two factor page
			if ( $Data->isEnabled ( $uid ) && !$Cookie->authenticate ( $uid ) ) {
				// Redirect to TFA controller
				$response = Mage::app ()->getResponse ();
				Mage::getSingleton ("adminhtml/session")->setTwoFactorFlag ( true );
				$redirect = Mage::helper ("adminhtml")->getUrl ("jetrails_twofactor/login/form");
				$response->setRedirect ( $redirect );
				$response->sendResponse ();
			}
			// Return an instance of class
			return $this;
		}

		public function postAdminHtml ( Varien_Event_Observer $observer ) {
			// Get the request using the observer
			$request = $observer->getControllerAction ()->getRequest ();
			// If the action is logout then simply return without redirect to verify page
			if ( $request->getActionName () == "logout" ) {
				// Return without TFA redirect
				return $this;
			}
			// If the flag is set in the session
			if ( Mage::getSingleton ("adminhtml/session")->getTwoFactorFlag () ) {
				// Initialize the user id and the data  and cookie helpers
				$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
				$Data = Mage::helper ("twofactor/Data");
				$Cookie = Mage::helper ("twofactor/Cookie");
				// Check to see if we should route to the two factor page
				if ( $Data->isEnabled ( $uid ) && !$Cookie->authenticate ( $uid ) ) {
					// Redirect to TFA controller
					$redirect = Mage::helper ("adminhtml")->getUrl ("jetrails_twofactor/login/form");
					$response = Mage::app ()
						->getResponse ()
						->setRedirect ( $redirect )
						->sendResponse ();
				}
			}
			// Return an instance of this class either way
			return $this;
		}

	}

?>