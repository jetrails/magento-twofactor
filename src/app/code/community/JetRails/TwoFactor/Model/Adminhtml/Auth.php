<?php

	/**
	 * Auth.php - This authentication model class is very important because it interfaces with the
	 * authentication resource model and is a mediator that encrypts and decrypts data.  It also has
	 * useful methods that determine if a ban on a user is expired, does the authentication on a
	 * passed TOTP pin, and registers failed authentication attempts.  In addition the constants
	 * that are defined within this class are used throughout the module and are accessed
	 * statically.
	 * @version         1.1.3
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Auth extends Mage_Core_Model_Abstract {

		/**
		 * This constructor simply links up this model with the resource model class.  The path that
		 * is passed to the init function is treated as a resource path and the resource model is
		 * loaded.
		 */
		protected function _construct () {
			// Initialize using twofactor authentication resource model
			$this->_init ("twofactor/auth");
		}

		/**
		 * This method is fired whenever a failed authorization attempt is made via the verification
		 * pin or backup code.  Incrementing the attempts is optional, if the behavior that is
		 * desired is to log only the last timestamp and IP address of client.  If incrementing is
		 * desired, and the attempts exceed the number of allowed failed authentication attempts,
		 * then the user's authentication state is set to banned.
		 * @param       boolean             increment           Increment attempt? Default is true
		 * @return      void
		 */
		public function registerAttempt ( $increment = true ) {
			$data = Mage::helper ("twofactor/data");
			$state = Mage::getModel ("twofactor/state");
			// Get the current timestamp and client IP address
			$timestamp = ( new Zend_Date () )->toString ("YYYY-MM-dd HH:mm:ss");
			$address = Mage::helper ("core/http")->getRemoteAddr ();
			// Set the last timestamp and IP address
			$this->setLastTimestamp ( $timestamp );
			$this->setLastAddress ( $address );
			// If we choose to increment the attempts
			if ( $increment === true ) {
				// Increment the number of authorization attempts
				$attempt = intval ( $this->getAttempts () ) + 1;
				$this->setAttempts ( $attempt );
				// If the number of attempts exceeds the maximum, then set state as banned
				if ( $attempt >= $data->getData () ["ban_attempts"] ) {
					$this->setState ( $state::BANNED );
				}
			}
			// Save changed data for the admin user
			$this->save ();
		}

		/**
		 * This method evaluates the currently loaded user and it simply returns the user's state.
		 * If the state is not defined then by default, the SCAN state is returned.
		 * @return      integer                                 State value for loaded user
		 */
		public function getState () {
			// Load the state model
			$state = Mage::getModel ("twofactor/state");
			// If the state is not set, return scan stage
			if ( parent::getState () === null ) {
				return $state::SCAN;
			}
			// Otherwise, return state as integer
			return intval ( parent::getState () );
		}

		/**
		 * This method evaluates the currently loaded user and it simply returns the user's status.
		 * If the status is not defined then by default, the ENABLED status is returned.
		 * @return      integer                                 Status value for loaded user
		 */
		public function getStatus () {
			$status = Mage::getModel ("twofactor/status");
			// If 2FA is not enabled for user then default is enabled
			if ( parent::getStatus () === null ) {
				return $status::ENABLED;
			}
			// Otherwise, return state as integer
			return intval ( parent::getStatus () );
		}

		/**
		 * This method gets the stored TOTP secret from the database and decrypts it before
		 * returning it to the caller.
		 * @return      string                                  Decrypted secret
		 */
		public function getSecret () {
			// Get the encrypted secret
			$secret = parent::getSecret ();
			// If the secret is not null
			if ( $secret !== null ) {
				// Decrypt and return the secret
				return Mage::getSingleton ("core/encryption")->decrypt ( $secret );
			}
			// Otherwise, return null
			return null;
		}

		/**
		 * This method takes in an un-encrypted TOTP secret and encrypts it before saving it into
		 * the database.
		 * @param       string              secret              Un-encrypted TOTP secret to save
		 * @return      void
		 */
		public function setSecret ( $secret ) {
			// Before saving to databases, encrypt the TOTP secret
			parent::setSecret ( Mage::getSingleton ("core/encryption")->encrypt ( $secret ) );
		}

		/**
		 * This method decrypts the backup codes that are stored in the database and returns them
		 * as a PHP array.  If the stored value is null, then an empty array is returned.
		 * @return      array                                   List of backup codes
		 */
		public function getBackupCodes () {
			// Get the encrypted backup codes
			$codes = parent::getBackupCodes ();
			// If the value is not null and defined
			if ( $codes !== null ) {
				// Decrypt the values and turn into a PHP array
				$codes = Mage::getSingleton ("core/encryption")->decrypt ( $codes );
				return json_decode ( $codes );
			}
			// Otherwise, return an empty array
			return array ();
		}

		/**
		 * This method takes in an array of strings which represent backup codes, it then saves them
		 * in JSON format before encrypting them.
		 * @param       array               codes               List of backup codes
		 * @return      void
		 */
		public function setBackupCodes ( $codes ) {
			// Encode as JSON, encrypt, store into database
			$codes = json_encode ( $codes );
			$codes = Mage::getSingleton ("core/encryption")->encrypt ( $codes );
			parent::setBackupCodes ( $codes );
		}

		/**
		 * This method takes in a proposed verification pin and then the model uses the TOTP helper
		 * to verify if the code is correct.  Essentially it authenticates the user using the passed
		 * verification pin.
		 * @param       integer             pin                 User defined verification pin
		 * @return      boolean                                 Is the TOTP pin correct?
		 */
		public function verify ( $pin ) {
			// Get TOTP helper
			$totp = Mage::helper ("twofactor/totp");
			// Get TOTP secret and initialize TOTP helper using secret
			$secret = $this->getSecret ();
			$totp->initialize ( $secret );
			// Return whether pin is correct
			return $totp->verify ( intval ( $pin ) );
		}

		/**
		 * This method determines if a user is banned even after they are in the banned state.
		 * Since a user can be unbanned based on the amount of time that went by after being
		 * banned, it is possible for the ban to expire.  This method handles this case.
		 * @return      boolean                                 Is user banned, or ban expired?
		 */
		public function isBanned () {
			// Load the state model and the data helper
			$data = Mage::helper ("twofactor/data");
			$state = Mage::getModel ("twofactor/state");
			// First things first, make sure the user is indeed banned
			if ( $this->getState () == $state::BANNED ) {
				// Get the current timestamp and calculate the expire timestamp
				$current = new Zend_Date ();
				$expires = new Zend_Date ( $this->getLastTimestamp (), Zend_Date::ISO_8601 );
				$expires->addMinute ( intval ( $data->getData () ["ban_time"] ) );
				// Check to see if the ban expired
				if ( $current->compare ( $expires ) > -1 ) {
					// Change the state to be not banned and reset attempts
					$this->setAttempts ( 0 );
					$this->setState ( $state::VERIFY );
					$this->save ();
					return false;
				}
				// By default return true (still banned)
				return true;
			}
			// Otherwise, the user is not banned
			return false;
		}

	}
