<?php

	class JetRails_TwoFactor_Block_Adminhtml_Template_Verify extends Mage_Adminhtml_Block_Template {
		
		protected function _getStyleURL () {
			// Get the stylesheet associated with this module
			return $this->getSkinUrl ("css/JetRails/TwoFactor/Verify.css");
		}

		protected function _getFormSubmitURL () {
			// Get the action URL for the verify form
			return Mage::helper ("adminhtml")->getUrl ("jetrails_twofactor/login/verify");
		}

	}

?>