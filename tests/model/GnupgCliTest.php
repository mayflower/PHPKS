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

class GnupgCliTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->model = new \Model\GnupgCli();
    $this->model->setKeyringDir(KEYRING_DIR);
  }

  public function test_addKey () {
    // add-and-remove-key.asc
    $fingerprint = '089971A2AC8609C5BE264605C58461F9F4A79B93';
    Helper::removeTestKey($this, $fingerprint);

    $keyFile = sprintf("%s/add-and-remove-key.asc", KEYRING_DIR);
    $keyText = file_get_contents($keyFile);
    $actual = explode("\n", $this->model->addKey($keyText));
    $this->assertEquals(1 + 2, count($actual));

    // the omitted parts in the regex may be translated :/
    // gpg: key F4A79B93: public key "Add-and-Remove-Key (test key to add and remove) <add.and.remove.key@example.org>" imported
    // gpg: Schlüssel F4A79B93: Öffentlicher Schlüssel "Add-and-Remove-Key (test key to add and remove) <add.and.remove.key@example.org>" importiert
    // gpg: key F4A79B93: "Add-and-Remove-Key (test key to add and remove) <add.and.remove.key@example.org>" not changed
    // gpg: Schlüssel F4A79B93: "Add-and-Remove-Key (test key to add and remove) <add.and.remove.key@example.org>" nicht geändert
    $regex = '/^gpg: .* F4A79B93:.+"Add-and-Remove-Key \(test key to add and remove\) <add\.and\.remove\.key@example\.org>" .+/';
    $this->assertRegExp($regex, $actual[0]);
  }

  public function test_addKey_huge_data () {
    Helper::removeHugeImportKeys($this);

    $keyFile = sprintf("%s/huge-import.asc", KEYRING_DIR);
    $keyText = file_get_contents($keyFile);
    $actual = explode("\n", $this->model->addKey($keyText));
    $this->assertEquals(50 + 2, count($actual));

    $regex = '/^gpg: .* 6AD570D8:.+"test-00@example.org \(test-00@example.org\) <test-00@example\.org>" .+/';
    $this->assertRegExp($regex, $actual[0]);

    $regex = '/^gpg: .* 88D34B38:.+"test-49@example.org \(test-49@example.org\) <test-49@example\.org>" .+/';
    $this->assertRegExp($regex, $actual[49]);
  }

  /**
   * @expectedException \Model\Error\Gnupg
   */
  public function test_addKey_fail () {
    $keyText = "something invalid";
    $this->model->addKey($keyText);
  }

  public function test_exportAll () {
    Helper::addTestKey($this, 'huge-import.asc');
    $actual = $this->model->exportAll();

    $regex = Helper::toRegex('-----BEGIN PGP PUBLIC KEY BLOCK-----');
    $this->assertRegexp($regex, $actual);

    $regex = Helper::toRegex('-----END PGP PUBLIC KEY BLOCK-----');
    $this->assertRegexp($regex, $actual);

    $this->assertGreaterThanOrEqual(1217, count(explode("\n", $actual)));
  }

  public function test_getArmoredKeys () {
    Helper::addTestKey($this, 'tom.asc');

    $lookupParams = $this->getMockBuilder('Model\\LookupParams')
      ->disableOriginalConstructor()->getMock();
    $lookupParams->method('getSearch')->willReturn('tom.test@example.org');

    $expected = trim(file_get_contents(sprintf(
      '%s/%s',
      KEYRING_DIR,
      'tom.asc'
    )));
    $actual = $this->model->getArmoredKeys($lookupParams);
    $this->assertEquals($expected, $actual);
  }

  public function test_getArmoredKeys_no_data () {
    $lookupParams = $this->getMockBuilder('Model\\LookupParams')
      ->disableOriginalConstructor()->getMock();
    $lookupParams->method('getSearch')->willReturn('no-such-key-hopefully');

    $expected = '';
    $actual = $this->model->getArmoredKeys($lookupParams);
    $this->assertEquals($expected, $actual);
  }

  public function test_getIndexMachineReadable () {
    Helper::addTestKey($this, 'tomate.asc');

    $lookupParams = $this->getMockBuilder('Model\\LookupParams')
      ->disableOriginalConstructor()->getMock();
    $lookupParams->method('getSearch')->willReturn('tomate.test@example.org');

    $expected = implode("\n", array(
      'info:1:1',
      "pub:8829B9B3139816ED:1:4096:1426379526::",
      "uid:Tomate%20Test%20%28I%27m%20the%20master%20tomato%29%20%3Ctomate.test%40example.org%3E:1426379526::",
    ));
    $actual = $this->model->getIndexMachineReadable($lookupParams);
    $this->assertEquals($expected, $actual);
  }

  public function test_getIndexMachineReadable_no_data () {
    $lookupParams = $this->getMockBuilder('Model\\LookupParams')
      ->disableOriginalConstructor()->getMock();
    $lookupParams->method('getSearch')->willReturn('this-should-not-exist');

    $expected = '';
    $actual = $this->model->getIndexMachineReadable($lookupParams);
    $this->assertEquals($expected, $actual);
  }

  public function test_getIndexHumanReadable () {
    Helper::addTestKey($this, 'tomate.asc');

    $lookupParams = $this->getMockBuilder('Model\\LookupParams')
      ->disableOriginalConstructor()->getMock();
    $lookupParams->method('getSearch')->willReturn('tomate.test@example.org');

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
          'fingerprint'   => 'D3124736B49BD7D617A89F248829B9B3139816ED'
        ),
      )
    );
    $actual = $this->model->getIndexHumanReadable($lookupParams);
    $this->assertEquals($expected, $actual);
  }

  public function test_getIndexHumanReadable_no_data () {
    $lookupParams = $this->getMockBuilder('Model\\LookupParams')
      ->disableOriginalConstructor()->getMock();
    $lookupParams->method('getSearch')->willReturn('this-should-not-exist');

    $expected = array();
    $actual = $this->model->getIndexHumanReadable($lookupParams);
    $this->assertEquals($expected, $actual);
  }

  public function test_getVindexHumanReadable () {
    Helper::addTestKey($this, 'daschauher.asc');
    Helper::addTestKey($this, 'tom.asc');
    Helper::addTestKey($this, 'tomate.asc');

    $lookupParams = $this->getMockBuilder('Model\\LookupParams')
      ->disableOriginalConstructor()->getMock();
    $lookupParams->method('getSearch')->willReturn('tomate.test@example.org');

    $expected = array(
      array(
        array(
          'recordType'    => 'pub',
          'bits'          => '4096',
          'creationTst'   => '1426379526',
          'expirationTst' => '',
          'keyId'         => '8829B9B3139816ED',
          'userId'        => 'Tomate Test (I\'m the master tomato) <tomate.test@example.org>'
        ),
        array(
          'recordType'    => 'fpr',
          'fingerprint'   => 'D3124736B49BD7D617A89F248829B9B3139816ED'
        ),
        array(
          'recordType'    => 'uid',
          'creationTst'   => '1426379526',
          'expirationTst' => '',
          'userId'        => 'Tomate Test (I\'m the master tomato) <tomate.test@example.org>'
        ),
        array(
          'recordType'    => 'sig',
          'creationTst'   => '1426379526',
          'expirationTst' => '',
          'keyId'         => '8829B9B3139816ED',
          'userId'        => "Tomate Test (I'm the master tomato) <tomate.test@example.org>"
        ),
        array(
          'recordType'    => 'sig',
          'creationTst'   => '1426380293',
          'expirationTst' => '',
          'keyId'         => '62D02FDAD58263D1',
          'userId'        => 'Tom Test <tom.test@example.org>'
        ),
        array(
          'recordType'    => 'sig',
          'creationTst'   => '1426381233',
          'expirationTst' => '',
          'keyId'         => '999C3D063FE302BE',
          'userId'        => 'Dä Schäü Hör (umlauts and expiry) <daschauher@example.org>'
        ),
        array(
          'recordType'    => 'sig',
          'creationTst'   => '1426379526',
          'expirationTst' => '',
          'keyId'         => '8829B9B3139816ED',
          'userId'        => "Tomate Test (I'm the master tomato) <tomate.test@example.org>"
        )
      )
    );
    $actual = $this->model->getVindexHumanReadable($lookupParams);
    $this->assertEquals($expected, $actual);
  }

  public function test_getVindexHumanReadable_no_data () {
    $lookupParams = $this->getMockBuilder('Model\\LookupParams')
      ->disableOriginalConstructor()->getMock();
    $lookupParams->method('getSearch')->willReturn('this-should-not-exist');

    $expected = array();
    $actual = $this->model->getVindexHumanReadable($lookupParams);
    $this->assertEquals($expected, $actual);
  }

  public function test_removeKey_existing_key () {
    Helper::addTestKey($this, 'add-and-remove-key.asc');
    $fingerprint = '089971A2AC8609C5BE264605C58461F9F4A79B93';

    $removeParams = $this->getMockBuilder('Model\\RemoveParams')
      ->disableOriginalConstructor()->getMock();
    $removeParams->method('getFingerprint')->willReturn($fingerprint);

    $this->assertTrue($this->model->removeKey($removeParams));
    $this->assert_key_does_not_exist($fingerprint);
  }

  public function test_removeKey_nonexisting_key () {
    $fingerprint = '089971A2AC8609C5BE264605C58461F9F4A79B93';
    Helper::removeTestKey($this, $fingerprint);

    $removeParams = $this->getMockBuilder('Model\\RemoveParams')
      ->disableOriginalConstructor()->getMock();
    $removeParams->method('getFingerprint')->willReturn($fingerprint);

    $this->assertFalse($this->model->removeKey($removeParams));
  }

  protected function assert_key_does_not_exist ($fingerprint) {
    $cmd = sprintf(
      "%s --homedir %s --export --armor --no-permission-warning %s 2>/dev/null",
      escapeshellarg(GPG_BINARY),
      escapeshellarg(KEYRING_DIR),
      escapeshellarg($fingerprint)
    );
    $output = array();
    $rc = null;

    exec($cmd, $output, $rc);
    $this->assertEquals(0, $rc);
    $this->assertEquals(array(), $output);
  }
}
