<?php

	/**
	 * Page.php - This class contains the page route constants that are used throughout the module.
	 * It also contains methods that link the state to the associated route.
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Page extends Mage_Core_Model_Abstract {

		/**
		 * These constants describe the controller routes to the different pages that are part of
		 * this module.  This includes throughout the setup of 2FA and for account management.
		 */
		const PAGE_SETUP_SCAN       = "twofactor/setup/scan";
		const PAGE_SETUP_BACKUP     = "twofactor/setup/backup";
		const PAGE_SETUP_RESET      = "twofactor/setup/reset";
		const PAGE_SETUP_ENABLE     = "twofactor/setup/enable";
		const PAGE_SETUP_DISABLE    = "twofactor/setup/disable";
		const PAGE_LOGIN_BLOCKED    = "twofactor/login/blocked";
		const PAGE_LOGIN_VERIFY     = "twofactor/login/verify";

		/**
		 * This method takes in a controller route and an authorization state, which is defined in
		 * the Auth model, and it returns whether or not the route is allowed based on the state.
		 * @param       string              route               One of the constants defined above
		 * @param       integer             state               State constant defined in Auth model
		 * @return      boolean                                 Is route allowed based on state
		 */
		public function isRouteAllowed ( $route, $state ) {
			return $this->getPageFromState ( $state ) === $route;
		}

		/**
		 * This method takes in an authorization state, which is defined in the Auth model, and it
		 * returns the route that is associated with said state.  If an unknown state is passed,
		 * then the route to default on would be the route leading to the verification page.
		 * @param       integer             state               State constant defined in Auth model
		 * @return      string                                  Route based on authorization state
		 */
		public function getPageFromState ( $state ) {
			// Get the authorization model
			$auth = Mage::getSingleton ("twofactor/auth");
			// Return a page route based on the passed state
			switch ( $state ) {
				case $auth::STATE_BACKUP:
					return self::PAGE_SETUP_BACKUP;
				case $auth::STATE_SCAN:
					return self::PAGE_SETUP_SCAN;
				case $auth::STATE_VERIFY:
					return self::PAGE_LOGIN_VERIFY;
				case $auth::STATE_BLOCKED:
					// If the state is blocked, then check to see if the time limit expired
					return $auth->isBlocked () ? self::PAGE_LOGIN_BLOCKED : self::PAGE_LOGIN_VERIFY;
				default:
					return self::PAGE_LOGIN_VERIFY;
			}
		}

	}