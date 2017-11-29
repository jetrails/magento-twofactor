<?php

	class JetRails_TwoFactor_Block_Adminhtml_Renderer_Enforce_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

		public function render ( Varien_Object $row ) {
			$url = $this->getUrl ( "twofactor/enforce/role", array ( "id" => $row->getRoleId () ) );
			return sprintf (
				"<select onchange='setLocation ( \"%s/type/\" + this.value )' >" .
					"<option value='0' >Enforce 2FA</option>" . 
					"<option value='1' selected >Allow 2FA Option</option>" .
					"<option value='2' >Deny 2FA Option</option>" .
				"</select>",
				rtrim ( $url, "/" ),
				Mage::helper ("twofactor")->__("Reset")
			);
		}

	}