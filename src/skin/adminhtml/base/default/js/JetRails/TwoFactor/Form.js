/**
 * This file contains a function that runs whenever a pin is submitted, and it also attaches an
 * event listener to the form submit action.  This even runs the said function.
 */

function submitTwoFactorPin () {
	// Get the controller URL and the entered pin
	var base = document.getElementsByClassName ("jetrails_twofactor_submit") [ 0 ].name;
	var pin = document.getElementsByClassName ("jetrails_twofactor_pin") [ 0 ].value;
	// Load the controller by passing user input as GET request
	window.location = base + 'pin/' + pin;
}

$( document ).on ( "submit", "form", function ( event ) {
	// Prevent form from being submitted
	event.preventDefault ();
	// Check to see if we are in our custom pin text field
	if ( document.activeElement.className.split (" ").indexOf ("jetrails_twofactor_pin") != -1 ) {
		// If we are, then submit the two factor pin
		submitTwoFactorPin ();
	}
});