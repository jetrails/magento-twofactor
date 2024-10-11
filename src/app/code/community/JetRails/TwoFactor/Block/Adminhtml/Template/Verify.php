<?php

	/**
	 * Verify.php - This function aids the Verify template.  This block offers helpful function that
	 * are used within the template, such as suppling the form action and stylesheet URL.
	 * @version         1.1.5
	 * @package         JetRails® TwoFactor
	 * @category        Template
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Template_Verify extends Mage_Adminhtml_Block_Template {

		/**
		 * This method gets and instance of the helper data class for this module.  Using this
		 * object, we get the configured number of days a use should be authenticated for if the
		 * "Remember me option is selected".
		 * @return      int                                     Remember me duration
		 */
		public function getRememberMe () {
			// Get the data helper instance, and return remember me duration
			$data = Mage::helper ("twofactor/data");
			return $data->getData () ["remember_me"];
		}

		/**
		 * This method returns the correct action url for the form which is defined in the template.
		 * @return      string                                  Form action URL
		 */
		public function getFormUrl () {
			// Get the verify action url and return it
			$page = Mage::getSingleton ("twofactor/page");
			return $this->getUrl ( $page::PAGE_LOGIN_VERIFY );
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
