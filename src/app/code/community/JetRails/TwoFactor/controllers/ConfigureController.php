<?php

	/**
	 * ConfigureController.php - 
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_ConfigureController extends Mage_Adminhtml_Controller_Action {

		protected function _isAllowed () {
			// Is user allowed to enforce 2FA on roles?
			$session = Mage::getSingleton ("admin/session");
			return $session->isAllowed ("jetrails/twofactor/configure"); 
		}

		public function indexAction () {
			$this->loadLayout ();
			$this->_title ( $this->__("JetRails / Two-Factor Authentication / Configure 2FA") );
			$this->_setActiveMenu ("jetrails/twofactor");
			

			$form = new Varien_Data_Form ( array (
	            "id"      	=> 	"configure_form",
	            "method"  	=> 	"post",
	            "action"  	=> 	$this->getUrl ( "*/*/save", array ( "_current" => true ) ),
			));

			$form->setUseContainer ( true );

			$fieldset = $form->addFieldset ( "twofactor", array (
				"legend" 	=> 	Mage::helper ("adminhtml")->__("Two-Factor Authentication"),
			));

			$fieldset->addField ( "status", "text", array (
				"name"      			=> 	"status",
				"label"     			=> 	Mage::helper ("adminhtml")->__("Status"),
				"onclick"				=> 	"alert ('hello');",
				"disabled"  			=> 	true,
				"value"					=> 	Mage::helper ("adminhtml")->__("2FA is currently disabled")
			));


			$fieldset->addField ( "submit", "submit", array (
				"class"					=>	"submit",
				"label"     			=> 	Mage::helper ("adminhtml")->__("Save"),
				"onclick"				=> 	"alert ('hello');",
				"disabled"  			=> 	false,
				"type"					=> 	"submit",
				"value"					=> 	Mage::helper ("adminhtml")->__("2FA is currently disabled")
			));

			// var_dump ( get_class_methods ( $this->getLayout () ) ); die;

			echo $form->toHtml ();


			// $block = Mage::app ()->getLayout ()->createBlock ("adminhtml/system_account_edit_form");
			// $block->setForm ( $form );
			// $this->_addContent (
			// 	$form
			// 	// $this->getLayout ()->createBlock ("twofactor/adminhtml_enforce_container")
			// );

			// $this->renderLayout ();
		}

	}