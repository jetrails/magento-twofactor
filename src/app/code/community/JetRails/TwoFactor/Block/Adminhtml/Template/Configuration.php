<?php

	/**
	 * Configuration.php - This class provides useful helper functions for the templates displayed
	 * in the Action panel in the system configuration page.  It is used to get controller actions
	 * URLs and retrieving the TOTP secret along with the URL to the QR code image containing the
	 * user's secret.
	 * @version         1.0.0
	 * @package         JetRails® TwoFactor
	 * @category        Template
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Template_Configuration extends Mage_Adminhtml_Block_Template {

		/**
		 * This function returns the url to the configuration controller with the enable action.
		 * @return      string                                      URL to configuration/enable
		 */
		protected function _getEnableURL () {
			// Return the URL to the controller that handles enabling TFA
			return Mage::helper ("adminhtml")->getUrl ("jetrails_twofactor/configuration/enable");
		}

		/**
		 * This function returns the url to the configuration controller with the generate action.
		 * @return      string                                      URL to configuration/generate
		 */
		protected function _getGenerateURL () {
			// Return the URL to the controller that handles enabling TFA
			return Mage::helper ("adminhtml")->getUrl ("jetrails_twofactor/configuration/generate");
		}

		/**
		 * This function returns the url to the configuration controller with the disable action.
		 * @return      string                                      URL to configuration/disable
		 */
		protected function _getDisableURL () {
			// Return the URL to the controller that handles enabling TFA
			return Mage::helper ("adminhtml")->getUrl ("jetrails_twofactor/configuration/disable");
		}

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
			if ( trim ( $Data->getSecret ( $uid ) ) == "" ) {
				// Initialize a new secret
				$TOTP->initialize ();
				// Store it in the database
				$Data->setSecret ( $uid, $TOTP->getSecret () );
			}
			// Otherwise, we will use the already generated secret
			else {
				// Initialize with the stored secret
				$TOTP->initialize ( $Data->getSecret ( $uid ) );
			}
			// Return the secret
			return $TOTP->getSecret ();
		}

		/**
		 * This function returns a URL to the QR code image that contains the user's secret.  The
		 * Google charts API is used for this request.
		 * @return      string                                      URL to QR code image
		 */
		protected function _getQRCode () {
			// Load the TOTP helper class and the Data class
			$Data = Mage::helper ("twofactor/Data");
			$TOTP = Mage::helper ("twofactor/TOTP");
			// Load the uid from Mage session as well as the user email
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			$email = Mage::getSingleton ("admin/session")->getUser ()->getEmail ();
			// Check to see if a secret is already set
			if ( trim ( $Data->getSecret ( $uid ) ) == "" ) {
				// Initialize a new secret
				$TOTP->initialize ();
				// Store it in the database
				$Data->setSecret ( $uid, $TOTP->getSecret () );
			}
			// Return the QR code URL
			return $TOTP->QRCode ( $email, "JetRails", $Data->getSecret ( $uid ), 400 );
		}

	}

?>