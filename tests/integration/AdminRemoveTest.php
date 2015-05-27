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

class AdminRemoveTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->curl = null;
    $this->url = sprintf("%s%s", TEST_URL_PREFIX, '/admin/remove?');
  }

  public function tearDown () {
    curl_close($this->curl);
  }

  public function test_remove_200_existing_key () {
    $keyFile = 'add-and-remove-key.asc';
    Helper::addTestKey($this, $keyFile);

    $params = '';
    $this->setupCurl($params);

    $fingerprint = '089971A2AC8609C5BE264605C58461F9F4A79B93';
    curl_setopt(
      $this->curl,
      CURLOPT_POSTFIELDS,
      array('fingerprint' => $fingerprint)
    );
    $actualBody = curl_exec($this->curl);

    Helper::assert_200($this);

    $regex = Helper::toRegex('The key was removed');
    $this->assertRegexp($regex, $actualBody);
  }

  public function test_remove_200_non_existing_key () {
    $fingerprint = '089971A2AC8609C5BE264605C58461F9F4A79B93';
    Helper::removeTestKey($this, $fingerprint);

    $params = '';
    $this->setupCurl($params);

    curl_setopt(
      $this->curl,
      CURLOPT_POSTFIELDS,
      array('fingerprint' => $fingerprint)
    );
    $actualBody = curl_exec($this->curl);

    Helper::assert_200($this);

    $regex = Helper::toRegex('The key to remove did not exist');
    $this->assertRegexp($regex, $actualBody);
  }

  public function test_remove_400 () {
    $params = '';
    $this->setupCurl($params);

    $invalidFingerprint = '';
    curl_setopt(
      $this->curl,
      CURLOPT_POSTFIELDS,
      array('fingerprint' => $invalidFingerprint)
    );
    $actualBody = curl_exec($this->curl);
    Helper::assert_400($this, $actualBody);
  }

  public function setupCurl ($params) {
    $this->curl = curl_init();
    curl_setopt_array($this->curl, array(
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_URL => sprintf("%s%s", $this->url, $params)
    ));
  }
}