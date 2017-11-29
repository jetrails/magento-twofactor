<?php
 
    class JetRails_TwoFactor_Block_Adminhtml_Reset_Container_Grid extends Mage_Adminhtml_Block_Widget_Grid {
     
        public function __construct () {
            parent::__construct();
            $this->setId ("user_id");
            $this->setDefaultSort ("user_id");
            $this->setDefaultDir ("DESC");
            $this->setUseAjax ( true );
            $this->setSaveParametersInSession ( true );
            $this->setFilterVisibility ( false );
        }

        protected function _prepareCollection () {
            $collection = Mage::getModel ("admin/user")->getCollection ();
            $this->setCollection ( $collection );
            return parent::_prepareCollection ();
        }

        protected function _prepareColumns () {

            $this->addColumn ( "user_id", array (
                "header"            =>  Mage::helper ("twofactor")->__("User ID"),
                "index"             =>  "user_id",
                "type"              =>  "text",
                "filter"            =>  false
            ));

            $this->addColumn ( "firstname", array (
                "header"            =>  Mage::helper ("twofactor")->__("Firstname"),
                "index"             =>  "firstname",
                "type"              =>  "text",
                "filter"            =>  false
            ));

            $this->addColumn ( "lastname", array (
                "header"            =>  Mage::helper ("twofactor")->__("Lastname"),
                "index"             =>  "lastname",
                "type"              =>  "text",
                "filter"            =>  false
            ));

            $this->addColumn ( "username", array (
                "header"            =>  Mage::helper ("twofactor")->__("Username"),
                "index"             =>  "username",
                "type"              =>  "text",
                "filter"            =>  false
            ));

            $this->addColumn ( "email", array (
                "header"            =>  Mage::helper ("twofactor")->__("Email"),
                "index"             =>  "email",
                "type"              =>  "text",
                "filter"            =>  false
            ));

            $this->addColumn ( "2fa_enforced", array (
                "header"            =>  Mage::helper ("twofactor")->__("2FA Enforced"),
                "index"             =>  "user_id",
                "renderer"          =>  "twofactor/adminhtml_renderer_reset_enforced",
                "type"              =>  "text",
                "filter"            =>  false,
                "sortable"          =>  false
            ));

            $this->addColumn ( "2fa_state", array (
                "header"            =>  Mage::helper ("twofactor")->__("2FA State"),
                "index"             =>  "user_id",
                "renderer"          =>  "twofactor/adminhtml_renderer_reset_state",
                "type"              =>  "text",
                "filter"            =>  false,
                "sortable"          =>  false
            ));

            $this->addColumn ( "2fa_action", array (
                "header"            =>  Mage::helper ("twofactor")->__("Reset 2FA"),
                "width"             =>  "100",
                "type"              =>  "text",
                "filter"            =>  false,
                "sortable"          =>  false,
                "align"             =>  "center",
                "renderer"          =>  "twofactor/adminhtml_renderer_reset_action",
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