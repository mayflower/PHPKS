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

class GnupgColonListingMachineReadableTest extends PHPUnit_Framework_TestCase {
  protected $firstListing = array(
    "tru::1:1426762702:1439660757:3:1:5",
    "pub:u:4096:1:8829B9B3139816ED:1426379526:::u:::scESC:",
    "fpr:::::::::D3124736B49BD7D617A89F248829B9B3139816ED:",
    "uid:u::::1426379526::B3C93CC043A539F083C9D7C8E409FF4F7D32EC7E::Tomate Test (I'm the master tomato) <tomate.test@example.org>:",
    "sig:::1:8829B9B3139816ED:1426379526::::Tomate Test (I'm the master tomato) <tomate.test@example.org>:13x:",
    "sig:::1:62D02FDAD58263D1:1426380293::::Tom Test <tom.test@example.org>:10x:",
    "sig:::1:999C3D063FE302BE:1426381233::::Dä Schäü Hör (umlauts and expiry) <daschauher@example.org>:10x:",
    "sub:u:4096:1:A41542AB06129534:1426379526::::::e:",
    "sig:::1:8829B9B3139816ED:1426379526::::Tomate Test (I'm the master tomato) <tomate.test@example.org>:18x:"
  );

  protected $secondListing = array(
    "pub:u:2048:1:CCA310527EA947D6:1426762701:1458298701::u:::scESC:",
    "fpr:::::::::5C1D54005B923935A677ADBCCCA310527EA947D6:",
    "uid:u::::1426762701::DF61F42F7A6DD09C5BC844C5607FBD2130BA96F4::test with expiration date <test-with-expiration-date@example.org>:",
    "sig:::1:CCA310527EA947D6:1426762701::::test with expiration date <test-with-expiration-date@example.org>:13x:",
    "sub:u:2048:1:B89A540785ACCBDD:1426762701:1458298701:::::e:",
    "sig:::1:CCA310527EA947D6:1426762701::::test with expiration date <test-with-expiration-date@example.org>:18x:"
  );

  public function setUp () {
    $this->model = new \Model\Parser\GnupgColonListingMachineReadable();
  }

  public function test_buildPubLine () {
    $tokens = array('pub', 'r', '2048', '1', 'CCA310527EA947D6', '1426762701', '1458298701', '', 'u', '', '', 'scESC', '');
    $expected = 'pub:CCA310527EA947D6:1:2048:1426762701:1458298701:r';
    $actual = $this->model->buildPubLine($tokens);
    $this->assertEquals($expected, $actual);
  }

  public function test_buildUidLine () {
    $tokens = array('uid', 'r', '', '', '', '1409187455', '', '33BECB938B12769FBBDD011452F0FF39EE7CD369', '', 'Dä Schäü Hör (umlauts and expiry) <daschauher@example.org>', '');
    $expected = 'uid:D%C3%A4%20Sch%C3%A4%C3%BC%20H%C3%B6r%20%28umlauts%20and%20expiry%29%20%3Cdaschauher%40example.org%3E:1409187455::r';
    $actual = $this->model->buildUidLine($tokens);
    $this->assertEquals($expected, $actual);
  }

  public function test_parse () {
    $lines = array_merge($this->firstListing, $this->secondListing);
    $expected = implode("\n", array(
      'info:1:2',
      'pub:8829B9B3139816ED:1:4096:1426379526::',
      'uid:Tomate%20Test%20%28I%27m%20the%20master%20tomato%29%20%3Ctomate.test%40example.org%3E:1426379526::',
      'pub:CCA310527EA947D6:1:2048:1426762701:1458298701:',
      'uid:test%20with%20expiration%20date%20%3Ctest-with-expiration-date%40example.org%3E:1426762701::'
    ));
    $actual = $this->model->parse($lines);
    $this->assertEquals($expected, $actual);
  }

  public function test_parse_no_data () {
    $lines = array();
    $expected = '';
    $actual = $this->model->parse($lines);
    $this->assertEquals($expected, $actual);
  }
}
