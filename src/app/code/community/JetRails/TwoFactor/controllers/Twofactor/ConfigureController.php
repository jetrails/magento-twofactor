<?php

	/**
	 * ConfigureController.php - This controller renders the "Configure 2FA Accounts" page that can
	 * be found in the "JetRails/Two-Factor Authentication" menu tab.  It handles the rendering of
	 * that page as well as all the actions that are submitted with the grid container that the
	 * index action renders.
	 * @version         1.1.5
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Twofactor_ConfigureController extends Mage_Adminhtml_Controller_Action {

		/**
		 * This method simply asks Magento's ACL if the logged in user is allowed to see the
		 * configure page that belongs to this module.
		 * @return      boolean                                 Is the user allowed to see page?
		 */
		protected function _isAllowed () {
			// Is user allowed to manage 2FA accounts?
			$session = Mage::getSingleton ("admin/session");
			return $session->isAllowed ("jetrails/twofactor/configure");
		}

		/**
		 * This method takes in an array of values and returns true if they are all natural numbers
		 * embedded as a string.  Otherwise false will be returned.  This method is used to sanitize
		 * all the values that are passed to the save action.
		 * @param       array               values              The values to evaluate
		 * @return      boolean                                 Are values all valid?
		 */
		protected function _validate ( $values ) {
			// Loop through each value
			foreach ( $values as $value ) {
				$valid =
					strval ( intval ( $value ) ) == strval ( $value ) &&
					is_int ( intval ( $value ) ) &&
					intval ( $value ) > 0;
				if ( !$valid ) return false;
			}
			// By default return true
			return true;
		}

		/**
		 * This action setup the grid container for the configure page.  It defines the page title,
		 * active tab, initializes session methods, and finally renders out the page as defined in
		 * they twofactor.xml layout file.
		 * @return      void
		 */
		public function indexAction () {
			// Set the title for the page
			$this->_title ( $this->__("JetRails") );
			$this->_title ( $this->__("Two-Factor Authentication") );
			$this->_title ( $this->__("Configure 2FA Settings") );
			// Load layout, add the content, set active tab, and render layout
			$this->loadLayout ();
			$this->_initLayoutMessages ("admin/session");
			$this->_setActiveMenu ("jetrails/twofactor");
			$this->_addContent ( $this->getLayout ()->createBlock ("twofactor/configure_edit") );
			$this->renderLayout ();
		}

		/**
		 * This method simply sanitizes all the values that are passed to it from the index action
		 * page.  It then saves it internally using the appropriate XPATHS.
		 * @return      void
		 */
		public function saveAction () {
			// Get an instance of the data helper
			$data = Mage::helper ("twofactor/data");
			$page = Mage::getModel ("twofactor/page");
			// Get configuration variables from request
			$rememberMe = $this->getRequest ()->getParam ( "remember_me", null );
			$banAttempts = $this->getRequest ()->getParam ( "ban_attempts", null );
			$banTime = $this->getRequest ()->getParam ( "ban_time", null );
			// Check to see that passed values are valid and in range
			if ( $this->_validate ( array ( $rememberMe, $banAttempts, $banTime ) ) ) {
				// Save values into the configuration table
				Mage::getConfig ()->saveConfig (
					$data::XPATH_REMEMBER_ME, $rememberMe, $data::XPATH_SCOPE, 0
				);
				Mage::getConfig ()->saveConfig (
					$data::XPATH_BAN_ATTEMPTS, $banAttempts, $data::XPATH_SCOPE, 0
				);
				Mage::getConfig ()->saveConfig (
					$data::XPATH_BAN_TIME, $banTime, $data::XPATH_SCOPE, 0
				);
				// Attach a success message to the session
				Mage::getSingleton ("admin/session")->addSuccess (
					Mage::helper ("twofactor")->__("Successfully saved settings")
				);
				// Invalidate config cache
				Mage::app ()->getCacheInstance ()->cleanType ("config");
			}
			else {
				// Attach an error message to the session
				Mage::getSingleton ("admin/session")->addError (
					Mage::helper ("twofactor")->__("Values must be integer values greater than 0")
				);
			}
			// Redirect back to the configure page
			$this->getResponse ()->setRedirect ( $this->getUrl ( $page::PAGE_CONFIGURE_INDEX ) );
		}

	}
