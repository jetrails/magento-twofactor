<?php

	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Reset_State extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		public function render ( Varien_Object $row ) {
			// return $row->getUserId ();
			// $collection = Mage::getResourceModel ("twofactor/auth_collection")
			// 	->addAttributeToSelect ( array ( "state" ) )
   //              ->addAttributeToFilter ( "id", array ( "eq" => $row->getUserId () ) );

			$auth = Mage::getModel ("twofactor/auth");
			$auth->setId ( $row->getUserId () );
			return $auth->getState ();
		}

	}