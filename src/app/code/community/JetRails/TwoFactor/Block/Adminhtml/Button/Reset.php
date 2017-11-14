<?php

	/**
	 * Reset.php - This class is responsible for generating the HTML that is displayed in the system
	 * config section of the admin area.  The reset button simply resets the currently logged in
	 * user's state.  This class defines the HTML for the widget element, and it also un-binds it
	 * from any store view.
	 * @version         1.0.8
	 * @package         JetRails® TwoFactor
	 * @category        Button
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Button_Reset extends Mage_Adminhtml_Block_System_Config_Form_Field {

		/**
		 * This method exists to simply be the middle man and un-scope the button from any stores in
		 * the config.  Since this is a global user option, it should not be binded to any store.
		 * @param       Varien_Data_Form_Element_Abstract   element     Form field element instance
		 * @return                                                      HTML to return for button
		 */
		public function render ( Varien_Data_Form_Element_Abstract $element ) {
			// Remove scope label
			$element->unsScope ()->unsCanUseWebsiteValue ()->unsCanUseDefaultValue ();
			return parent::render ( $element );
		}

		/**
		 * In this method we construct the button from a widget block, set it's desired options, and
		 * return the HTML that is generated from it.
		 * @param       Varien_Data_Form_Element_Abstract   element     Form field element instance
		 * @return      string                                          HTML to return for button
		 */
		protected function _getElementHtml ( Varien_Data_Form_Element_Abstract $element ) {
			// Set the passed element
			$this->setElement ( $element );
			// Get page model and get the URL from the reset page
			$page = Mage::getSingleton ("twofactor/page");
			$url = $this->getUrl ( $page::PAGE_SETUP_RESET );
			// Construct the button and return the HTML
			return $this
				->getLayout ()
				->createBlock ("adminhtml/widget_button")
				->setType ("button")
				->setClass ("scalable")
				->setLabel ( $this->__("Reset") )
				->setOnClick ("setLocation ('$url')")
				->toHtml ();
		}

	}