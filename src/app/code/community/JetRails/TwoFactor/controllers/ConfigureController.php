<?php

    /**
     * ConfigureController.php - 
     * @version         1.0.10
     * @package         JetRails® TwoFactor
     * @category        Controllers
     * @author          Rafael Grigorian - JetRails®
     * @copyright       JetRails®, all rights reserved
     */
    class JetRails_TwoFactor_ConfigureController extends Mage_Adminhtml_Controller_Action {

        /**
         * 
         */
        protected function _isAllowed () {
            // Is user allowed to manage 2FA accounts?
            $session = Mage::getSingleton ("admin/session");
            return $session->isAllowed ("jetrails/twofactor/configure");
        }

        /**
         * 
         */
        protected function _validate ( $values ) {
            // Loop through each value
            foreach ( $values as $value ) {
                $valid = 
                    strval ( intval ( $id ) ) == strval ( $id ) &&
                    is_int ( intval ( $id ) ) &&
                    intval ( $id ) > 0;
                if ( !$valid ) return false;
            }
            // By default return true
            return true;
        }

        /**
         * 
         */
        public function indexAction () {
            // Set the title for the page
            $this->_title ( $this->__("JetRails") );
            $this->_title ( $this->__("Two-Factor Authentication") );
            $this->_title ( $this->__("Configure 2FA Settings") );
            // Load layout, add the content, set active tab, and render layout
            $this->loadLayout ();
            $this->_setActiveMenu ("jetrails/twofactor");
            $this->_addContent ( $this->getLayout ()->createBlock ("twofactor/configure_edit") );
            $this->renderLayout ();
        }

        /**
         * 
         */
        public function saveAction () {
            // Get an instance of the data helper
            $data = Mage::helper ("twofactor/data");
            $page = Mage::getModel ("twofactor/page");
            // Get configuration variables from request
            $rememberMe = $this->getRequest ()->getParam ( "remember_me", null );
            $banAttempts = $this->getRequest ()->getParam ( "ban_attempts", null );
            $banTime = $this->getRequest ()->getParam ( "ban_time", null );
            // Check to see that passed values are valid and in range
            if ( $this->_validate ( array ( $rememberMe, $banAttempts, $banTime ) ) ) {
                // Save values into the configuration table
                Mage::getConfig ()->saveConfig (
                    $data::XPATH_REMEMBER_ME, $rememberMe, $data::XPATH_SCOPE, 0
                );
                Mage::getConfig ()->saveConfig (
                    $data::XPATH_BAN_ATTEMPTS, $banAttempts, $data::XPATH_SCOPE, 0
                );
                Mage::getConfig ()->saveConfig (
                    $data::XPATH_BAN_TIME, $banTime, $data::XPATH_SCOPE, 0
                );
                // Attach a success message to the session
                Mage::getSingleton ("admin/session")->addSuccess (
                    Mage::helper ("twofactor")->__("Successfully saved settings")
                );
            }
            else {
                // Attach an error message to the session
                Mage::getSingleton ("admin/session")->addError (
                    Mage::helper ("twofactor")->__("Values must be integer values greater than 0")
                );
            }
            // Redirect back to the configure page
            $this->getResponse ()->setRedirect ( $this->getUrl ( $page::PAGE_CONFIGURE_INDEX ) );
        }

    }