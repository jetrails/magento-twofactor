<?php
 
	class JetRails_TwoFactor_Block_Adminhtml_Reset_Container extends Mage_Adminhtml_Block_Widget_Grid_Container {

		public function __construct () {
			$this->_controller = "adminhtml_reset_container";
			$this->_blockGroup = "twofactor";
			$this->_headerText = Mage::helper ("twofactor")->__("Reset 2FA Accounts");
			parent::__construct ();
			$this->_removeButton ("add");
		}

	}