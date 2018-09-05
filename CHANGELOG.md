## Version 1.0.1
-   Updated global style file
-   Added auto focus to pin text fields
-   Added badges to form elements
-   Changed the description of the panel (step by step explanation)
-   Added a panel to show account email information
-   Added JetRails tab containing two-factor authentication option, this will show both forms

## Version 1.0.2
-   Auto generate new secret on each page load, until validated
-   Removed "secret" box
-   Removed "Generate New Secret" button
-   Removed "Generate New Secret" action from configuration controller
-   Made description a dynamically loaded block, depending on configuration status
-   Updated Two-Factor Authentication tab to dynamically load the description
-   Updated two-factor stylesheet

## Version 1.0.3
-   On 2FA enable, log user out

## Version 1.0.4
-   Removed 2FA tab on admin page because of redundancy.

## Version 1.0.5
-   SUPEE-6285 changed functionality of the \_isAllowed method in admin controllers.

## Version 1.0.6
-   Total overhaul in structure and 2FA process
-   Blocks users once too many attempts have been made
-   Once user is blocked, emails are sent to account owner and admins
-   Blocks expire after a set amount of minutes
-   2FA is setup on account login instead of config area
-   2FA is enforced through the ACL and binded to roles
-   Reset 2FA is possible through admin system config section
-   Backup codes are available on setup
-   Changed the look and feel of the skin (MaterializeCSS)
-   New logo for jetrails

## Version 1.0.7
-   On install, invalidate logged in sessions.

## Version 1.0.8
-   Only kept English translations
-   Changed/Added text translations throughout module
-   Enabled translations to work in Adminhtml area
-   Added MIT license

## Version 1.0.9
-   Changed hashing algorithm to use SHA512 (cookie)
-   Removed short array syntax (MEQP1)
-   Updated logo and color scheme
-   Fixed small bug when trying to change auth type after failing
-   Changed font to "Open Sans"
-   Changed layout on scan stage step-2
-   Fixed bug with verification pin/backup code having leading zero (intval)
-   Fixed "input is not focusable" bug on submit after error
-   Added User Guide
-   Removed "metadata" command from Grunt file
-   Added a compatibility list
-   Changed release to be 'tgz' instead of 'zip'
-   Fixed "PHP syntax error: Can't use method return value in write context"

## Version 1.1.0
-   Added docker to repo
-   Cleaned Grunt to necessary commands only
-   Changed from role based to user based 2FA enforcement
-   Made remember me duration configurable
-   Made failed attempts configurable
-   Made ban duration configurable
-   Added a manage 2FA accounts page for super admins
-   Improved email templates

## Version 1.1.1
-   Added enterprise compatibility

## Version 1.1.2
-   Refreshing config cache when saving 2fa settings
-   Fixed logout not working for custom admin frontend
-   TOTP class now being loaded properly on case-sensitive systems
-   Moved all controller actions to live under admin route
