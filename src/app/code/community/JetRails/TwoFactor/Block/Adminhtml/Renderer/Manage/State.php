<?php

	/**
	 * State.php - This class is a renderer class that is used with the grid widget's columns.
	 * These classes are used to load information from the authentication model using the admin
	 * user's id.
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Manage
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Manage_State extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		/**
		 * This method is used with the widget grid to load the details from the authentication
		 * model as it pertains with the admin user model. It uses the admin user id to load the
		 * authentication model and extract the necessary information.
		 * @param       Varien_Object       row                 The target row information
		 * @return      string                                  The authentication state badge
		 */
		public function render ( Varien_Object $row ) {
			// Load all the necessary models
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			$state = Mage::getModel ("twofactor/state");
			$status = Mage::getModel ("twofactor/status");
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $row->getUserId () );
			$auth->setId ( $row->getUserId () );
			// Expire ban if one is set
			$auth->isBanned ();
			// Get user state and set to hidden if status is disabled
			$userState = $auth->getState ();
			$userState = $status->isEnabled ( $auth->getStatus () ) ? $userState : $state::HIDDEN;
			// Return a formated string with the severity HTML
			return sprintf (
				"<span class='grid-severity-%s' ><span>%s</span></span>",
				$state->getSeverity ( $userState ),
				$state->getString ( $userState )
			);
		}

	}