<?php
 
    class JetRails_TwoFactor_Block_Adminhtml_Enforce_Container_Grid extends Mage_Adminhtml_Block_Widget_Grid {
     
        public function __construct () {
            parent::__construct();
            $this->setId ("role_id");
            $this->setDefaultSort ("role_id");
            $this->setDefaultDir ("DESC");
            $this->setUseAjax ( true );
            $this->setSaveParametersInSession ( true );
            $this->setFilterVisibility ( false );
        }

        protected function _prepareCollection () {
            $collection = Mage::getModel ("admin/roles")->getCollection ();
            $this->setCollection ( $collection );
            return parent::_prepareCollection ();
        }

        protected function _prepareColumns () {

            $this->addColumn ( "role_id", array (
                "header"            =>  Mage::helper ("twofactor")->__("Role ID"),
                "index"             =>  "role_id",
                "type"              =>  "text",
                "filter"            =>  false
            ));

            $this->addColumn ( "role_name", array (
                "header"            =>  Mage::helper ("twofactor")->__("Role Name"),
                "index"             =>  "role_name",
                "type"              =>  "text",
                "filter"            =>  false
            ));

            $this->addColumn ( "2fa_action", array (
                "header"            =>  Mage::helper ("twofactor")->__("2FA Enforce"),
                "index"             =>  "role_id",
                "type"              =>  "text",
                
                "align"             =>  "center",
                "renderer"          =>  "twofactor/adminhtml_renderer_enforce_action",
                
                "filter"            =>  false,
                "sortable"          =>  false
            ));

            return parent::_prepareColumns ();
        }

        public function getGridUrl () {
            return $this->getUrl ( "*/*/grid", array ( "_current" => true ) );
        }

        public function getRowUrl ( $item ) {
            return false;
        }

    }