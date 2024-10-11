<?php

	/**
	 * Cookie.php - This helper class contains functions that deal with cookie creation, deletion,
	 * and authentication.  This cookie class is used to aid in the "remember for x days"
	 * functionality.
	 * @version         1.1.5
	 * @package         JetRails® TwoFactor
	 * @category        Helper
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Helper_Cookie extends Mage_Core_Helper_Abstract {

		/**
		 * This function generates a unique user hash using the SHA512 algorithm and uses various
		 * database variables to salt user information.  This is used as the cookie name.
		 * @return      string                                      Unique hash for cookie name
		 */
		private function _createUserHash () {
			// Get the user id using the session
			$uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			// Extract the information using the admin user model
			$info = Mage::getModel ("admin/user")
				->getCollection ()
				->addFieldToSelect ( array ( "user_id", "email", "created" ) )
				->addFieldToFilter ( "user_id", array ( "eq" => $uid ) )
				->getData () [ 0 ];
			// Initialize the salt and data
			$salt = $info ["created"];
			$data = hash ( "sha512", $info ["email"] . $info ["user_id"] );
			// Return the unique hash encrypted
			return hash ( "sha512", hash ( "sha512", $salt . ":" . $data ) . ":" . $salt );
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
		 * for the cookie is set to x days.  We store the IP address, current time, and pin for
		 * current time.  These values are used to validate that we made this cookie and cannot be
		 * forged.  We also check that the IP address is the same and the expiration time is valid
		 * so the cookie cannot be tampered with.
		 * @param       integer             time                Exact time for authentication
		 * @param       integer             pin                 Current verification pin
		 * @param       integer             address             The IP address of the client
		 * @return      void
		 */
		public function create ( $time, $pin, $address ) {
			// Load the data helper instance
			$data = Mage::helper ("twofactor/data");
			// Create the content for the cookie
			$value = Mage::getSingleton ("core/encryption")->encrypt ( json_encode ( array (
				"timestamp" => $time,
				"pin" => $pin,
				"address" => $address
			)));
			// Create cookie hash for identity
			Mage::getSingleton ("core/cookie")->set (
				$this->_createUserHash (),
				$value,
				60 * 60 * 24 * intval ( $data->getData () ["remember_me"] )
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
		 * parse it.  If the IP address is not the same, the expiration time exceeds x days, or if
		 * the time/pin combo does not match with the user's secret, then the cookie is deleted and
		 * the user is forced to user the 2FA page.  Otherwise the 2FA process is handled for them.
		 * @param       integer             uid                     User id to authenticate with
		 * @return      bool                                        Does a valid live cookie exist?
		 */
		public function authenticate ( $uid ) {
			// Load the cookie and load helper class
			$data = Mage::helper ("twofactor/data");
			$cookie = $this->_load ();
			// Check to see if that the cookie exists
			if ( $cookie !== false ) {
				// Decrypt the contents of cookie
				$cached = json_decode ( Mage::getSingleton ("core/encryption")->decrypt ( $cookie ) );
				// Check to see that the IP address still matches
				if ( Mage::helper ("core/http")->getRemoteAddr () === $cached->address ) {
					// Calculate the timestamp for x days after creation
					$current = new Zend_Date ();
					$expires = new Zend_Date ( $cached->timestamp );
					$expires->addDay ( intval ( $data->getData () ["remember_me"] ) );
					// See if the cookie lived for more than needed
					if ( $current->compare ( $expires ) <= 0 ) {
						// Initialize authentication model and TOTP helper class
						$admin = Mage::getSingleton ("admin/session")->getUser ();
						$auth = Mage::getModel ("twofactor/auth");
						$auth->load ( $admin->getUserId () );
						$auth->setId ( $admin->getUserId () );
						$totp = Mage::helper ("twofactor/totp");
						// Initialize TOTP instance and parse cached timestamp
						$totp->initialize ( $auth->getSecret () );
						$timestamp = ( new Zend_Date ( $cached->timestamp ) )->getTimestamp ();
						// Check to see that the pin is valid given the secret
						if ( intval ( $totp->pin ( $timestamp ) ) === intval ( $cached->pin ) ) {
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
