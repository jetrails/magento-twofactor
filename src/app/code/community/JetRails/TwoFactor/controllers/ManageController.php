<?php

	/**
	 * ManageController.php -
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Controllers
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_ManageController extends Mage_Adminhtml_Controller_Action {

		protected function _isAllowed () {
			// Is user allowed to manage 2FA accounts?
			$session = Mage::getSingleton ("admin/session");
			return $session->isAllowed ("jetrails/twofactor/manage");
		}

		public function testAction () {
			echo "Hello"; die;
		}

		public function indexAction () {
			$this->loadLayout ();
			$this->_title ( $this->__("JetRails / Two-Factor Authentication / Manage 2FA Accounts") );
			$this->_setActiveMenu ("jetrails/twofactor");
			$this->_addContent (
				$this->getLayout ()->createBlock ("twofactor/adminhtml_manage_container")
			);
			$this->renderLayout ();
		}

		public function gridAction () {
			$this->loadLayout ();
			$this->getResponse ()->setBody ( $this
				->getLayout ()
				->createBlock ("twofactor/adminhtml_manage_container_grid")
				->toHtml ()
			);
		}

		public function enforceAction () {
			$userIds = $this->getRequest ()->getParam ("ids");
			if ( empty ( $userIds ) || !is_array ( $userIds ) ) {
				$this->_getSession ()->addError (
					Mage::helper ("twofactor")->__("Please select Indexes")
				);
			}
			else {
				foreach ( $userIds as $id ) {
					if ( $id != intval ( $id ) || $id == 0 ) continue;
					$auth = Mage::getModel ("twofactor/auth")
						->load ( intval ( $id ) )
						->setId ( intval ( $id ) );
					$auth
						->setEnforced ( $auth::ENFORCED_YES )
						->save ();
				}
				$this->_getSession ()->addSuccess (
					Mage::helper ("twofactor")->__("Successfully enforcing 2FA on selected accounts")
				);
			}
			$this->_redirect ("*/*/index");
		}

		public function unenforceAction () {
			$userIds = $this->getRequest ()->getParam ("ids");
			if ( empty ( $userIds ) || !is_array ( $userIds ) ) {
				$this->_getSession ()->addError (
					Mage::helper ("twofactor")->__("Please select Indexes")
				);
			}
			else {
				foreach ( $userIds as $id ) {
					if ( $id != intval ( $id ) || $id == 0 ) continue;
					$auth = Mage::getModel ("twofactor/auth")
						->load ( intval ( $id ) )
						->setId ( intval ( $id ) );
					$auth
						->setEnforced ( $auth::ENFORCED_NO )
						->save ();
				}
				$this->_getSession ()->addSuccess (
					Mage::helper ("twofactor")->__("Successfully un-enforced 2FA on selected accounts")
				);
			}
			$this->_redirect ("*/*/index");
		}

		public function unblockAction () {
			$userIds = $this->getRequest ()->getParam ("ids");
			if ( empty ( $userIds ) || !is_array ( $userIds ) ) {
				$this->_getSession ()->addError (
					Mage::helper ("twofactor")->__("Please select Indexes")
				);
			}
			else {
				foreach ( $userIds as $id ) {
					if ( $id != intval ( $id ) || $id == 0 ) continue;
					$auth = Mage::getModel ("twofactor/auth");
					$auth->load ( intval ( $id ) );
					$auth->setId ( intval ( $id ) );
					if ( $auth->isBlocked () ) {
						$auth->setState ( $auth::STATE_VERIFY );
						$auth->setAttempts ( 0 );
						$auth->save ();
					}
					else {
						$this->_getSession ()->addError (
							Mage::helper ("twofactor")->__("Account with user_id '$id' is not blocked")
						);
					}
				}
				if ( count ( $this->_getSession ()->getMessages ()->getErrors () ) == 0 ) {
					$this->_getSession ()->addSuccess (
						Mage::helper ("twofactor")->__("Successfully unblocked selected accounts")
					);
				}
			}
			$this->_redirect ("*/*/index");
		}

		public function resetAction () {
			$userIds = $this->getRequest ()->getParam ("ids");
			if ( empty ( $userIds ) || !is_array ( $userIds ) ) {
				$this->_getSession ()->addError (
					Mage::helper ("twofactor")->__("Please select Indexes")
				);
			}
			else {
				foreach ( $userIds as $id ) {
					if ( $id != intval ( $id ) || $id == 0 ) continue;
					$auth = Mage::getModel ("twofactor/auth");
					$auth->load ( intval ( $id ) );
					$auth->setId ( intval ( $id ) );
					$auth->setBackupCodes ( array () );
					$auth->setAttempts ( 0 );
					$auth->setSecret ("");
					$auth->setState ( $auth::STATE_SCAN );
					$auth->save ();
			
					// // Log user out and try to redirect to startup page
					// $session = Mage::getSingleton ("admin/session");
					// $url = $session->getUser ()->getStartupPageUrl ();
					// $admin = Mage::getSingleton ("admin/session");
					// $admin->unsetAll ();
					// $admin->getCookie ()->delete ( $admin->getSessionName () );
					// return $this->_redirect ( $url );

				}
				$this->_getSession ()->addSuccess (
					Mage::helper ("twofactor")->__("Successfully reset 2FA accounts for selected accounts")
				);
			}
			$this->_redirect ("*/*/index");
		}

	}