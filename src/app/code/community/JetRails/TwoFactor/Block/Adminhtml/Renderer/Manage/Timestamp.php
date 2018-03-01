<?php

	/**
	 * Timestamp.php - This class is a renderer class that is used with the grid widget's columns.
	 * These classes are used to load information from the authentication model using the admin
	 * user's id.
	 * @version         1.1.1
	 * @package         JetRails® TwoFactor
	 * @category        Manage
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Manage_Timestamp extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		/**
		 * This method is used with the widget grid to load the details from the authentication
		 * model as it pertains with the admin user model. It uses the admin user id to load the
		 * authentication model and extract the necessary information.
		 * @param       Varien_Object       row                 The target row information
		 * @return      string                                  The last authentication timestamp
		 */
		public function render ( Varien_Object $row ) {
			// Get the admin user object
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			// Load the authentication entry
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $row->getUserId () );
			$auth->setId ( $row->getUserId () );
			// Load the timestamp and format it
			$timestamp = $auth->getLastTimestamp ();
			return $timestamp === null ? "-" : Mage::getModel ("core/date")->date (
				"m/d/Y h:i:s A",
				strtotime ( $timestamp )
			);
		}

	}