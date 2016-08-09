<?php

	/**
	 * Cookie.php - 
	 * @version         1.0.0
	 * @package         JetRails® TwoFactor
	 * @category        Helper
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Helper_Cookie extends Mage_Core_Helper_Abstract {

 		/**
		 * 
		 * @return
		 */
		private function _createUserHash () {
			// Get the user id using the session
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			// Extract the information using the admin user model
			$info = Mage::getModel ("admin/user")
	            ->getCollection ()
	            ->addFieldToSelect ( [ "user_id", "email", "created" ] )
	            ->addFieldToFilter ( "user_id", [ "eq" => $uid ] )
	            ->getData () [ 0 ];
	        // Initialize the salt and data
			$salt = $info ["created"];
			$data = md5 ( $info ["email"] . $info ["user_id"] );
			// Return the unique hash encrypted
			return md5 ( md5 ( $salt . ":" . $data ) . ":" . $salt );			
		}

 		/**
		 * 
		 * @return
		 */
		private function _load () {
			// Load the cookie
			$cookie = Mage::getModel ("core/cookie")->get ( $this->_createUserHash () );
			// Return the cookie
			return $cookie;
		}

 		/**
		 * 
		 * @return
		 */
		public function create ( $time, $pin, $address ) {
			// Create the content for the cookie
			$value = Mage::helper ("core")->encrypt ( json_encode ([
				"timestamp" 	=> 		$time,
				"pin"			=>		$pin,
				"address"		=>		$address
			]));
			// Create cookie hash for identity
			Mage::getSingleton ("core/cookie")->set (
				$this->_createUserHash (),
				$value,
				60 * 60 * 24 * 7
			);
		}

 		/**
		 * 
		 * @return
		 */
		public function delete () {
			// Simply, set expire to now
			Mage::getModel ("core/cookie")->set ( $this->_createUserHash (), "", 0 );
		}

 		/**
		 * 
		 * @return
		 */
		public function authenticate ( $uid ) {
			// Load the cookie
			$cookie = $this->_load ();
			// Check to see if that the cookie exists
			if ( $cookie !== false ) {
				// Decrypt the contents
				$value = json_decode ( Mage::helper ("core")->decrypt ( $cookie ) );
				// Check to see that the IP address still matches
				if ( Mage::helper ("core/http")->getRemoteAddr () === $value->address ) {
					// Calculate the timestamp for 7 days after creation
					$now = ( new DateTime () )->setTimestamp ( time () );
					$set = ( new DateTime () )->setTimestamp ( $value->timestamp );
					$set = $set->add ( new DateInterval ("P7D") );
					// See if the cookie lived for more than needed
					if ( $now <= $set ) {
						// Initialize the helper classes
						$Data = Mage::helper ("twofactor/Data");
						$TOTP = Mage::helper ("twofactor/TOTP");
						$TOTP->initialize ( $Data->getSecret ( $uid ) );
						// Check to see that the pin is valid given the secret
						if ( $TOTP->pin ( $value->timestamp ) == $value->pin ) {
							// Then, and only then, return true
							return true;
						}
					}
				}
				// If we fail any of these tests, then we must unset the cookie
				$this->delete (); 
			}
			// Return false by default
			return false;
		}

	}

?>