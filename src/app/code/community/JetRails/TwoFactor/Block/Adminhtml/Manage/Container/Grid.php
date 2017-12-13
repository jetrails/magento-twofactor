<?php
 
    class JetRails_TwoFactor_Block_Adminhtml_Manage_Container_Grid extends Mage_Adminhtml_Block_Widget_Grid {
     
        /**
         * 
         */
        public function __construct () {
            //
            parent::__construct ();
            //
            $this->setId ("username");
            $this->setDefaultSort ("username");
            $this->setDefaultDir ("ASC");
            $this->setUseAjax ( true );
            $this->setSaveParametersInSession ( true );
            $this->setFilterVisibility ( false );
        }

        /**
         * 
         */
        protected function _prepareCollection () {
            //
            $collection = Mage::getModel ("admin/user")->getCollection ();
            $this->setCollection ( $collection );
            return parent::_prepareCollection ();
        }

        /**
         * 
         */
        protected function _prepareColumns () {
            //
            $this->addColumn ( "username", array (
                "header"            =>  Mage::helper ("twofactor")->__("Username"),
                "index"             =>  "username",
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
            $this->addColumn ( "email", array (
                "header"            =>  Mage::helper ("twofactor")->__("Email Address"),
                "index"             =>  "email",
                "type"              =>  "text",
                "filter"            =>  false
            ));
            $this->addColumn ( "last_timestamp", array (
                "header"            =>  Mage::helper ("twofactor")->__("Last Authenticated On"),
                "index"             =>  "user_id",
                "renderer"          =>  "twofactor/renderer_manage_timestamp",
                "filter"            =>  false
            ));
            $this->addColumn ( "last_address", array (
                "header"            =>  Mage::helper ("twofactor")->__("Last Authenticated From"),
                "index"             =>  "user_id",
                "renderer"          =>  "twofactor/renderer_manage_address",
                "filter"            =>  false
            ));
            $this->addColumn ( "status", array (
                "header"            =>  Mage::helper ("twofactor")->__("2FA Enabled"),
                "index"             =>  "user_id",
                "width"             =>  "200px",
                "renderer"          =>  "twofactor/renderer_manage_status",
                "sortable"          =>  false,
                "filter"            =>  false
            ));
            $this->addColumn ( "state", array (
                "header"            =>  Mage::helper ("twofactor")->__("2FA State"),
                "index"             =>  "user_id",
                "width"             =>  "200px",
                "renderer"          =>  "twofactor/renderer_manage_state",
                "sortable"          =>  false,
                "filter"            =>  false
            ));
            //
            return parent::_prepareColumns ();
        }

        public function getGridUrl () {
            // Return the grid action route 
            $page = Mage::getModel ("twofactor/page");
            return $this->getUrl ( $page::PAGE_MANAGE_GRID, array ( "_current" => true ) );
        }

        public function getRowUrl ( $item ) {
            // Disable clicking on rows (edit)
            return false;
        }

        protected function _prepareMassaction () {
            //
            $page = Mage::getModel ("twofactor/page");
            //
            $this->setMassactionIdField ("user_id");
            $this->getMassactionBlock ()->setFormFieldName ("ids");
            //
            $this->getMassactionBlock ()->addItem ( "enable", array (
                "label"    => Mage::helper ("twofactor")->__("Enable"),
                "url"      => $this->getUrl ( $page::PAGE_MANAGE_ENABLE ),
                "selected" => true,
            ));
            $this->getMassactionBlock ()->addItem ( "disable", array (
                "label"    => Mage::helper ("twofactor")->__("Disable"),
                "url"      => $this->getUrl ( $page::PAGE_MANAGE_DISABLE ),
                "selected" => false,
            ));
            $this->getMassactionBlock ()->addItem ( "unban", array (
                "label"    => Mage::helper ("twofactor")->__("Remove Temp Ban"),
                "url"      => $this->getUrl ( $page::PAGE_MANAGE_UNBAN ),
                "selected" => false,
            ));
            $this->getMassactionBlock ()->addItem ( "reset", array (
                "label"    => Mage::helper ("twofactor")->__("Re-Enroll User"),
                "url"      => $this->getUrl ( $page::PAGE_MANAGE_RESET ),
                "selected" => false,
            ));
            //
            return $this;
        }

    }