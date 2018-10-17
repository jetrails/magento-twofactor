<?php

	/**
	 * Backup.php - This template block is used in hand with the associated template file and it
	 * is used to prepare information for the template file.
	 * @version         1.1.3
	 * @package         JetRails® TwoFactor
	 * @category        Template
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Template_Backup extends Mage_Adminhtml_Block_Template {

		/**
		 * This method simply interfaces with the authentication model and returns an array of
		 * backup codes for the admin user that is currently logged in with the current session.
		 * @return      array                                   List of backup codes that are set
		 */
		public function getBackupCodes () {
			// Get authentication model and return backup codes
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $admin->getUserId () );
			$auth->setId ( $admin->getUserId () );
			return $auth->getBackupCodes ();
		}

		/**
		 * This method returns the correct action url for the form which is defined in the template.
		 * @return      string                                  Form action URL
		 */
		public function getFormUrl () {
			// Get the backup action url and return it
			$page = Mage::getSingleton ("twofactor/page");
			return Mage::getUrl ( $page::PAGE_SETUP_BACKUP );
		}

		/**
		 * This method gets a form key from the core session model.  This form key is used to submit
		 * a custom form that is defined in the template.
		 * @return      string                                  Valid form key
		 */
		public function getFormKey () {
			// Ask admin session for a form key and return it
			return Mage::getSingleton ("core/session")->getFormKey ();
		}

	}
