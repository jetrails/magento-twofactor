<?php
 
	class JetRails_TwoFactor_Block_Adminhtml_Manage_Container extends Mage_Adminhtml_Block_Widget_Grid_Container {

		public function __construct () {
			$this->_controller = "adminhtml_manage_container";
			$this->_blockGroup = "twofactor";
			$this->_headerText = Mage::helper ("twofactor")->__("Manage 2FA Accounts");
			parent::__construct ();
			$this->_removeButton ("add");
		}

	}