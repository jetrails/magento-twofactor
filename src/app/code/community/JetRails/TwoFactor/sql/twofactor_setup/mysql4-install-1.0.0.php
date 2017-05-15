<?php

	// Get the installer instance from self
	$installer = $this;
	// Start the setup process
	$installer->startSetup ();
	// Initialize the default JSON that will be displayed (encrypted)
	$json = Mage::helper ("core")->encrypt ( json_encode ( array (
		"secret"        =>      "",
		"enabled"       =>      false
	)));
	// Run the following command to set up the database
	$installer->run ("
		ALTER TABLE admin_user
		ADD COLUMN twofactor varchar (255) NOT NULL DEFAULT '$json';
	");
	// Finish the setup process
	$installer->endSetup ();