<?php

	 /**
	 * Container.php - This class exists to simply tell Magento's grid system where to look for the
	 * proper controller for the configure page.  It also specifies the proper block group within
	 * the constructor.
	 * @version         1.1.3
	 * @package         JetRails® TwoFactor
	 * @category        Manage
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Manage_Container extends Mage_Adminhtml_Block_Widget_Grid_Container {

		/**
		 * This constructor is important because it defines what controller and block group this
		 * grid container will be using.  It then calls the super constructor to take care of the
		 * reset.
		 */
		public function __construct () {
			// Define the controller and block group
			$this->_controller = "manage_container";
			$this->_blockGroup = "twofactor";
			// Change the header text
			$this->_headerText = Mage::helper ("twofactor")->__("Manage 2FA Accounts");
			// Call the super constructor and remove the add button
			parent::__construct ();
			$this->_removeButton ("add");
		}

	}
