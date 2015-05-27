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

class FormatterTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->model = new \View\Formatter();
  }

  public function test_formatFingerprint () {
    $fingerprint = 'D3124736B49BD7D617A89F248829B9B3139816ED';
    $expected = 'D312 4736 B49B D7D6 17A8  9F24 8829 B9B3 1398 16ED';
    $actual = $this->model->formatFingerprint($fingerprint);
    $this->assertEquals($expected, $actual);
  }

  public function test_formatTst () {
    $tst = "1426379526";
    $expected = "2015-03-15";
    $actual = $this->model->formatTst($tst);
    $this->assertEquals($expected, $actual);

    $tst = "";
    $expected = "__________";
    $actual = $this->model->formatTst($tst);
    $this->assertEquals($expected, $actual);
  }

  public function test_formatGetLink () {
    $path = '/admin';
    $keyId = '62D02FDAD58263D1';
    $text = 'something <> weird';
    $expected = "<a href='/admin/lookup?op=get&search=0x62D02FDAD58263D1'>something &lt;&gt; weird</a>";
    $actual = $this->model->formatGetLink($path, $keyId, $text);
    $this->assertEquals($expected, $actual);
  }

  public function test_formatKeyIdLink () {
    $path = '/pks';
    $keyId = '62D02FDAD58263D1';
    $expected = "<a href='/pks/lookup?op=get&search=0x62D02FDAD58263D1'>D58263D1</a>";
    $actual = $this->model->formatKeyIdLink($path, $keyId);
    $this->assertEquals($expected, $actual);
  }
}
