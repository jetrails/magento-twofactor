<?php

    class JetRails_TwoFactor_Block_Adminhtml_Configure_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
        
        public function __construct () {
            parent::__construct ();
            $this->_controller = "configure";
            $this->_blockGroup = "twofactor";
            $this->_updateButton ( "save", "label", Mage::helper ("twofactor")->__("Save") );
            $this->_removeButton ("delete");
            $this->_removeButton ("back");
            $this->_removeButton ("reset");
        }

        public function getHeaderText () {
            return Mage::helper ("twofactor")->__('Configure 2FA Settings');
        }

    }