<?php

	/**
	 * Configuration.php - This class extends the system config form field class and it overrides a
	 * function that is responsible for retrieving HTML that represents the internally defined form
	 * field.  We override the function to supply custom HTML within the form field's space.
	 * @version         1.0.5
	 * @package         JetRails® TwoFactor
	 * @category        Panel
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Panel_Configuration extends Mage_Adminhtml_Block_System_Config_Form_Field {

		/**
		 * This function overrides a function from the parent class, which is responsible for
		 * retrieving the HTML string for a given passed element.  This allows us to provide custom
		 * HTML for a form element using template blocks.
		 * @param       Varien_Data_Form_Element_Abstract       $element        Form element to eval
		 * @return      string                                                  HTML representation
		 */
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