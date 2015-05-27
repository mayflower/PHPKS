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

class RedirectTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->curl = null;
  }

  public function tearDown () {
    curl_close($this->curl);
  }

  public function test_get_admin_add () {
    $url = sprintf("%s%s", TEST_URL_PREFIX, '/admin/add');
    $this->setupCurl($url);
    curl_exec($this->curl);
    $expectedLocation = sprintf("%s%s", TEST_URL_PREFIX, '/admin');
    Helper::assert_301($this, $expectedLocation);
  }

  public function test_get_admin_lookup_without_params () {
    $url = sprintf("%s%s", TEST_URL_PREFIX, '/admin/lookup');
    $this->setupCurl($url);
    curl_exec($this->curl);
    $expectedLocation = sprintf("%s%s", TEST_URL_PREFIX, '/admin');
    Helper::assert_301($this, $expectedLocation);
  }

  public function test_get_admin_remove () {
    $url = sprintf("%s%s", TEST_URL_PREFIX, '/admin/remove');
    $this->setupCurl($url);
    curl_exec($this->curl);
    $expectedLocation = sprintf("%s%s", TEST_URL_PREFIX, '/admin');
    Helper::assert_301($this, $expectedLocation);
  }

  public function test_get_pks () {
    $url = sprintf("%s%s", TEST_URL_PREFIX, '/pks');
    $this->setupCurl($url);
    curl_exec($this->curl);
    $expectedLocation = sprintf("%s%s", TEST_URL_PREFIX, '/');
    Helper::assert_301($this, $expectedLocation);
  }

  public function test_get_pks_add () {
    $url = sprintf("%s%s", TEST_URL_PREFIX, '/pks/add');
    $this->setupCurl($url);
    curl_exec($this->curl);
    $expectedLocation = sprintf("%s%s", TEST_URL_PREFIX, '/');
    Helper::assert_301($this, $expectedLocation);
  }

  public function test_get_pks_lookup_without_params () {
    $url = sprintf("%s%s", TEST_URL_PREFIX, '/pks/lookup');
    $this->setupCurl($url);
    curl_exec($this->curl);
    $expectedLocation = sprintf("%s%s", TEST_URL_PREFIX, '/');
    Helper::assert_301($this, $expectedLocation);
  }

  public function setupCurl ($url) {
    $this->curl = curl_init();
    curl_setopt_array($this->curl, array(
      CURLOPT_HTTPGET         => true,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_URL             => $url
    ));
  }
}