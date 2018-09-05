<?php

	/**
	 * Page.php - This class contains the page route constants that are used throughout the module.
	 * It also contains methods that link the state to the associated route.
	 * @version         1.1.2
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
		const PAGE_SETUP_SCAN       = "adminhtml/twofactor_setup/scan";
		const PAGE_SETUP_BACKUP     = "adminhtml/twofactor_setup/backup";
		const PAGE_SETUP_RESET      = "adminhtml/twofactor_setup/reset";
		const PAGE_SETUP_ENABLE     = "adminhtml/twofactor_setup/enable";
		const PAGE_SETUP_DISABLE    = "adminhtml/twofactor_setup/disable";
		const PAGE_LOGIN_BANNED     = "adminhtml/twofactor_login/banned";
		const PAGE_LOGIN_VERIFY     = "adminhtml/twofactor_login/verify";
		const PAGE_MANAGE_INDEX     = "adminhtml/twofactor_manage/index";
		const PAGE_MANAGE_GRID      = "adminhtml/twofactor_manage/grid";
		const PAGE_MANAGE_UNBAN     = "adminhtml/twofactor_manage/unban";
		const PAGE_MANAGE_ENABLE    = "adminhtml/twofactor_manage/enable";
		const PAGE_MANAGE_DISABLE   = "adminhtml/twofactor_manage/disable";
		const PAGE_MANAGE_RESET     = "adminhtml/twofactor_manage/reset";
		const PAGE_CONFIGURE_INDEX  = "adminhtml/twofactor_configure/index";
		const PAGE_CONFIGURE_SAVE   = "adminhtml/twofactor_configure/save";

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

		/**
		 * This method takes in a frontname and controller and returns if the route is forbidden for
		 * a user that is already authenticated.
		 * @param       string              frontname           The frontname to evaluate
		 * @param       string              controller          The controller to evaluate
		 * @return      boolean                                 Is the route forbidden?
		 */
		public function isForbiddenRoutesAfterAuth ( $frontname, $controller ) {
			// Get current admin front name
			$adminFrontName = Mage::getConfig ()
				->getNode ("admin/routers/adminhtml/args/frontName");
			// Return if the controller is forbidden
			$allowed = array ( "twofactor_setup", "twofactor_login" );
			return $frontname === "$adminFrontName" && in_array ( $controller, $allowed );
		}

	}
