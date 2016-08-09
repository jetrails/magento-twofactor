<?php

	/**
	 * Enabled.php - 
	 * @version         1.0.0
	 * @package         JetRails® TwoFactor
	 * @category        Status
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Status_Enabled extends Mage_Adminhtml_Block_System_Config_Form_Field {
		
 		/**
		 * 
		 * @return
		 */
		protected function _getElementHtml ( Varien_Data_Form_Element_Abstract $element ) {
			// Set the element using the inherited class
			$this->setElement ( $element );
			// Load the data helper
			$Data = Mage::helper ("twofactor/Data");
			// Get the user id using the session
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			// See if the program is installed
			$enabled = $Data->isEnabled ( $uid );
			// Initialize the block element
			$block = $this->getLayout ()
				->createBlock ("adminhtml/widget_button")
				->setType ("button")
				->setClass ( "jetrails-status-" . ( $enabled ? "pass" : "fail" ) )
				->setLabel ( ( $enabled ? "" : "Not " ) . "Enabled" );
			// Return the block's HTML
			return $block->toHtml ();
		}

	}

?>