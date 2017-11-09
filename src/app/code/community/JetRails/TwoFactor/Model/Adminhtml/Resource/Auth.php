<?php

	/**
	 * Auth.php - This class is the resource model for the authentication model.  There is not much
	 * going on here other than the standard setup.  The database table and primary column is
	 * defined in the constructor and auto incrementing primary key on data save is disabled.
	 * @version         1.0.7
	 * @package         JetRails® TwoFactor
	 * @category        Resource Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Resource_Auth extends Mage_Core_Model_Resource_Db_Abstract {

		/**
		 * This internally saved data member is overridden from the parent class.  This data member
		 * is changed to change the behavior of saving data into the database.  In the case of this
		 * module, we want the primary key (admin user id) to be defined and saved into the custom
		 * table.  For this reason we disable the auto-increment feature for this resource model.
		 * We want the set user id to be saved and not be incremented.
		 * @var         boolean             _isPkAutoIncrement  Auto increment primary key on save
		 */
		protected $_isPkAutoIncrement = false;

		/**
		 * This constructor will initialize the resource model by telling it what database table to
		 * use and also define what the primary column will be
		 */
		protected function _construct () {
			// Define the table that will be used, and define the primary key
			$this->_init ( "twofactor/auth", "id" );
		}

	}