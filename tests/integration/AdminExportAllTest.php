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

class AdminExportAllTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->curl = null;
    $this->url = sprintf("%s%s", TEST_URL_PREFIX, '/admin/export-all');
  }

  public function tearDown () {
    curl_close($this->curl);
  }

  public function test_exportAll () {
    $keyFile = 'huge-import.asc';
    Helper::addTestKey($this, $keyFile);
    $this->setupCurl();

    $response = curl_exec($this->curl);

    Helper::assert_200($this);

    $actual = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);
    $this->assertRegexp('#text/plain#', $actual);

    $headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
    $actualHeader = substr($response, 0, $headerSize);
    $regex = Helper::toRegex(
      'Content-Disposition: attachment; filename="all-keys.asc"'
    );
    $this->assertRegexp($regex, $actualHeader);

    $actual = curl_getinfo($this->curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $this->assertGreaterThanOrEqual(78909, $actual);

    $actualBody = substr($response, $headerSize);
    $regex = Helper::toRegex('-----BEGIN PGP PUBLIC KEY BLOCK-----');
    $this->assertRegexp($regex, $actualBody);
    $regex = Helper::toRegex('-----END PGP PUBLIC KEY BLOCK-----');
    $this->assertRegexp($regex, $actualBody);
  }

  public function setupCurl () {
    $this->curl = curl_init();
    curl_setopt_array($this->curl, array(
      CURLOPT_HEADER          => true,
      CURLOPT_HTTPGET         => true,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_URL             => $this->url,
    ));
  }
}