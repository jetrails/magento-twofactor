<?php

	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Reset_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		public function render ( Varien_Object $row ) {
			$url = $this->getUrl ( "twofactor/reset/user", array ( "id" => $row->getUserId () ) );
			return sprintf (
				"<a href='%s' ><button>%s</button></a>",
				$url,
				Mage::helper ("twofactor")->__("Reset")
			);
		}

	}