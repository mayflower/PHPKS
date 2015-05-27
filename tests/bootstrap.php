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
require 'application/config.php';

if (APPLICATION_MODE == 'PRODUCTION') {
  echo "********************************************".PHP_EOL;
  echo "NO TESTS RAN".PHP_EOL;
  echo "application is configured in PRODUCTION mode".PHP_EOL;
  echo "@see application/config.php".PHP_EOL;
  echo "********************************************".PHP_EOL;
  exit(1);
}

require 'tests/helper.php';
require 'vendor/autoload.php';
