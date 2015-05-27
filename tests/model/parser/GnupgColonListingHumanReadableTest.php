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

class GnupgColonListingHumanReadableTest extends PHPUnit_Framework_TestCase {
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
    $this->model = new \Model\Parser\GnupgColonListingHumanReadable();
  }

  public function test_buildSections () {
    // gpg --with-colons --list-sigs --fixed-list-mode --utf8-strings --display-charset=utf-8 '@gagamux.net'
    $lines = array_merge($this->firstListing, $this->secondListing);
    $expected = array(
      array(
        array('pub', 'u', '4096', '1', '8829B9B3139816ED', '1426379526', '', '', 'u', '', '', 'scESC', ''),
        array('fpr', '', '', '', '', '', '', '', '', 'D3124736B49BD7D617A89F248829B9B3139816ED', ''),
        array('uid', 'u', '', '', '', '1426379526', '', 'B3C93CC043A539F083C9D7C8E409FF4F7D32EC7E', '', "Tomate Test (I'm the master tomato) <tomate.test@example.org>", ''),
        array('sig', '', '', '1', '8829B9B3139816ED', '1426379526', '', '', '', "Tomate Test (I'm the master tomato) <tomate.test@example.org>", '13x', ''),
        array('sig', '', '', '1', '62D02FDAD58263D1', '1426380293', '', '', '', 'Tom Test <tom.test@example.org>', '10x', ''),
        array('sig', '', '', '1', '999C3D063FE302BE', '1426381233', '', '', '', 'Dä Schäü Hör (umlauts and expiry) <daschauher@example.org>', '10x', ''),
        array('sig', '', '', '1', '8829B9B3139816ED', '1426379526', '', '', '', "Tomate Test (I'm the master tomato) <tomate.test@example.org>", '18x', ''),
      ),
      array(
        array('pub', 'u', '2048', '1', 'CCA310527EA947D6', '1426762701', '1458298701', '', 'u', '', '', 'scESC', ''),
        array('fpr', '', '', '', '', '', '', '', '', '5C1D54005B923935A677ADBCCCA310527EA947D6', ''),
        array('uid', 'u', '', '', '', '1426762701', '', 'DF61F42F7A6DD09C5BC844C5607FBD2130BA96F4', '', 'test with expiration date <test-with-expiration-date@example.org>', ''),
        array('sig', '', '', '1', 'CCA310527EA947D6', '1426762701', '', '', '', 'test with expiration date <test-with-expiration-date@example.org>', '13x', ''),
        array('sig', '', '', '1', 'CCA310527EA947D6', '1426762701', '', '', '', 'test with expiration date <test-with-expiration-date@example.org>', '18x', ''),
      )
    ) ;
    $actual = $this->model->buildSections($lines);
    $this->assertTrue(is_array($actual));
    $this->assertEquals(2, count($actual));
    $this->assertEquals(7, count($actual[0]));
    $this->assertEquals(5, count($actual[1]));
    $this->assertEquals($expected, $actual);
  }

  public function test_buildFprRecord () {
    $fprTokens = array('fpr', '', '', '', '', '', '', '', '', 'D3124736B49BD7D617A89F248829B9B3139816ED', '');
    $expected = array(
      'recordType'   => 'fpr',
      'fingerprint'  => 'D3124736B49BD7D617A89F248829B9B3139816ED',
    );
    $actual = $this->model->buildFprRecord($fprTokens);
    $this->assertEquals($expected, $actual);
  }

  public function test_buildPubRecord () {
    $tokenSection = array(
      array('pub', 'u', '2048', '1', 'CCA310527EA947D6', '1426762701', '1458298701', '', 'u', '', '', 'scESC', ''),
      array('fpr', '', '', '', '', '', '', '', '', '5C1D54005B923935A677ADBCCCA310527EA947D6', ''),
      array('uid', 'u', '', '', '', '1426762701', '', 'DF61F42F7A6DD09C5BC844C5607FBD2130BA96F4', '', 'test with expiration date <test-with-expiration-date@example.org>', ''),
    );
    $expected = array(
      'recordType'    => 'pub',
      'bits'          => '2048',
      'creationTst'   => '1426762701',
      'expirationTst' => '1458298701',
      'keyId'         => 'CCA310527EA947D6',
      'userId'        => "test with expiration date <test-with-expiration-date@example.org>",
    );
    $actual = $this->model->buildPubRecord($tokenSection);
    $this->assertEquals($expected, $actual);
  }

  public function test_buildSigRecord () {
    $sigTokens = array('sig', '', '', '1', '62D02FDAD58263D1', '1426380293', '', '', '', 'Tom Test <tom.test@example.org>', '10x', '');
    $expected = array(
      'recordType'    => 'sig',
      'creationTst'   => '1426380293',
      'expirationTst' => '',
      'keyId'         => '62D02FDAD58263D1',
      'userId'        => 'Tom Test <tom.test@example.org>',
    );
    $actual = $this->model->buildSigRecord($sigTokens);
    $this->assertEquals($expected, $actual);
  }

  public function test_buildUidRecord () {
    $uidTokens = array('uid', 'u', '', '', '', '1426379526', '', 'B3C93CC043A539F083C9D7C8E409FF4F7D32EC7E', '', "Tomate Test (I'm the master tomato) <tomate.test@example.org>", '');
    $expected = array(
      'recordType'    => 'uid',
      'creationTst'   => '1426379526',
      'expirationTst' => '',
      'userId'        => "Tomate Test (I'm the master tomato) <tomate.test@example.org>",
    );
    $actual = $this->model->buildUidRecord($uidTokens);
    $this->assertEquals($expected, $actual);
  }

  public function test_parseIndex () {
    // gpg --with-colons --list-sigs --fixed-list-mode --fingerprint --utf8-strings --display-charset=utf-8 '@gagamux.net'
    $lines = array_merge($this->firstListing, $this->secondListing);
    $expected = array(
      array(
        array(
          'recordType'    => 'pub',
          'bits'          => '4096',
          'creationTst'   => '1426379526',
          'expirationTst' => '',
          'keyId'         => '8829B9B3139816ED',
          'userId'        => "Tomate Test (I'm the master tomato) <tomate.test@example.org>"
        ),
        array(
          'recordType'    => 'fpr',
          'fingerprint'   => 'D3124736B49BD7D617A89F248829B9B3139816ED',
        ),
      ),
      array(
        array(
          'recordType'    => 'pub',
          'bits'          => '2048',
          'creationTst'   => '1426762701',
          'expirationTst' => '1458298701',
          'keyId'         => 'CCA310527EA947D6',
          'userId'        => 'test with expiration date <test-with-expiration-date@example.org>'
        ),
        array(
          'recordType'    => 'fpr',
          'fingerprint'   => '5C1D54005B923935A677ADBCCCA310527EA947D6',
        ),
      )
    );
    $actual = $this->model->parseIndex($lines);
    $this->assertEquals($expected, $actual);
  }

  public function test_parseVindex () {
    // gpg --with-colons --list-sigs --fixed-list-mode --fingerprint --utf8-strings --display-charset=utf-8 '@gagamux.net'
    $lines = array_merge($this->firstListing, $this->secondListing);
    $expected = array(
      array(
        array(
          'recordType'    => 'pub',
          'bits'          => '4096',
          'creationTst'   => '1426379526',
          'expirationTst' => '',
          'keyId'         => '8829B9B3139816ED',
          'userId'        => "Tomate Test (I'm the master tomato) <tomate.test@example.org>"
        ),
        array(
          'recordType'    => 'fpr',
          'fingerprint'   => 'D3124736B49BD7D617A89F248829B9B3139816ED',
        ),
        array(
          'recordType'    => 'uid',
          'creationTst'   => '1426379526',
          'expirationTst' => '',
          'userId'        => "Tomate Test (I'm the master tomato) <tomate.test@example.org>"
        ),
        array(
          'recordType'    => 'sig',
          'creationTst'   => '1426379526',
          'expirationTst' => '',
          'keyId'         => '8829B9B3139816ED',
          'userId'        => "Tomate Test (I'm the master tomato) <tomate.test@example.org>",
        ),
        array(
          'recordType'    => 'sig',
          'creationTst'   => '1426380293',
          'expirationTst' => '',
          'keyId'         => '62D02FDAD58263D1',
          'userId'        => 'Tom Test <tom.test@example.org>',
        ),
        array(
          'recordType'    => 'sig',
          'creationTst'   => '1426381233',
          'expirationTst' => '',
          'keyId'         => '999C3D063FE302BE',
          'userId'        => 'Dä Schäü Hör (umlauts and expiry) <daschauher@example.org>',
        ),
        array(
          'recordType'    => 'sig',
          'creationTst'   => '1426379526',
          'expirationTst' => '',
          'keyId'         => '8829B9B3139816ED',
          'userId'        => "Tomate Test (I'm the master tomato) <tomate.test@example.org>",
        ),
      ),
      array(
        array(
          'recordType'    => 'pub',
          'bits'          => '2048',
          'creationTst'   => '1426762701',
          'expirationTst' => '1458298701',
          'keyId'         => 'CCA310527EA947D6',
          'userId'        => 'test with expiration date <test-with-expiration-date@example.org>'
        ),
        array(
          'recordType'    => 'fpr',
          'fingerprint'   => '5C1D54005B923935A677ADBCCCA310527EA947D6',
        ),
        array(
          'recordType'    => 'uid',
          'creationTst'   => '1426762701',
          'expirationTst' => '',
          'userId'        => 'test with expiration date <test-with-expiration-date@example.org>',
        ),
        array(
          'recordType'    => 'sig',
          'creationTst'   => '1426762701',
          'expirationTst' => '',
          'keyId'         => 'CCA310527EA947D6',
          'userId'        => 'test with expiration date <test-with-expiration-date@example.org>',
        ),
        array(
          'recordType'    => 'sig',
          'creationTst'   => '1426762701',
          'expirationTst' => '',
          'keyId'         => 'CCA310527EA947D6',
          'userId'        => 'test with expiration date <test-with-expiration-date@example.org>',
        ),
      )
    );
    $actual = $this->model->parseVindex($lines);
    $this->assertEquals($expected, $actual);
  }
}
