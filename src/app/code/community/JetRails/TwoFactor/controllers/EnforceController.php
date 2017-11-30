<?php

	/**
	 * EnforceController.php - 
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_EnforceController extends Mage_Adminhtml_Controller_Action {

		protected function _isAllowed () {
			// Is user allowed to enforce 2FA on roles?
			$session = Mage::getSingleton ("admin/session");
			return $session->isAllowed ("jetrails/twofactor/enforce"); 
		}

		public function indexAction () {
			$this->loadLayout ();
			$this->_title ( $this->__("JetRails / Two-Factor Authentication / Enforce 2FA") );
			$this->_setActiveMenu ("jetrails/twofactor");
			$this->_addContent (
				$this->getLayout ()->createBlock ("twofactor/adminhtml_enforce_container")
			);
			$this->renderLayout ();
		}
 
    	public function gridAction () {
			$this->loadLayout ();
			$this->getResponse ()->setBody ( $this
				->getLayout ()
		    	->createBlock ("twofactor/adminhtml_enforce_container_grid")
		    	->toHtml ()
			);
		}

		public function roleAction () {
			// echo "SETTING ROLE WITH ID : " . $this->getRequest ()->getParam ("id") . " AND TYPE " . $this->getRequest ()->getParam ("type");; die;

			$this->_redirect ("twofactor/enforce/index");
		}

	}