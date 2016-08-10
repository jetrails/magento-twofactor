<?php

	/**
	 * Verify.php - This function aids the Verify template.  This block offers helpful function that
	 * are used within the template, such as suppling the form action and stylesheet URL.
	 * @version         1.0.0
	 * @package         JetRails® TwoFactor
	 * @category        Template
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Template_Verify extends Mage_Adminhtml_Block_Template {

		/**
		 * This function simply returns the URL to the template's stylesheet in the skin file
		 * hierarchy.  This URL is publicly accessible.
		 * @return      string                                      Stylesheet URL
		 */
		protected function _getStyleURL () {
			// Simply return the CSS url
			return $this->getSkinUrl ("css/JetRails/TwoFactor/Verify.css");
		}

		/**
		 * This function returns the form action URL to the login verify controller.  It is used to
		 * verify whether the user has entered in a valid TOTP PIN.
		 * @return      string                                      URL to login/verify controller
		 */
		protected function _getFormSubmitURL () {
			// Simply return the controller URL for login/verify
			return $this->getUrl ("jetrails_twofactor/login/verify");
		}

	}

?>