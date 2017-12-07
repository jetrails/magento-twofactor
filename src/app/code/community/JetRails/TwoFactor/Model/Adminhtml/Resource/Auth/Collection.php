<?php

	class JetRails_TwoFactor_Model_Adminhtml_Resource_Auth_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
		
		protected function _construct () {
			// Initialize by passing in model reference
			$this->_init ("twofactor/auth");
		}

	}