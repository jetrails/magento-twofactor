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
		 * this module.  This includes throughout the setup of 2FA and for account management.  This
		 * also includes all the controller routes that are defined for the manage and configure
		 * controllers that only super admins have access to.
		 */
		const PAGE_SETUP_SCAN       = "twofactor/setup/scan";
		const PAGE_SETUP_BACKUP     = "twofactor/setup/backup";
		const PAGE_SETUP_RESET      = "twofactor/setup/reset";
		const PAGE_SETUP_ENABLE     = "twofactor/setup/enable";
		const PAGE_SETUP_DISABLE    = "twofactor/setup/disable";
		const PAGE_LOGIN_BANNED     = "twofactor/login/banned";
		const PAGE_LOGIN_VERIFY     = "twofactor/login/verify";
		const PAGE_MANAGE_INDEX     = "twofactor/manage/index";
		const PAGE_MANAGE_GRID      = "twofactor/manage/grid";
		const PAGE_MANAGE_UNBAN     = "twofactor/manage/unban";
		const PAGE_MANAGE_ENABLE    = "twofactor/manage/enable";
		const PAGE_MANAGE_DISABLE   = "twofactor/manage/disable";
		const PAGE_MANAGE_RESET     = "twofactor/manage/reset";
		const PAGE_CONFIGURE_INDEX  = "twofactor/configure/index";
		const PAGE_CONFIGURE_SAVE   = "twofactor/configure/save";

		/**
		 * This method takes in a controller route and an authorization state, which is defined in
		 * the Auth model, and it returns whether or not the route is allowed based on the state.
		 * @param       string              route               One of the constants defined above
		 * @param       integer             state               State constant defined in Auth model
		 * @return      boolean                                 Is route allowed based on state
		 */
		public function isRouteAllowed ( $route, $state ) {
			// Check to see if the route is expected
			return $this->getPageFromState ( $state ) === $route;
		}

		/**
		 * This method takes in an authorization state, which is defined in the Auth model, and it
		 * returns the route that is associated with said state.  If an unknown state is passed,
		 * then the route to default on would be the route leading to the verification page.
		 * @param       integer             targetState         State constant to route with
		 * @return      string                                  Route based on authorization state
		 */
		public function getPageFromState ( $targetState ) {
			// Get the authorization model
			$state = Mage::getModel ("twofactor/state");
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $admin->getUserId () );
			$auth->setId ( $admin->getUserId () );
			// Return a page route based on the passed state
			switch ( $targetState ) {
				case $state::BACKUP:
					return self::PAGE_SETUP_BACKUP;
				case $state::SCAN:
					return self::PAGE_SETUP_SCAN;
				case $state::VERIFY:
					return self::PAGE_LOGIN_VERIFY;
				case $state::BANNED:
					return $auth->isBanned () ? self::PAGE_LOGIN_BANNED : self::PAGE_LOGIN_VERIFY;
				default:
					return self::PAGE_LOGIN_VERIFY;
			}
		}

	}