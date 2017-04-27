<?php

	/**
	 * Data.php - This helper sets and gets values into the twofactor column in the admin_user table
	 * in the Magento store's database.  We need this helper class in order to ensure that these
	 * values get encrypted and decrypted.
	 * @version         1.0.5
	 * @package         JetRails® TwoFactor
	 * @category        Helper
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Helper_Data extends Mage_Core_Helper_Abstract {

		/**
		 * This function returns the JSON string stored in the database under the admin_user table.
		 * This JSON string contains the enabled status as well as the user's TOTP secret.
		 * @param       int                     uid                 The user id linked to session
		 * @return      Object                                      JSON object with TOTP config
		 */
		private function _getTwoFactor ( $uid ) {
			// Get the database resource from magento
			$resource = Mage::getSingleton ("core/resource");
			// Get the read connection
			$connection = $resource->getConnection ("core_read");
			// Get the table name for admin users
			$table = $resource->getTableName ("admin/user");
			// Construct the SQL query
			$sql = "SELECT twofactor AS config
					FROM $table
					WHERE user_id='$uid'
					LIMIT 1";
			// Execute query and save results
			$results = $connection->fetchAll ( $sql );
			// Return the result, decrypted and decoded
			return json_decode ( Mage::helper ("core")->decrypt ( $results [ 0 ] ["config"] ) );
		}

		/**
		 * This function take in the user id and the json string value and it encrypts it before
		 * storing it back into the twofactor column in the admin_user table.
		 * @param       int                     uid                 The user id linked to session
		 * @param       string                  value               The TOTP config as JSON string
		 * @return      void
		 */
		private function _setTwoFactor ( $uid, $value ) {
			// Encrypt the value
			$value = Mage::helper ("core")->encrypt ( $value );
			// Get the database resource from magento
			$resource = Mage::getSingleton ("core/resource");
			// Get the write connection
			$connection = $resource->getConnection ("core_write");
			// Get the table name for admin users
			$table = $resource->getTableName ("admin/user");
			// Construct the SQL query
			$sql = "UPDATE $table
					SET twofactor = '$value'
					WHERE user_id = '$uid'
					LIMIT 1";
			// Execute the query
			$connection->query ( $sql );
		}

		/**
		 * This function simply gets the TOTP configuration and evaluated if the TFA service is
		 * enabled.
		 * @param       int                     uid                 The user id linked to session
		 * @return      bool                                        Is TFA service enabled for user?
		 */
		public function isEnabled ( $uid ) {
			// Get the configuration
			$config = $this->_getTwoFactor ( $uid );
			// Return if TFA is enabled
			return $config->enabled;
		}

		/**
		 * This function simply gets the TOTP configuration and returns the user's secret.
		 * @return      string                                      TOTP secret for user
		 */
		public function getSecret ( $uid ) {
			// Get the configuration
			$config = $this->_getTwoFactor ( $uid );
			// Return the stored secret
			return $config->secret;
		}

		/**
		 * This function takes in a user id and a value for whether the TFA service is enabled for
		 * said user, then stores the configuration back into the database.
		 * @param       int                     uid                 The user id linked to session
		 * @param       bool                    value               Set TFA service enabled?
		 * @return      void
		 */
		public function setEnabled ( $uid, $value ) {
			// Get the configuration
			$config = $this->_getTwoFactor ( $uid );
			// Change the enabled state
			$config->enabled = $value;
			// Save to database
			$this->_setTwoFactor ( $uid, json_encode ( $config ) );
		}

		/**
		 * This function takes in a user id and a string value for the TOTP secret.  It then saves
		 * it back into the database for said user.
		 * @param       int                     uid                 The user id linked to session
		 * @param       string                  value               New TOTP secret
		 * @return      void
		 */
		public function setSecret ( $uid, $value ) {
			// Get the configuration
			$config = $this->_getTwoFactor ( $uid );
			// Change the secret
			$config->secret = $value;
			// Save to database
			$this->_setTwoFactor ( $uid, json_encode ( $config ) );
		}

	}

?>