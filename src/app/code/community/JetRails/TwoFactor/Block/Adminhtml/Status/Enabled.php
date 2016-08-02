<?php

	class JetRails_TwoFactor_Block_Adminhtml_Status_Enabled extends Mage_Adminhtml_Block_System_Config_Form_Field {
		
		protected function _getElementHtml ( Varien_Data_Form_Element_Abstract $element ) {
			// Set the element using the inherited class
			$this->setElement ( $element );
			// Initialize the block element
			$block = $this->getLayout ()
				->createBlock ("adminhtml/widget_button")
				->setType ("button")
				->setClass ("jetrails_twofactor_status_pass")
				->setLabel ("Enabled");
			// Initialize the helper class and the uid
			$Data = Mage::helper ("twofactor/Data");
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			// Get the status of the configuration
			$status = $Data->isEnabled ( $uid );
			// Check to see if the status has errors
			if ( !$status ) {
				// Update the class and label of the block
				$block
					->setClass ("jetrails_twofactor_status_fail")
					->setLabel ( "Not Enabled" );
			}
			// Return the block's HTML
			return $block->toHtml ();
		}

	}

?>