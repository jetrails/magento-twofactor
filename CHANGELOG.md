## Version 1.0.1
- 	Updated global style file
- 	Added auto focus to pin text fields
- 	Added badges to form elements
- 	Changed the description of the panel (step by step explanation)
- 	Added a panel to show account email information
- 	Added JetRails tab containing two factor authentication option, this will show both forms

## Version 1.0.2
- 	Auto generate new secret on each page load, until validated
- 	Removed "secret" box
- 	Removed "Generate New Secret" button
- 	Removed "Generate New Secret" action from configuration controller
- 	Made description a dynamically loaded block, depending on configuration status
- 	Updated Two Factor Authentication tab to dynamically load the description
- 	Updated two factor stylesheet

## Version 1.0.3
- 	On TFA enable, log user out

## Version 1.0.4
- 	Removed TFA tab on admin page because of redundancy.

## Version 1.0.5
- 	SUPEE-6285 changed functionality of the \_isAllowed method in admin controllers.

## Version 1.0.6
- 	Total overhaul in structure and TFA process
- 	Blocks users once too many attempts have been made
- 	Once user is blocked, emails are sent to account owner and admins
- 	Blocks expire after a set amount of minutes
- 	TFA is setup on account login instead of config area
- 	TFA is enforced through the ACL and binded to roles
- 	Reset TFA is possible through admin system config section
- 	Backup codes are available on setup
- 	Changed the look and feel of the skin (MaterializeCSS)
- 	New logo for jetrails