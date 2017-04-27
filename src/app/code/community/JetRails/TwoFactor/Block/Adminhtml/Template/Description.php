<?php

	/**
	 * Description.php - This class provides useful helper functions for the templates displayed
	 * in the configuration panel and serves the appropriate descriptions based on enabled state.
	 * @version         1.0.5
	 * @package         JetRails® TwoFactor
	 * @category        Template
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Template_Description extends Mage_Adminhtml_Block_Template {

		/**
		 * this function simply returns the current user's secret, using the logged in user id
		 * stored in the session.
		 * @return      string                                      The user's TOTP secret
		 */
		protected function _getSecret () {
			// Load the TOTP helper class and the Data class
			$Data = Mage::helper ("twofactor/Data");
			$TOTP = Mage::helper ("twofactor/TOTP");
			// Load the uid from Mage session
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			// Check to see if a secret is already set
			if ( $Data->isEnabled ( $uid ) ) {
				// Return the secret
				return $Data->getSecret ( $uid );
			}
			// Initialize TOTP helper
			$TOTP->initialize ();
			// Store it in the database
			$Data->setSecret ( $uid, $TOTP->getSecret () );
			// Return a new secret
			return $TOTP->getSecret ();
		}

		/**
		 * This function determines if two factor authentication is enabled.  It does this using the
		 * Data helper class.
		 * @return      boolean                                     Is two factor auth enabled?
		 */
		protected function _isEnabled () {
			// Load the Data helper as well as the uid, so we can determine which template to load
			$Data = Mage::helper ("twofactor/Data");
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			// Return whether it is enabled
			return $Data->isEnabled ( $uid );
		}

		/**
		 * This function, depending on the enable status of the two factor service, returns either
		 * the enabled or disabled state template.
		 * @return      string                                      HTML contents of form
		 */
		protected function _loadContents () {
			// Get a generic layout
			$block = Mage::app ()->getLayout ();
			// Create a block that will help all the templates
			$block = $block->createBlock ("twofactor/Adminhtml_Template_Description");
			// Check to see if the state is enabled
			if ( !$this->_isEnabled () ) {
				// Load the template that describes the enabled state
				$block = $block->setTemplate ("JetRails/TwoFactor/Description-Disabled.phtml");
			}
			// If the state is disabled
			else {
				// Load the template that describes the disabled state
				$block = $block->setTemplate ("JetRails/TwoFactor/Description-Enabled.phtml");
			}
			// Return the HTML
			return $block->toHtml ();
		}

	}

?>