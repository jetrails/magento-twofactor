<?php

	class JetRails_TwoFactor_Block_Adminhtml_Panel_Configuration extends Mage_Adminhtml_Block_System_Config_Form_Field {

		protected function _getElementHtml ( Varien_Data_Form_Element_Abstract $element ) {
			// Get a generic layout
			$block = Mage::app ()->getLayout ();
			// Create a block that will help all the templates
			$block = $block->createBlock ("twofactor/Adminhtml_Template_Configuration");
			// Load the Data helper as well as the uid, so we can determine which template to load
			$Data = Mage::helper ("twofactor/Data");
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			// Check to see if the state is enabled
			if ( !$Data->isEnabled ( $uid ) ) {
				// Load the template that describes the enabled state
				$block = $block->setTemplate ("JetRails/TwoFactor/State-Disabled.phtml");
			}
			// If the state is disabled
			else {
				// Load the template that describes the disabled state
				$block = $block->setTemplate ("JetRails/TwoFactor/State-Enabled.phtml");
			}
			// Return the HTML
			return $block->toHtml ();
		}

	}

?>