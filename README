# PHP Public Key Server PHPKS

## Intro

This application implements a public key server according to
http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00

The specification is not too detailed. So I oriented myself towards existing
servers like https://pgp.mit.edu/ or http://pgp.zdv.uni-mainz.de:11371/


# Contents
## LICENSE
## INSTALLATION
## CONFIGURATION
### PRODUCTION MODE
### TESTING MODE
### ADMIN MODE
## API
## DEVELOPMENT
## IMPLEMENTED
## NOT IMPLEMENTED
## TODO
## HOWTO


## Licence

Please see the LICENCE file in the distribution.



## INSTALLATION

Please see the INSTALL file in the distribution.



## CONFIGURATION

The application is configured in config.php
@see PRODUCTION MODE
@see TESTING MODE

Set GPG_BINARY to the correct path.
You may find the correct path by calling "which gpg".

You may set the APPLICATION_TITLE in config.php
It will be displayed on html pages (title, headline).

There is an admin section where you may delete keys.
You may turn it off by setting ADMIN_MODE_AVAILABLE to false.
@see ADMIN_MODE


### PRODUCTION MODE

To turn on PRODUCTION mode edit config.php, replace the line
* $_ENV['SLIM_MODE'] = 'TESTING';
with
* $_ENV['SLIM_MODE'] = 'PRODUCTION';

see comment in config.php

#### In PRODUCTION mode tests won't run and issue a hint.
When NOT in PRODUCTION mode the website uses the testing keyring
and displays a warning.

#### Make sure the webserver has read and write access to
* keys/pubring.gpg
* keys/pubring.gpg~
* keys/trustdb.gpg


### TESTING MODE

Unit tests and integration tests use the pubring and trustdb configured
in config.php. By default this is tests/keys
In TESTING mode the website uses the testing keyring and displays a warning.
Don't run tests on the production pubring!

To run tests edit config.php, replace the line
* $_ENV['SLIM_MODE'] = 'PRODUCTION';
with
* $_ENV['SLIM_MODE'] = 'TESTING';

see comment in config.php

If not configured properly tests won't run and issue a hint.

* To run all tests issue "phpunit".
* To run unit tests issue "phpunit tests/model" and "phpunit tests/view".
* To run integration tests issue "phpunit tests/integration".
@see DEVELOPMENT section

#### Tests will fail on invalid access rights.
When working with vagrant/puppet the webserver runs as "user" so this is not
an issue, you may skip the next lines.

#### Take care for access rights, this is quite some !@#$:
* "user" is the user running phpunit.
* "www-data" is the webserver group.
* Both need write access to tests/keys.

* # add user to webserver group
* # the user will need to re-login for effectively being a member of www-data
* usermod -a -G www-data user

* # tests/keys accessible and sticky group
* chown user:www-data tests/keys
* chmod 2770 tests/keys

* # pubring.gpg and pubring.gpg~
* chown user:www-data tests/keys/pubring.gpg*
* chmod 660 tests/keys/pubring.gpg*

* # trustdb.gpg
* chown user:www-data tests/keys/trustdb.gpg
* chmod 660 tests/keys/trustdb.gpg


### ADMIN MODE

There is an admin mode where you are able to remove keys and export all keys.
For everything else "man gpg". One of your friends is --homedir.

* Url is /admin
* Keys are identified by their fingerprint.
* It might be wise to restrict access from public.
* The admin section is turned off by default. You may turn it on by setting
  ADMIN_MODE_AVAILABLE to true in application/config.php

* Restrict access to /admin (example for apache2, basic auth)
  <Location /admin>
    AuthType basic
    AuthName "admin area"
    AuthUserFile /var/www/keys/htpasswd
    Require valid-user
  </Location>

* Create admin-user and password
* # htpasswd -c /var/www/keys/htpasswd admin
* @see https://httpd.apache.org/docs/2.4/programs/htpasswd.html
* You may need to install htpasswd first.
  On Ubuntu issue "apt-get install apache2-utils".



