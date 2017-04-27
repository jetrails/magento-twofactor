<?php

	/**
	 * TwoFactor.php - This observer redirects a logged in user to the Verify page if TFA is
	 * enabled.  Also if the user tries to redirect out of the Verify page, they will be redirected
	 * back until verification is complete.
	 * @version         1.0.5
	 * @package         JetRails® TwoFactor
	 * @category        Observer
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Observer_TwoFactor {

		/**
		 * This event observer executes whenever an admin user successfully logs into their account.
		 * We check if TFA is enabled using the user id linked to the session and we set a flag in
		 * the session stating that we need to authenticate the user.  This way if the user tries to
		 * redirect, they are redirected back to the Verify page, this is handled in the next
		 * observer.  We then redirect to the Verify page.
		 * @param       Varien_Event_Observer   observer            The observed event
		 * @return      Observer Class
		 */
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

		/**
		 * After every adminhtml controller redirect this function runs to ensure that the user
		 * first verified their account.  If they didn't then they will be redirected back to the
		 * Verify page.
		 * @param       Varien_Event_Observer   observer            The observed event
		 * @return      Observer Class
		 */
		public function postAdminHtml ( Varien_Event_Observer $observer ) {
			// Get the request using the observer
			$request = $observer->getControllerAction ()->getRequest ();
			// Is this the logout action?
			$logout = $request->getActionName () == "logout";
			// If the flag is set in the session
			if ( Mage::getSingleton ("adminhtml/session")->getTwoFactorFlag () && !$logout ) {
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