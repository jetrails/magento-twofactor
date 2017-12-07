<?php

	/**
	 * Auth.php - This authentication model class is very important because it interfaces with the
	 * authentication resource model and is a mediator that encrypts and decrypts data.  It also has
	 * useful methods that determine if a block on a user is expired, does the authentication on a
	 * passed TOTP pin, and registers failed authentication attempts.  In addition the constants
	 * that are defined within this class are used throughout the module and are accessed
	 * statically.
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Model
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Model_Adminhtml_Auth extends Mage_Core_Model_Abstract {

		/**
		 * These constants define the different states of authentication that the user can be in.
		 * They are used throughout the module and accessed statically.
		 */
		const STATE_SCAN = 0;
		const STATE_BACKUP = 1;
		const STATE_VERIFY = 2;
		const STATE_BLOCKED = 3;



		/**
		 *
		 *
		 *
		 *
		 *
		 * 
		 */
		const ENFORCED_NO = 0;
		const ENFORCED_YES = 1;



		/**
		 * These constants define the admin users preference whether to use 2FA or not.  Note that
		 * if the role that the user is in is forced to use 2FA, then the admins preference does not
		 * matter.
		 */
		const PREFERENCE_DISABLED = 0;
		const PREFERENCE_ENABLED = 1;

		/**
		 * These constants define some un-configurable options that are possible with this module.
		 * The max number of failed authentication attempts that are allowed before blocking a user
		 * is defined here, as well as the number of minutes the block lasts.
		 */
		const MAX_ATTEMPTS = 10;
		const BLOCK_TIME_MINUTES = 10;

		/**
		 * This constructor simply makes sure that it sets the user id for the model to be equal to
		 * the user id of the logged in admin user.  For this very reason, this model should not be
		 * used unless explicitly checking that the admin user is logged in.  Through the model,
		 * places like the observer class, these checks are made.
		 */
		protected function _construct () {
			// Initialize using twofactor authentication resource model
			$this->_init ("twofactor/auth");
			// // Get the logged in user's id and set it within this model (ready to be saved / loaded)
			// $uid = Mage::getSingleton ("admin/session")->getUser ()->getUserId ();
			// $this->load ( $uid );
			// $this->setId ( $uid );
		}

		/**
		 * This method is fired whenever a failed authorization attempt is made via the verification
		 * pin or backup code.  Incrementing the attempts is optional, if the behavior that is
		 * desired is to log only the last timestamp and IP address of client.  If incrementing is
		 * desired, and the attempts exceed the number of allowed failed authentication attempts,
		 * then the user's authentication state is set to blocked.
		 * @param       boolean             increment           Increment attempt? Default is true
		 * @return      void
		 */
		public function registerAttempt ( $increment = true ) {
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
				// If the number of attempts exceeds the maximum, then set state as blocked
				if ( $attempt >= self::MAX_ATTEMPTS ) {
					$this->setState ( self::STATE_BLOCKED );
				}
			}
			// Save changed data for the admin user
			$this->save ();
		}


		/**
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 * 
		 */
		public function getState () {
			// If the state is not set, return scan stage
			if ( parent::getState () === null ) {
				return self::STATE_SCAN;
			}
			// Otherwise, return state as integer
			return intval ( parent::getState () );
		}

		public function getEnforced () {
			// If the enforced flag is not set, return that user is not enforced
			if ( parent::getEnforced () === null ) {
				return self::ENFORCED_NO;
			}
			// Otherwise, return state as integer
			return intval ( parent::getEnforced () );
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
				return Mage::helper ("core")->decrypt ( $secret );
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
			parent::setSecret ( Mage::helper ("core")->encrypt ( $secret ) );
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
				$codes = Mage::helper ("core")->decrypt ( $codes );
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
			$codes = Mage::helper ("core")->encrypt ( $codes );
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
		 * This method determines if a user is blocked even after they are in the blocked state.
		 * Since a user can be unblocked based on the amount of time that went by after being
		 * blocked, it is possible for the block to expire.  This method handles this case.
		 * @return      boolean                                 Is user blocked, or block expired?
		 */
		public function isBlocked () {
			// First things first, make sure the user is indeed blocked
			if ( $this->getState () == self::STATE_BLOCKED ) {
				// Get the current timestamp and calculate the expire timestamp
				$current = new Zend_Date ();
				$expires = new Zend_Date ( $this->getLastTimestamp (), Zend_Date::ISO_8601 );
				$expires->addMinute ( self::BLOCK_TIME_MINUTES );
				// Check to see if the block expired
				if ( $current->compare ( $expires ) > -1 ) {
					// Change the state to be not blocked and reset attempts
					$this->setAttempts ( 0 );
					$this->setState ( self::STATE_VERIFY );
					$this->save ();
					return false;
				}
				// By default return true (still blocked)
				return true;
			}
			// Otherwise, the user is not blocked
			return false;
		}

	}