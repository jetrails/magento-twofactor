<?php

	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Manage_Timestamp extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		public function render ( Varien_Object $row ) {
			// Get the admin user object
			$admin = Mage::getSingleton ("admin/session")->getUser ();
			// Load the authentication entry
			$auth = Mage::getModel ("twofactor/auth");
			$auth->load ( $row->getUserId () );
			$auth->setId ( $row->getUserId () );

			$timestamp = $auth->getLastTimestamp ();
			return $timestamp === null ? "-" : Mage::getModel ("core/date")->date ( "m/d/Y h:i:s A", strtotime ( $timestamp ) );
		}

	}