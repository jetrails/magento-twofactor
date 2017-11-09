<?php

	/**
	 * Observer.php - This class stores the observer method that is used and configured within the
	 * config.xml file.  This observer fires before any adminhtml action gets executed.  This way if
	 * a user is not authenticated, we can block access to those actions and redirect to the
	 * verification page.
	 * @version         1.0.7
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Observer {

		/**
		 * This method is a helper function that is used within the observed method below.  This
		 * method simply takes in the observer object, and the route that is requested to redirect
		 * to.  It then redirects to the requested route and sets a flag within the requested action
		 * to not continue dispatching other observers.
		 * @param       Varien_Event_Observer   observer    Passed event observer
		 * @param       string                  route       The route to redirect to
		 * @return      void
		 */
		protected function _redirectByRoute ( $observer, $route ) {
			// Set redirect by url based on route
			$response = Mage::app ()->getResponse ();
			$url = Mage::helper ("adminhtml")->getUrl ( $route );
			$response->setRedirect ( $url );
			// Set controller action dispatch flag to not continue dispatching
			$controller = $observer->getControllerAction ();
			$controller->getRequest ()->setDispatched ( true );
			$controller->setFlag ( "", Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true );
			// Send response once everything is set
			$response->sendResponse ();
		}

		/**
		 * This method observes the event before any admin controller route gets called.  This way
		 * we control access to the adminhtml pages based on the state of authentication of the
		 * admin user.
		 * @param       Varien_Event_Observer   observer    Passed event observer
		 * @return      void
		 */
		public function preAdminHtml ( Varien_Event_Observer $observer ) {
			// Check to make sure an admin is logged in
			if ( Mage::getSingleton ("admin/session")->isLoggedIn () ) {
				// Initialize routing information
				$request = Mage::app ()->getRequest ();
				$frontname = $request->getModuleName ();
				$controller = $request->getControllerName ();
				$action = $request->getActionName ();
				$route = "$frontname/$controller/$action";
				// Allow the admin logout action
				if ( $route === "admin/index/logout" ) return;
				// If two factor is not forced on role, then ignore everything
				if ( !Mage::helper ("twofactor")->isAllowed () ) return;
				// Get instances of objects
				$admin = Mage::getSingleton ("admin/session");
				$page = Mage::getSingleton ("twofactor/page");
				$auth = Mage::getSingleton ("twofactor/auth");
				$cookie = Mage::helper ("twofactor/cookie");
				// Get the TOTP state and admin user id
				$state = $auth->getState ();
				$uid = $auth->getId ();
				// If in scan state and we didn't refresh secret for session, then refresh it
				if ( $auth->getState () == $auth::STATE_SCAN && !$admin->getTwoFactorSetup () ) {
					// Generate new secret for session
					$totp = Mage::helper ("twofactor/totp");
					$totp->initialize ();
					$auth->setSecret ( $totp->getSecret () );
					$auth->setBackupCodes ( $totp->generateBackupCodes ( 10 ) );
					$auth->save ();
					$admin->setTwoFactorSetup ( true );
				}
				// Session is not authenticated, or is authenticated but not in verify state
				if ( $admin->getTwoFactorAllow () !== true || $state != $auth::STATE_VERIFY ) {
					// Allow state based routes to allow for state based pages
					if ( $page->isRouteAllowed ( $route, $state ) ) return;
					// If the state is not verify, then unset session flag
					if ( $state != $auth::STATE_VERIFY ) $admin->unsTwoFactorAllow ();
					// If there is a cookie and it is valid, then allow access to admin area
					if ( $cookie->authenticate ( $uid ) && $state != $auth::STATE_BLOCKED ) {
						// Allow access to admin area and allow dispatch to continue
						return $admin->setTwoFactorAllow ( true );
					}
					// Get appropriate page based on state and redirect
					$redirectRoute = $page->getPageFromState ( $state );
					$this->_redirectByRoute ( $observer, $redirectRoute );
				}
				// Session is authenticated, state is verify, and route is not reset
				else if ( $frontname === "twofactor" && $route != $page::PAGE_SETUP_RESET ) {
					// Redirect user to their saved startup page
					$redirectRoute = $admin->getUser ()->getStartupPageUrl ();
					$this->_redirectByRoute ( $observer, $redirectRoute );
				}
			}
		}

	}