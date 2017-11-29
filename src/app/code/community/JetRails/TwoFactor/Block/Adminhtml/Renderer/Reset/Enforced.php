<?php

	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Reset_Enforced extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		public function render ( Varien_Object $row ) {
			return $row->getUserId ();
		}

	}