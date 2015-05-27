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

class PksAddTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->curl = null;
    $this->addUrl = sprintf("%s%s", TEST_URL_PREFIX, '/pks/add?');
  }

  public function tearDown () {
    curl_close($this->curl);
  }

  public function test_add_200 () {
    $fingerprint = '089971A2AC8609C5BE264605C58461F9F4A79B93';
    Helper::removeTestKey($this, $fingerprint);

    $params = '';
    $this->setupCurl($params);

    $keyFile = sprintf("%s/add-and-remove-key.asc", KEYRING_DIR);
    $keyText = file_get_contents($keyFile);

    curl_setopt($this->curl, CURLOPT_POSTFIELDS, array('keytext' => $keyText));
    $actualBody = curl_exec($this->curl);
    Helper::assert_200($this);

    $regex = Helper::toRegex('F4A79B93: ');
    $this->assertRegexp($regex, $actualBody);
    $regex = Helper::toRegex('"Add-and-Remove-Key (test key to add and remove) &lt;add.and.remove.key@example.org&gt;"');
    $this->assertRegexp($regex, $actualBody);
  }

  public function test_add_400 () {
    $params = '';
    $this->setupCurl($params);

    $keyFile = sprintf("%s/add-and-remove-key.asc", KEYRING_DIR);
    $keyText = "invalid wtf ".file_get_contents($keyFile);

    curl_setopt($this->curl, CURLOPT_POSTFIELDS, array('keytext' => $keyText));
    $actualBody = curl_exec($this->curl);
    Helper::assert_400($this, $actualBody);
  }

  public function setupCurl ($params) {
    $this->curl = curl_init();
    curl_setopt_array($this->curl, array(
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_URL => sprintf("%s%s", $this->addUrl, $params)
    ));
  }
}