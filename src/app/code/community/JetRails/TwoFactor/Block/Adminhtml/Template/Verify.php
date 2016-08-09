<?php

	/**
	 * Verify.php - 
	 * @version         1.0.0
	 * @package         JetRails® TwoFactor
	 * @category        Template
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Template_Verify extends Mage_Adminhtml_Block_Template {

 		/**
		 * 
		 * @return
		 */
		protected function _getStyleURL () {
			// Simply return the CSS url
			return $this->getSkinUrl ("css/JetRails/TwoFactor/Verify.css");
		}

 		/**
		 * 
		 * @return
		 */
		protected function _getFormSubmitURL () {
			// Simply return the controller URL for login/verify
			return $this->getUrl ("jetrails_twofactor/login/verify");
		}

	}

?>