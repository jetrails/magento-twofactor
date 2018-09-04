<h1 align="center" >Two-Factor Authentication</h1>
<p align="center" >
	<img src="https://img.shields.io/badge/Magento-1.x-orange.svg?style=for-the-badge" />
	<img src="https://img.shields.io/badge/License-MIT-orange.svg?style=for-the-badge" />
	<img src="https://img.shields.io/badge/Version-1.1.1-orange.svg?style=for-the-badge" />
</p>
</br>

About
=============================
This module is available on the <a href="https://marketplace.magento.com/jetrails-jetrails-twofactor.html" ><b>Magento Marketplace</b></a>. The JetRails 2FA plugin adds an extra layer of security to your Magento store.  User-based 2FA enablement ensures that admin users are following best security practices. This module has the following features:

- A Master Administrator can require 2FA to be utilized by specific users.
- Usage of 2FA can be enforced and required for log in.
- Once you use the 2FA to log in, there is an option to bypass authentication for a pre-configured number of days.
- A Master Administrator can oversee every user's authentication process.
- In the event of a lost or misplaced 2FA account, backup codes are available as an alternate method for authentication.
- In the event of an attempted account breach, prevention protocols are in place via brute-force protection, which will temporarily block the account.
- The threshold for the number of failed authentication attempts before a temporary ban is imposed is configurable.
- The duration of a user's temporary ban is configurable.
- An automatic instantaneous alert will be sent to the account owner and store admins informing them of an attempted breach. Any security warning will be logged with any relevant data such as the offender's IP address.
- The 2FA account can be setup for devices (something they have) using the Google Authenticator app, which is available for every platform including <a href="https://itunes.apple.com/us/app/google-authenticator/id388497605" >iPhone</a> and <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" >Android</a>.

Compatibility
=============================
Please refer to [COMPATIBILITY.md](COMPATIBILITY.md) to see which versions of Magento this extension was tested on and proved to be compatible with.

Documentation
=============================
The user guide can be found in the [doc](doc) folder.  The user guide goes through the installation process as well as explains all the features that comes with this plugin.

Build System
=============================
All JetRails® modules use __Grunt__ as a build system.  Grunt is a package that can be easily downloaded using __NPM__.  Once this repository is cloned, run `npm install grunt -g` followed by `npm install` to install Grunt and all Grunt modules used within this build system.  Please refer to the following table for a description of some useful grunt build commands. A typical grunt command takes the following form: `grunt task:argument`.

| Task       | Description                                                                                                                                                                                     |
|------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `version`  | Updates the version number in all __php__ and __xml__ files with the one defined in __package.json__.                                                                                           |
| `release`  | This command first runs __init__ and then __resolve__.  It then compresses the source and dependencies and outputs the archive in __dist__.  This command gets the repo ready for a git commit. |
| `deploy`   | Will upload dependencies and source code to a staging server.  Credentials to this server can be configured in the __package.json__ file under the _staging_ attribute.                         |
| `stream`   | Will watch the __lib__ and __src__ folders for any changes. Once a change occurs it will run the __deploy__ task.                                                                               |
|            | The default task is aliased to run the __release__ task.                                                                                                                                        |

Docker Environment
=============================
This project comes with a [docker-compose.yml](docker-compose.yml) and a [docker-sync.yml](docker-sync.yml) file, which can be used to spin up a Magento 1 environment. In order to use docker, please make sure you have **Docker**, **Docker Compose**, and **Docker Sync** installed. For information about configuring this docker environment, please refer to it's Github repository which can be found [here](https://github.com/jetrails/docker-magento-alpine).
