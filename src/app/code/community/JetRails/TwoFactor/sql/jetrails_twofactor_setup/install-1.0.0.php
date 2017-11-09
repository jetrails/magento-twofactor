<?php

	// Start the setup process
	$this->startSetup ();
	// Get the table name
	$tableName = $this->getTable ("twofactor/auth");
	// Define the table structure
	$table = $this->getConnection ()->newTable ( $tableName )
	->addColumn ( "id", Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array (
		"auto_increment" => false,
		"unsigned" => true,
		"nullable" => false,
		"primary" => true,
	), "Admin user ID" )
	->addColumn ( "secret", Varien_Db_Ddl_Table::TYPE_VARCHAR, 24, array (
		"nullable" => true,
		"default" => null,
	), "TOTP Secret (encrypted)" )
	->addColumn ( "state", Varien_Db_Ddl_Table::TYPE_SMALLINT, 1, array (
		'unsigned' => true,
		'nullable' => false,
		'default' => 0,
	), "TOTP state" )
	->addColumn ( "attempts", Varien_Db_Ddl_Table::TYPE_SMALLINT, 2, array (
		'nullable' => false,
		'unsigned' => true,
		'default' => 0,
	), "Failed Attempts" )
	->addColumn ( "last_timestamp", Varien_Db_Ddl_Table::TYPE_TIMESTAMP, 2, array (
		'nullable' => false,
		'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
	), "Last timestamp for authentication attempt" )
	->addColumn ( "last_address", Varien_Db_Ddl_Table::TYPE_VARCHAR, 15, array (
		'nullable' => true,
		'default' => null,
	), "Last IP address for authentication attempt" )
	->addColumn ( "backup_codes", Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array (
		'nullable' => true,
		'default' => null,
	), "List of backup codes (encrypted)" )
	->setComment ("JetRails TwoFactor Table");
	// Create the table
	$this->getConnection ()->createTable ( $table );
	// Finish the setup process
	$this->endSetup ();