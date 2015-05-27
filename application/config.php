<?php
/*
 * This file is part of PHPKS, a PHP Key Server.
 * Copyright 2015 Peter Ritt <devel AT gagamux.net>
 *
 * PHPKS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPKS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHPKS.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  APPLICATION_MODE string
 *    value TESTING:
 *    configures the application to use the keyring in tests/keys/
 *    the website will show a warning
 *
 *    value PRODUCTION:
 *    configures the application to use the keyring in keys/
 *    tests won't run
 */
//define('APPLICATION_MODE', 'PRODUCTION');
define('APPLICATION_MODE', 'TESTING');

if (APPLICATION_MODE == 'TESTING') {
  /**
   * KEYRING_DIR string where the testing pubring.gpg and trustdb.gpg are
   */
  define('KEYRING_DIR', '/var/www/phpks/tests/keys');

  /**
   * TEST_URL_PREFIX string
   */
  define('TEST_URL_PREFIX', 'http://keys.example.org');

  /**
   * ADMIN_MODE_AVAILABLE bool
   * must be true to successfully run integration tests
   */
  define('ADMIN_MODE_AVAILABLE', true);
}
elseif (APPLICATION_MODE == 'PRODUCTION') {
  /**
   * KEYRING_DIR string where the production pubring.gpg and trustdb.gpg are
   */
  define('KEYRING_DIR', '/var/www/phpks/keys');

  /**
   * ADMIN_MODE_AVAILABLE bool
   * true to enable admin mode
   */
  define('ADMIN_MODE_AVAILABLE', false);
}
else {
  throw new \Exception(
    'application is not configured, @see application/config.php'
  );
}

/**
 * GPG_BINARY string
 * output of "which gpg"
 */
define('GPG_BINARY', '/usr/bin/gpg');

/**
 * APPLICATION_TITLE string
 */
define('APPLICATION_TITLE', 'Public Key Server');
