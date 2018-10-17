<?php

	/**
	 * Observer.php - This observer rewrites the observer found in the Enterprise_Pci module. It is
	 * here in order to ensure 2FA authentication before forcing admin password change. The Pci
	 * module is only available for EE versions of Magento and therefore this observer will only
	 * run on enterprise versions of Magento.
	 * @version         1.1.3
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Rewrite_Observer extends Enterprise_Pci_Model_Observer {

		/**
		 * This method overrides the parent method. Before it executes the desired module behavior, it
		 * makes sure that we are authenticated. If we are not authenticated, then we do not run the
		 * forceAdminPasswordChange method from the parent.
		 * @param       Varien_Event_Observer   observer    Passed event observer
		 * @return      void
		 */
		public function forceAdminPasswordChange ( $observer ) {
			Mage::getSingleton ("twofactor/observer")->preAdminHtml ( $observer );
			$admin = Mage::getSingleton ("admin/session");
			if ( $admin->getTwoFactorAllow () === true ) {
				parent::forceAdminPasswordChange ( $observer );
			}
			return $observer;
		}

	}
