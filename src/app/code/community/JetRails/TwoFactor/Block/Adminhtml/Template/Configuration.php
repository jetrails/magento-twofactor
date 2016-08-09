<?php

	/**
	 * Configuration.php - 
	 * @version         1.0.0
	 * @package         JetRails® TwoFactor
	 * @category        Template
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Template_Configuration extends Mage_Adminhtml_Block_Template {

 		/**
		 * 
		 * @return
		 */
		protected function _getVerifyURL () {
			// Return the URL to the controller that handles enabling TFA
			return Mage::helper ("adminhtml")->getUrl ("jetrails_twofactor/configuration/enable");
		}

 		/**
		 * 
		 * @return
		 */
		protected function _getGenerateURL () {
			// Return the URL to the controller that handles enabling TFA
			return Mage::helper ("adminhtml")->getUrl ("jetrails_twofactor/configuration/generate");
		}

 		/**
		 * 
		 * @return
		 */
		protected function _getDisableURL () {
			// Return the URL to the controller that handles enabling TFA
			return Mage::helper ("adminhtml")->getUrl ("jetrails_twofactor/configuration/disable");
		}

 		/**
		 * 
		 * @return
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
		 * 
		 * @return
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