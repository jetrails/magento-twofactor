<?php

	class JetRails_TwoFactor_Block_Adminhtml_Configure_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

		protected function _prepareForm () {

			$data = Mage::helper ("twofactor/data");
			$page = Mage::getModel ("twofactor/page");

			$form = new Varien_Data_Form ();

			$fieldset = $form->addFieldset (
				"general_configuration",
				array ( "legend" => Mage::helper ("twofactor")->__("General Configuration") )
			);

			$fieldset->addField ( "remember_me", "text", array (
				"name" 		=>	"remember_me",
				"label" 	=> 	Mage::helper ("twofactor")->__("Remember Me Duration"),
				"note"		=>	Mage::helper ("twofactor")->__("Number of <b>days</b> authentication is remembered"),
				"required" 	=> 	true,
			));

			$fieldset->addField ( "ban_attempts", "text", array (
				"name" 		=>	"ban_attempts",
				"label" 	=> 	Mage::helper ("twofactor")->__("Failed Authentication Threshold"),
				"note"		=>	Mage::helper ("twofactor")->__("Number of failed authentication attempts before a user is temporarily banned"),
				"required" 	=> 	true,
			));

			$fieldset->addField ( "ban_time", "text", array (
				"name" 		=>	"ban_time",
				"label" 	=> 	Mage::helper ("twofactor")->__("Temporary Ban Duration"),
				"note"		=>	Mage::helper ("twofactor")->__("Amount of time in <b>minutes</b> that a user will be banned"),
				"required" 	=> 	true,
			));

			$form->setValues ( $data->getData () );

			$form->setId ("edit_form");
			$form->setMethod ("post");
			$form->setUseContainer ( true );
			$form->setAction ( $this->getUrl ( $page::PAGE_CONFIGURE_SAVE ) );

			$this->setForm ( $form );
			return parent::_prepareForm ();
		}

}