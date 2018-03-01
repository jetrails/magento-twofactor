<?php

	/**
	 * Edit.php - This class exists to simply tell Magento's grid system where to look for the
	 * proper controller for the configure page.  It also specifies the proper block group within
	 * the constructor.
	 * @version         1.1.1
	 * @package         JetRails® TwoFactor
	 * @category        Configure
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Configure_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

		/**
		 * This constructor is important because it defines what controller and block group this
		 * grid container will be using.  It then calls the super constructor to take care of the
		 * reset.
		 */
		public function __construct () {
			// Call the super constructor
			parent::__construct ();
			// Define the controller and block group
			$this->_controller = "configure";
			$this->_blockGroup = "twofactor";
			// Remove buttons and update the save button text
			$this->_updateButton ( "save", "label", Mage::helper ("twofactor")->__("Save") );
			$this->_removeButton ("delete");
			$this->_removeButton ("back");
			$this->_removeButton ("reset");
		}

		/**
		 * This method simply defines the text that will be displayed above the form in the
		 * configure page.
		 * @return      string                                  Header text above form
		 */
		public function getHeaderText () {
			// Simply return the header text for the form
			return Mage::helper ("twofactor")->__("Configure 2FA Settings");
		}

	}