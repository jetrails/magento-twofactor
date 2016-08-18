<?php

	/**
	 * Account.php - This class extends the system config form field class and it overrides a
	 * function that is responsible for retrieving HTML that represents the internally defined form
	 * field.  We override the function to supply custom HTML within the form field's space.
	 * @version         1.0.2
	 * @package         JetRails® TwoFactor
	 * @category        Info
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Info_Account extends Mage_Adminhtml_Block_System_Config_Form_Field {

		/**
		 * This function overrides a function from the parent class, which is responsible for
		 * retrieving the HTML string for a given passed element.  This allows us to provide custom
		 * HTML for a form element using template blocks.
		 * @param       Varien_Data_Form_Element_Abstract       $element        Form element to eval
		 * @return      string                                                  HTML representation
		 */
		protected function _getElementHtml ( Varien_Data_Form_Element_Abstract $element ) {
			// Set the element using the inherited class
			$this->setElement ( $element );
			// Get the user email
			$email = Mage::getSingleton ("admin/session")->getUser ()->getEmail ();
			// Initialize the block element
			$block = $this->getLayout ()
				->createBlock ("adminhtml/widget_button")
				->setType ("button")
				->setClass ( "jetrails-info-panel"  )
				->setLabel ( $email );
			// Return the block's HTML
			return $block->toHtml ();
		}

	}

?>