<?php

	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Manage_State extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		public function render ( Varien_Object $row ) {

			$admin = Mage::getSingleton ("admin/session")->getUser ();
			$state = Mage::getModel ("twofactor/state");
			$status = Mage::getModel ("twofactor/status");
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $row->getUserId () );
			$auth->setId ( $row->getUserId () );

			// Expire ban if one is set
			$auth->isBanned ();
			
			$userState = $auth->getState ();
			$userState = $status->isEnabled ( $auth->getStatus () ) ? $userState : $state::HIDDEN;

			return sprintf (
				"<span class='grid-severity-%s' ><span>%s</span></span>",
				$state->getSeverity ( $userState ),
				$state->getString ( $userState )
			);
		}

	}