## API

### Submitting Keys
* HTTP POST URL:
 /pks/add
 e.g. http://localhost:11371/pks/add

* HTTP POST PARAMETERS:
** keytext=<pgp block>
 e.g. keytext=-----BEGIN PGP PUBLIC KEY BLOCK-----...
** options=nm (optional) -- No Modification Option, not implemented

### Lookup
#### HTTP GET URL:
 /pks/lookup

#### PARAMETERS:
* op=[get|index|vindex]
* search=<searchstring>

#### OPTIONAL PARAMETERS:
* options=mr -- Machine Readable Option
* exact=[on|off] -- Exact Match Option, NOT IMPLEMENTED
* fingerprint=[on|off] -- Show Fingerprint Option (only for index/vindex)



## DEVELOPMENT

### framework
* This application is built using Slim - a micro framework for PHP
* @see http://www.slimframework.com/

### autoloading, namespaces
* There is a mapping from namespaces to paths in composer.json for autoloading.
* When you add new paths or namespaces edit composer.json, section autoload
  then run "php composer.phar dump-autoload"

### composer
* To get composer @see https://getcomposer.org/

### phpunit
* The application has been developed test driven so far.
* To get phpunit @see https://phpunit.de/manual/current/en/installation.html

### tests
* To run tests configure the application for TESTING mode, see TESTING section.
* To run all tests issue "phpunit".
* To run unit tests issue "phpunit tests/model" and "phpunit tests/view".
* To run integration tests issue "phpunit tests/integration".

### CAUTION
* Please take special care this program ALWAYS properly escapes any
  data sent to the commandline gpg using escapeshellarg() and the likes!
* @see http://php.net/manual/en/function.escapeshellarg.php
* @see http://php.net/manual/en/function.escapeshellcmd.php
* Thank you.



## IMPLEMENTED

### The "get" operation
* ?op=get
* @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.1.2.1
* example: /pks/lookup?op=get&search=example.org


### The "index" Operation
* ?op=index
* @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.1.2.2
* example: /pks/lookup?op=index&search=example.org


### The "vindex" (verbose index) Operation
* ?op=vindex
* @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.1.2.3
* example: /pks/lookup?op=vindex&search=example.org


### The "fingerprint" Variable
* ?fingerprint=on
* @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.2.2
* example: /pks/lookup?op=index&search=example.org&fingerprint=on
* example: /pks/lookup?op=vindex&search=example.org&fingerprint=on


### The "mr" (Machine Readable) Option
* ?options=mr
* @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.2.1.1
* example: /pks/lookup?op=get&search=example.org&options=mr
* example: /pks/lookup?op=index&search=example.org&options=mr


### Not specified:
* The minimum length of the search param is hardcoded to 3 characters.
* The maximum length of the search param is hardcoded to 320 characters.



## NOT IMPLEMENTED

Some keyservers lookup key ids only when prefixed with "0x".
The underlaying gnupg does not care about it. So don't I.
Searching with or without a leading 0x makes no difference.
Anything you search for will be searched for in any field.


### The "exact" Variable
* ?exact=on
* @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.2.3
* This one is intentionally left out.
It is recognized, validated (on or off) and is available in the model.
I did not find a proper way to tell the underlaying gnupg to care about it.
Doing so within the application is possible but nonsense in my eyes.


### The "nm" (No Modification) Option
* ?options=nm
* @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.2.1.2
* This one is intentionally left out.
It is recognized, validated (on or off) and is available in the model.
A keyserver is allowed to alter the email address of a submitted key
so it always points to the keyserver's owner's domain for instance.
A feature like this seems questionable to me. It is neither specified nor
implemented.
So there is no need to tell the server to reject a submitted key in case
this restriction would apply.



## TODO

see TODO file



## HOWTO

have a look at these files:
* docs/apache-vhost.conf
* docs/howto-thunderbird-enigmail.txt