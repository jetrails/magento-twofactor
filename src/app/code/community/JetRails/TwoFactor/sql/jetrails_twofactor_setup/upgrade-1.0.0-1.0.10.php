<?php

	// Start the setup process
	$this->startSetup ();
	// Get the table name
	$tableName = $this->getTable ("twofactor/auth");
	// Add another column to the existing table
	$this->getConnection ()
	->addColumn ( $tableName, "status", Varien_Db_Ddl_Table::TYPE_SMALLINT, 1, array (
		"nullable" => false,
		"unsigned" => true,
		"default" => 0,
	), "2FA Status" );
	// Finish the setup process
	$this->endSetup ();