<?php
 
	class JetRails_TwoFactor_Block_Adminhtml_Enforce_Container extends Mage_Adminhtml_Block_Widget_Grid_Container {

		public function __construct () {
			$this->_controller = "adminhtml_enforce_container";
			$this->_blockGroup = "twofactor";
			$this->_headerText = Mage::helper ("twofactor")->__("Enforce 2FA");
			parent::__construct ();
			$this->_removeButton ("add");
		}

	}