## Short Description:

The JetRails 2FA plugin adds an extra layer of security to your Magento store.  User-based 2FA enablement ensures that admin users are following best security practices.

## Long Description:

Your Magento storefront is vulnerable. Eliminate your security risk by downloading the JetRails Two-Factor Authentication module. Two-factor authentication, also known as 2FA, is a critical component for Magento security and is used widely by Magento backend admin users. Authentication is a security process to verify a user's identity. Authentication consists of three factors: something they know (ie. password), something they have (ie. phone), or something they are (ie. fingerprint).

With a stock Magento installation, a user is only given one method of authentication. This method of authentication is "Something they know" usually consisting of their admin user's name and password. While having one method of authentication is typically secure, it has its limitations. By adding one additional layer of authentication, security is significantly strengthened. Having multiple methods of authentication is known as multi-factor authentication. It is often recommended that you choose at least two out of the three methods of authentication to ensure strong security.

This plugin works with "Something they know" and "Something they have". A Magento admin user that has the JetRails 2FA plugin enabled will not only be authenticated with "Something they know" which would be their admin username and password, but they will also authenticate with "Something they have" such as their phone or tablet.

Once the JetRails 2FA plugin is installed for your Magento store and an admin successfully logs into their account, the JetRails 2FA plugin will prompt the user to set up their 2FA account. The typical user enrollment process takes up to five minutes including installation of the Google Authenticator application on their device. For more information on using the JetRails 2FA plugin, make sure to read the user guide which offers visual step-by-step instructions.

2FA is an industry best practice and is implemented using the Time-Based One-Time Password (TOTP) algorithm. In developing this plugin, RFC-6238 was used for reference and it can be found <a href="https://tools.ietf.org/html/rfc6238" >here</a>. Since 2FA gives an extra layer of protection to Magento’s authentication process, it is vital to every Magento installation.

This plugin comes with the following features and benefits:

- A Master Administrator can require 2FA to be utilized by specific users.
- Usage for 2FA can be enforced and required for log-in.
- Once you use the 2FA to login, there is an option to bypass authentication for a pre-configured number of days.
- A Master Administrator can overlook every user's authentication process.
- In case of lost or misplaced 2FA account, backup codes are available as an alternate method for authentication.
- In case of an attempted account breach, prevention protocols are in place via Brute-force protection, which will temporarily block the account.
- The threshold for the number of failed authentication attempts before a ban is configurable as well as the duration the user is temporarily banned for.
- An automatic instantaneous alert will be sent to the account owner and store admins informing them of an attempted breach. Any security warning will be logged with any relevant data such as the offender's IP address.
- The 2FA account can be setup for devices (something they have) using the Google Authenticator app, which is available for every platform including <a href="https://itunes.apple.com/us/app/google-authenticator/id388497605" >iPhone</a> and <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" >Android</a>.

## Release Notes:

- Changed from role based to user based 2FA enforcement
- Made remember me duration configurable
- Made failed attempts configurable
- Made ban duration configurable
- Added a manage 2FA accounts page for super admins