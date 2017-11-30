<?php

	/**
	 * ResetController.php - 
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_ResetController extends Mage_Adminhtml_Controller_Action {

		protected function _isAllowed () {
			// Is user allowed to reset 2FA for admin accounts?
			$session = Mage::getSingleton ("admin/session");
			return $session->isAllowed ("jetrails/twofactor/reset"); 
		}

		public function indexAction () {
			$this->loadLayout ();
			$this->_title ( $this->__("JetRails / Two-Factor Authentication / Reset 2FA") );
			$this->_setActiveMenu ("jetrails/twofactor");
			$this->_addContent (
				$this->getLayout ()->createBlock ("twofactor/adminhtml_reset_container")
			);
			$this->renderLayout ();
		}
 
    	public function gridAction () {
			$this->loadLayout ();
			$this->getResponse ()->setBody ( $this
				->getLayout ()
		    	->createBlock ("twofactor/adminhtml_reset_container_grid")
		    	->toHtml ()
			);
		}

		public function userAction () {
			// echo "RESETTING USER WITH ID: " . $this->getRequest ()->getParam ("id"); die;

			$this->_redirect ("twofactor/enforce/index");
		}

	}