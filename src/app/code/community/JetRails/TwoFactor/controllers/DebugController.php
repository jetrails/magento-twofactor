<?php

	class JetRails_TwoFactor_DebugController extends Mage_Adminhtml_Controller_Action {

		public function indexAction () {
			$notify = Mage::getModel ("twofactor/notify");
			$notify->emailUser ();
			$notify->emailAllAdministrators ();
		}

	}