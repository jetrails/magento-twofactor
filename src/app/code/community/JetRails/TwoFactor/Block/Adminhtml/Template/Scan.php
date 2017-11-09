<?php

	/**
	 * Scan.php - This template block is used in hand with the associated template file and it
	 * is used to prepare information for the template file.
	 * @version         1.0.7
	 * @package         JetRails® TwoFactor
	 * @category        Template
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Template_Scan extends Mage_Adminhtml_Block_Template {

		/**
		 * This method simply interfaces with the authentication method and returns the admin user's
		 * TOTP secret.
		 * @return      string                                      The user's TOTP secret
		 */
		public function getSecret () {
			// Get authentication model
			$auth = Mage::getSingleton ("twofactor/auth");
			// Return secret saved in authentication model
			return $auth->getSecret ();
		}

		/**
		 * This function returns a URL to the QR code image that contains the user's secret.  The
		 * Google charts API is used for this request.
		 * @return      string                                      URL to QR code image
		 */
		public function getQRCode () {
			// Load the TOTP helper class and authentication model
			$auth = Mage::getSingleton ("twofactor/auth");
			$totp = Mage::helper ("twofactor/totp");
			// Load the email from admin session and get a secret
			$email = Mage::getSingleton ("admin/session")->getUser ()->getEmail ();
			// Return the QR code URL
			return $totp->QRCode ( $email, "JetRails", $auth->getSecret (), 400 );
		}

		/**
		 * This method returns the correct action url for the form which is defined in the template.
		 * @return      string                                  Form action URL
		 */
		public function getFormUrl () {
			// Get page model and return the scan url
			$page = Mage::getSingleton ("twofactor/page");
			return Mage::getUrl ( $page::PAGE_SETUP_SCAN );
		}

		/**
		 * This method gets a form key from the core session model.  This form key is used to submit
		 * a custom form that is defined in the template.
		 * @return      string                                  Valid form key
		 */
		public function getFormKey () {
			// Ask admin session for a form key and return it
			return Mage::getSingleton ("core/session")->getFormKey ();
		}

	}