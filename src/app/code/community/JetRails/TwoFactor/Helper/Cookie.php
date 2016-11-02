<?php

	/**
	 * Cookie.php - This helper class contains functions that deal with cookie creation, deletion,
	 * and authentication.  This cookie class is used to aid in the "remember for 7 days"
	 * functionality.
	 * @version         1.0.3
	 * @package         JetRails® TwoFactor
	 * @category        Helper
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Helper_Cookie extends Mage_Core_Helper_Abstract {

		/**
		 * This function generates a unique user hash using the MD5 algorithm and uses various
		 * database variables to salt user information.  This is used as the cookie name.
		 * @return      string                                      Unique hash for cookie name
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
		 * This function simply loads the cookie using the Magento core cookie model.  It returns
		 * the cookie object, if it is not set then false will be returned.
		 * @return      Mixed                                       False if unset, String if set
		 */
		private function _load () {
			// Load the cookie
			$cookie = Mage::getModel ("core/cookie")->get ( $this->_createUserHash () );
			// Return the cookie
			return $cookie;
		}

		/**
		 * This function creates a cookie based on the passed parameter.  These parameters are
		 * encoded to a JSON string, then encrypted with the store's encryption key.  The expiration
		 * for the cookie is set to 7 days.  We store the IP address, current time, and pin for
		 * current time.  These values are used to validate that we made this cookie and cannot be
		 * forged.  We also check that the IP address is the same and the expiration time is valid
		 * so the cookie cannot be tampered with.
		 * @return      void
		 */
		public function create ( $time, $pin, $address ) {
			// Create the content for the cookie
			$value = Mage::helper ("core")->encrypt ( json_encode ([
				"timestamp"     =>      $time,
				"pin"           =>      $pin,
				"address"       =>      $address
			]));
			// Create cookie hash for identity
			Mage::getSingleton ("core/cookie")->set (
				$this->_createUserHash (),
				$value,
				60 * 60 * 24 * 7
			);
		}

		/**
		 * This function simply creates a cookie with no value that is set to expire "now".  This
		 * effectively deletes the cookie.
		 * @return      void
		 */
		public function delete () {
			// Simply, set expire to now
			Mage::getModel ("core/cookie")->set ( $this->_createUserHash (), "", 0 );
		}

		/**
		 * This function is used to authenticate the login process for the user.  We see if a cookie
		 * is saved to determine whether or now to read it.  If one exists, then we decrypt it and
		 * parse it.  If the IP address is not the same, the expiration time exceeds 7 days, or if
		 * the time/pin combo does not match with the user's secret, then the cookie is deleted and
		 * the user is forced to user the TFA page.  Otherwise the TFA process is handled for them.
		 * @return      bool                                        Does a valid live cookie exist?
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