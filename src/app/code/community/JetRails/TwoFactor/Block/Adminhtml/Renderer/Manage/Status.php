<?php

	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Manage_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		public function render ( Varien_Object $row ) {
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			$status = Mage::getModel ("twofactor/status");
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $row->getUserId () );
			$auth->setId ( $row->getUserId () );

			return sprintf (
				"<span class='grid-severity-%s' ><span>%s</span></span>",
				$status->getSeverity ( $auth->getStatus () ),
				$status->getString ( $auth->getStatus () )
			);
		}

	}