<?php

	class JetRails_TwoFactor_Helper_Data extends Mage_Core_Helper_Abstract {

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
			// Return the result
			return json_decode ( $results [ 0 ] ["config"] );
		}

		private function _setTwoFactor ( $uid, $value ) {
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

		public function isEnabled ( $uid ) {
			// Get the configuration
			$config = $this->_getTwoFactor ( $uid );
			// Return if TFA is enabled
			return $config->enabled;
		}

		public function getSecret ( $uid ) {
			// Get the configuration
			$config = $this->_getTwoFactor ( $uid );
			// Return the stored secret
			return $config->secret;
		}

		public function setEnabled ( $uid, $value ) {
			// Get the configuration
			$config = $this->_getTwoFactor ( $uid );
			// Change the enabled state
			$config->enabled = $value;
			// Save to database
			$this->_setTwoFactor ( $uid, json_encode ( $config ) );
		}

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