<?php

	// Get the installer instance from self
	$installer = $this;
	// Start the setup process
	$installer->startSetup ();
	// Initialize the default JSON that will be displayed
	$json = json_encode ([
		"secret" 		=> 		"",
		"enabled"		=>		false
	]);
	// Run the following command to set up the database
	$installer->run ("
		ALTER TABLE admin_user
		ADD COLUMN twofactor varchar (45) NOT NULL DEFAULT '$json';
	"); 
	// Finish the setup process
	$installer->endSetup ();

?>