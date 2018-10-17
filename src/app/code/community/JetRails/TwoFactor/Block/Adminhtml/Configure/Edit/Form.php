<?php

	/**
	 * Form.php - This class initializes the form, field-set, and form fields that will exist on the
	 * configure page.  All information is set in the _prepareForm method.
	 * @version         1.1.3
	 * @package         JetRails® TwoFactor
	 * @category        Edit
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Configure_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

		/**
		 * This method setup up the whole form for the page.  It creates the form, the field-set and
		 * adds all the fields to the field-set.  It also defines the action for the form.
		 * @return      Mage_Adminhtml_Block_Widget_Form_Container      Returns instance of self
		 */
		protected function _prepareForm () {
			// Load the data and page models
			$data = Mage::helper ("twofactor/data");
			$page = Mage::getModel ("twofactor/page");
			// Make a new form element
			$form = new Varien_Data_Form ();
			// Add a field-set to the form
			$fieldset = $form->addFieldset (
				"general_configuration",
				array ( "legend" => Mage::helper ("twofactor")->__("General Configuration") )
			);
			// Add all the fields to the field-set
			$fieldset->addField ( "remember_me", "text", array (
				"name"      =>  "remember_me",
				"label"     =>  Mage::helper ("twofactor")->__("Remember Me Duration"),
				"note"      =>  Mage::helper ("twofactor")->__("Number of <b>days</b> authentication is remembered"),
				"required"  =>  true,
			));
			$fieldset->addField ( "ban_attempts", "text", array (
				"name"      =>  "ban_attempts",
				"label"     =>  Mage::helper ("twofactor")->__("Failed Authentication Threshold"),
				"note"      =>  Mage::helper ("twofactor")->__("Number of failed authentication attempts before a user is temporarily banned"),
				"required"  =>  true,
			));
			$fieldset->addField ( "ban_time", "text", array (
				"name"      =>  "ban_time",
				"label"     =>  Mage::helper ("twofactor")->__("Temporary Ban Duration"),
				"note"      =>  Mage::helper ("twofactor")->__("Amount of time in <b>minutes</b> that a user will be banned"),
				"required"  =>  true,
			));
			// Set the default values for the form
			$form->setValues ( $data->getData () );
			// Define form settings
			$form->setId ("edit_form");
			$form->setMethod ("post");
			$form->setUseContainer ( true );
			$form->setAction ( $this->getUrl ( $page::PAGE_CONFIGURE_SAVE ) );
			// Save the form internally and return the result of the inherited method
			$this->setForm ( $form );
			return parent::_prepareForm ();
		}

}
