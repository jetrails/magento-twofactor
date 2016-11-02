<?php

	/**
	 * ConfigurationController.php - This controller offers actions that aid in turning the TFA
	 * service on and off, as well as re-generate the user's secret.  It is used within the action
	 * panel in the system configuration panel in Magento.  There is also the "render" action that
	 * will render out the configuration menu when it is clicked under the admin menu tab.  This
	 * exists for admins that are not allowed to access the configuration page.
	 * @version         1.0.3
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_ConfigurationController extends Mage_Adminhtml_Controller_Action {

		/**
		 * This action will render out the configuration options when the option is selected under
		 * the JetRails admin menu tab.
		 * @return      void
		 */
		public function renderAction () {
			// Load the layout, and set the twofactor tab to be selected
			$this->loadLayout ()->_setActiveMenu ("twofactor_menu");
			// Create the custom block that we will insert
			$custom = $this->getLayout ()
				->createBlock ("twofactor/Adminhtml_Template_Configuration")
				->setTemplate ("JetRails/TwoFactor/Configuration.phtml");
			// Add the contents to the body
			$this->_addContent ( $custom );
			// Render the layout
			$this->renderLayout ();
		}

		/**
		 * This action enables the TFA given that TFA is currently disabled and the passed TOTP pin
		 * is valid, thus proving that the user has successfully set up their TFA account.
		 * @return      void
		 */
		public function enableAction () {
			// Get the user id using the Mage session
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			// Initialize the Data helper class and the TOTP helper class
			$Data = Mage::helper ("twofactor/Data");
			$TOTP = Mage::helper ("twofactor/TOTP");
			// Check to see if it is enabled and verified first
			if ( !$Data->isEnabled ( $uid ) ) {
				// Save the passed pin
				$pin = Mage::app ()->getRequest ()->getParam ("pin");
				// Initialize the TOTP helper class with secret
				$TOTP->initialize ( $Data->getSecret ( $uid ) );
				// Make sure that the pins match
				if ( $pin == $TOTP->pin () ) {
					// Set enable flag to true
					$Data->setEnabled ( $uid, true );
					// Set a success message
					Mage::getSingleton ("core/session")->addSuccess (
						"Successfully enabled two factor authentication."
					);
					// Log user out on enable and redirect user to login page
					$session = Mage::getSingleton ("admin/session");
					$session->unsetAll ();
					$session->getCookie ()->delete ( $session->getSessionName () );
					$this->_redirect ("adminhtml");
					return;
				}
				// Otherwise if the pin is incorrect
				else {
					// Set a notice message
					Mage::getSingleton ("core/session")->addError (
						"Failed to enable two factor authentication, due to an invalid pin."
					);
				}
			}
			// Otherwise, it is enabled already
			else {
				// Set a notice message
				Mage::getSingleton ("core/session")->addError (
					"Two factor authentication is already enabled."
				);
			}
			// Go back to the referrer page
			$this->_redirectReferer ();
		}

		/**
		 * This action disables TFA for the current logged in user, given the fact that TFA is
		 * currently enabled and that passed TOTP pin is valid, thus proving that it is the
		 * authorized user requesting to disable this service.
		 * @return      void
		 */
		public function disableAction () {
			// Get the user id using the Mage session
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			// Initialize the Data helper class, the TOTP helper class, and the Cookie class
			$Cookie = Mage::helper ("twofactor/Cookie");
			$Data = Mage::helper ("twofactor/Data");
			$TOTP = Mage::helper ("twofactor/TOTP");
			// Check to see if it is enabled and verified first
			if ( $Data->isEnabled ( $uid ) ) {
				// Save the passed pin
				$pin = Mage::app ()->getRequest ()->getParam ("pin");
				// Initialize the TOTP helper class with secret
				$TOTP->initialize ( $Data->getSecret ( $uid ) );
				// Make sure that the pins match
				if ( $pin == $TOTP->pin () ) {
					// Disable all items
					$Data->setEnabled ( $uid, false );
					$Data->setSecret ( $uid, "" );
					// Delete cookie if set
					$Cookie->delete ();
					// Set a success message
					Mage::getSingleton ("core/session")->addSuccess (
						"Successfully disabled two factor authentication."
					);
				}
				// Otherwise if the pin is incorrect
				else {
					// Set a notice message
					Mage::getSingleton ("core/session")->addError (
						"Failed to disable two factor authentication, due to an invalid pin."
					);
				}
			}
			// Otherwise, it is disabled
			else {
				// Set a notice message
				Mage::getSingleton ("core/session")->addError (
					"Two factor authentication is not enabled."
				);
			}
			// Go back to the referrer page
			$this->_redirectReferer ();
		}

	}

?>