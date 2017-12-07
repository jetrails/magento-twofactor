<?php
 
    class JetRails_TwoFactor_Block_Adminhtml_Manage_Container_Grid extends Mage_Adminhtml_Block_Widget_Grid {
     
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

            $this->addColumn ( "id", array (
                "header"            =>  Mage::helper ("twofactor")->__("User ID"),
                "index"             =>  "user_id",
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
                "header"            =>  Mage::helper ("twofactor")->__("Email Address"),
                "index"             =>  "email",
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

            $this->addColumn ( "state", array (
                "header"            =>  Mage::helper ("twofactor")->__("2FA Setup State"),
                "index"             =>  "user_id",
                "width"             =>  "200px",
                "renderer"          =>  "twofactor/adminhtml_renderer_manage_state",
                "sortable"          =>  false,
                "filter"            =>  false
            ));

            $this->addColumn ( "enforced", array (
                "header"            =>  Mage::helper ("twofactor")->__("2FA Enforced On User"),
                "index"             =>  "user_id",
                "width"             =>  "200px",
                "renderer"          =>  "twofactor/adminhtml_renderer_manage_enforced",
                "sortable"          =>  false,
                "filter"            =>  false
            ));

            $this->addColumn ( "last_timestamp", array (
                "header"            =>  Mage::helper ("twofactor")->__("Last Authentication On"),
                "index"             =>  "user_id",
                "renderer"          =>  "twofactor/adminhtml_renderer_manage_timestamp",
                "filter"            =>  false
            ));

            $this->addColumn ( "last_address", array (
                "header"            =>  Mage::helper ("twofactor")->__("Last Authentication From"),
                "index"             =>  "user_id",
                "renderer"          =>  "twofactor/adminhtml_renderer_manage_address",
                "filter"            =>  false
            ));

            return parent::_prepareColumns ();
        }

        public function getGridUrl () {
            return $this->getUrl ( "*/*/grid", array ( "_current" => true ) );
        }

        public function getRowUrl ( $item ) {
            return false;
        }

        protected function _prepareMassaction () {
            $this->setMassactionIdField ("user_id");
            $this->getMassactionBlock ()->setFormFieldName ("ids");

            $this->getMassactionBlock ()->addItem ( "enforce", array (
                "label"    => Mage::helper ("twofactor")->__("Enforce 2FA On User"),
                "url"      => $this->getUrl ("*/*/enforce"),
                "selected" => true,
            ));

            $this->getMassactionBlock ()->addItem ( "unenforce", array (
                "label"    => Mage::helper ("twofactor")->__("Un-Enforce 2FA On User"),
                "url"      => $this->getUrl ("*/*/unenforce"),
                "selected" => false,
            ));

            $this->getMassactionBlock ()->addItem ( "unblock", array (
                "label"    => Mage::helper ("twofactor")->__("Unblock 2FA Account"),
                "url"      => $this->getUrl ("*/*/unblock"),
                "selected" => false,
            ));

            $this->getMassactionBlock ()->addItem ( "reset", array (
                "label"    => Mage::helper ("twofactor")->__("Reset 2FA Account"),
                "url"      => $this->getUrl ("*/*/reset"),
                "selected" => false,
            ));


            return $this;
        }

    }