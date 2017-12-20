<p align="center" ><img height="50px" src="http://static.raffi.io/jetrails/logo.svg" /></p>
<h1 align="center" >Two-Factor Authentication</h1>
<p align="center" >
	<img src="https://img.shields.io/badge/Magento-1.x-green.svg?style=for-the-badge" />
	<img src="https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge" />
	<img src="https://img.shields.io/badge/Version-1.1.0-green.svg?style=for-the-badge" />
</p>
</br></br>

The JetRails 2FA plugin adds an extra layer of security to your Magento store.  User-based 2FA enablement ensures that admin users are following best security practices.

-	Backup codes as an alternate authentication method
-	Force 2FA usage based on Magento role
-	Remember authentication for 7 days
-	Send email to account owner and admins on account block
- 	Brute force protection (10 min. block between 10 failed attempts)

Compatibility
=============================
Please refer to `COMPATIBILITY.md` to see which versions of Magento this extension was tested on and proved to be compatible with.

User Guide
=============================
The user guide can be found in the _doc_ folder.  The user guide goes through the installation process as well as explains all the features that comes with this plugin.  The archive file can be found in the __dist__ folder or can be downloaded alongside the releases.

Build System
=============================
All JetRails® extensions use __Grunt__ as a build system.  Grunt is a package that can be easily downloaded using __NPM__.  Once this repository is cloned, run `npm install grunt -g` followed by `npm install` to install Grunt and all Grunt modules used within this build system.  Please refer to the following table for a description of some useful grunt build commands. A typical grunt command takes the following form: `grunt task:argument`.

| Task       | Description                                                                                                                                                                                     |
|------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `version`  | Updates the version number in all __php__ and __xml__ files with the one defined in __package.json__.                                                                                           |
| `release`  | This command first runs __init__ and then __resolve__.  It then compresses the source and dependencies and outputs the archive in __dist__.  This command gets the repo ready for a git commit. |
| `deploy`   | Will upload dependencies and source code to a staging server.  Credentials to this server can be configured in the __package.json__ file under the _staging_ attribute.                         |
| `stream`   | Will watch the __lib__ and __src__ folders for any changes. Once a change occurs it will run the __deploy__ task.                                                                               |
|            | The default task is aliased to run the __release__ task.                                                                                                                                        |
