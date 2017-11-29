<?php

	/**
	 * TOTP.php - This helper class is used in order to generate and authenticate TOTP secrets and
	 * pins.  It is very flexible and can be initialized to different sized secrets and pins.
	 * Although the default values conform to the generally accepted configuration for 2FA.  Apps
	 * such as Authy and Google Authenticator use the default configuration.
	 * @version         1.0.9
	 * @package         JetRails® TwoFactor
	 * @category        Helper
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Helper_TOTP extends Mage_Core_Helper_Abstract {

		/**
		 * This is the base 32 alphabet.  It is used to generate a random secret; it is also used
		 * to decode a base 32 string number back into decimal.
		 * @var          string        alphabet           Base 32 number system alphabet
		 */
		private $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";

		/**
		 * The desired size that we want the random secret to be in base 32.  This size is
		 * statically used throughout the class.
		 * @var          int           secretSize         Desired length of the base 32 secret
		 */
		private $secretSize = 16;

		/**
		 * This data member specifies the length of the pin that is derived from the secret key.
		 * @var          int           pinSize            This is the desired size of the pin code
		 */
		private $pinSize = 6;

		/**
		 * This data value is specified in seconds, and specifies the EOL for the pin number.
		 * @var          int           interval           The interval at which the pin changes
		 */
		private $interval = 30;

		/**
		 * This data member specifies the secret code that was either passed to the constructor or
		 * computed on initialization.
		 * @var          string        secret             The secret key string in base 32
		 */
		private $secret;

		/**
		 * This function is used to initialize the options for this TOTP class.  If it is called
		 * with no arguments, then the default ones will be used.
		 * @param       string         secret             Initialize with this secret
		 * @param       int            secretSize         Initialize with this secret size
		 * @param       int            pinSize            Initialize with this pin size
		 * @return      void
		 */
		public function initialize ( $secret = null, $secretSize = null, $pinSize = null ) {
			// Overwrite the default parameters if they are passed
			$this->secretSize = ( $secretSize != null ? $secretSize : $this->secretSize );
			$this->pinSize = ( $pinSize != null ? $pinSize : $this->pinSize );
			// If a secret was passed, then use it, otherwise generate a new one
			$this->secret = ( $secret == null ? $this->secret ( $this->secretSize ) : $secret );
		}

		/**
		 * This function takes in a Base 32 encoded string and it decodes it to plain text.
		 * @param       string         encoded            Base 32 encoded string
		 * @return      string                            Base 32 decoded result string
		 */
		private function decode ( $encoded ) {
			// Initialize the chunks string
			$chunks = "";
			// Split the base 32 numeric string into digits
			foreach ( str_split ( $encoded ) as $digit ) {
				// For each one, see if it is in the alphabet and get the translation index
				if ( false === ( $translation = strpos ( $this->alphabet, $digit ) ) ) {
					// If it isn't in the alphabet, use index zero
					$translation = 0;
				}
				// Append the translated index in a 5 bit binary format
				$chunks .= sprintf ( '%05b', $translation );
			}
			// Convert each chunk into decimal chunks
			$decimalChunks = array_map ( "bindec", str_split ( $chunks, 8 ) );
			// Convert each chunk into characters
			array_unshift ( $decimalChunks, "C*" );
			// Finally pack the result and pad with zeros at the end
			return rtrim ( call_user_func_array ( 'pack', $decimalChunks ), "\0" );
		}

		/**
		 * This function generates a random secret.  This secret uses the Base 32 alphabet.
		 * @param       integer        length             Length of desired secret
		 * @return      string                            Random secret is returned
		 */
		private function secret ( $length = 16 ) {
			// Initialize an empty string
			$secret = "";
			// Traverse $length times, to get random base32 encoding
			for ( $i = 0; $i < $length; $i++ ) {
				// Append a random number from base32 alphabet
				$secret .= $this->alphabet [ mt_rand ( 0, strlen ( $this->alphabet ) - 1 ) ];
			}
			// Return the resulting secret
			return $secret;
		}

		/**
		 * This function takes in a time for which to generate a pin for.  This time is based off
		 * UNIX EPOCH 0.  If nothing is passed then it uses the current time.
		 * @param       int            time               The time to generate pin for, default now
		 * @return      string                            Resulting 6-digit pin, in string form
		 */
		public function pin ( $time = null ) {
			// Get the time since EPOCH in UNIX time, then divide by the interval
			$time = floor ( ( $time == null ? time () : $time ) / $this->interval );
			// Decode the key from the secret
			$key = $this->decode ( $this->secret );
			// Pack the time into a 4 byte row of bits
			$time = "\0\0\0\0".pack ( "N*", $time );
			// Hash the time with the decoded key
			$hash = hash_hmac ( "SHA1", $time, $key, true );
			// extract the offset from the hash
			$offset = ord ( substr ( $hash, -1 ) ) & 0x0F;
			// Grab 4 bytes of the result
			$target = substr ( $hash, $offset, 4 );
			// Unpack binary value as unsigned long
			$value = unpack ( "N", $target );
			$value = $value [ 1 ];
			// Mask the value to only get 32 bits
			$value = $value & 0x7FFFFFFF;
			// Calculate the modulus from the decimal system and pin length
			$modulo = pow ( 10, $this->pinSize );
			// Return the pin, padded
			return str_pad ( $value % $modulo, $this->pinSize, '0', STR_PAD_LEFT );
		}

		/**
		 * This function simply takes in a pin as an argument and evaluates if the passed pin
		 * matches the current pin for the internally saved secret.
		 * @param       string         pin                The pin to evaluate
		 * @return      bool                              Is the pin valid?
		 */
		public function verify ( $pin ) {
			// Simply calculate the pin, and compare it to the passed in one
			return $this->pin () == $pin;
		}

		/**
		 * This function simply takes in information as parameters and generates a request to
		 * Google's chart's API to generate a QR code.  These QR codes are used for account setup
		 * for TOTP authentication.
		 * @param       string         email              The email to assign account to
		 * @param       string         issuer             The issuer of the account
		 * @param       string         secret             The secret for TOTP authentication
		 * @param       int            size               The size of the QR code
		 * @return      string                            URL to the QR code
		 */
		public function QRCode ( $email, $issuer, $secret, $size = 200 ) {
			// Create the standard TOTP URI using the secret, issuer, and user/host
			$uri  = "otpauth://totp/$email?secret=" . $secret . "&issuer=" . $issuer;
			// Create the Google QR code GET request
			$url  = "https://chart.googleapis.com/chart";
			$url .= "?cht=qr";
			$url .= "&chs=" . $size;
			$url .= "&chl=" . urlencode ( $uri );
			$url .= "&chld=H|0";
			// Return the URL to the QR barcode
			return $url;
		}

		/**
		 * This method takes in an integer and returns an array populated with randomly generated
		 * backup codes.  The amount that is passed matches the resulting array's size that is
		 * returned.
		 * @param       integer             amount              Number of codes (default is 10)
		 * @return      array                                   Generated random codes
		 */
		public function generateBackupCodes ( $amount = 10 ) {
			// Initialize an empty array
			$codes = array ();
			// Loop until the required amount is made
			while ( $amount > 0 ) {
				// Create a random code
				$code = "";
				for ( $i = 0; $i < 8; $i++ ) {
					$code .= rand ( 0, 9 );
				}
				// Push into result array, and decrement counter
				array_push ( $codes, $code );
				$amount--;
			}
			// Return resulting codes
			return $codes;
		}

		/**
		 * This function simply returns the internally saved TOTP secret to the caller.
		 * @return      string                            Internally saved secret
		 */
		public function getSecret () {
			// Simply return the saved secret
			return $this->secret;
		}

	}