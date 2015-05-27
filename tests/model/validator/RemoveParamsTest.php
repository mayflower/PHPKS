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

class RemoveParamsValidatorTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->model = new \Model\Validator\RemoveParams();
  }

  public function test_isValidFingerprint () {
    $valid = 'DF3AC76728A9BD301D289842CDC4B5188CEAEBF4';
    $this->assertTrue($this->model->isValidFingerprint($valid));

    $nonsense = '';
    $this->assertFalse($this->model->isValidFingerprint($nonsense));

    $tooShort = '123456789012345678901234567890123456789';
    $this->assertEquals(39, strlen($tooShort));
    $this->assertFalse($this->model->isValidFingerprint($tooShort));

    $tooLong = '12345678901234567890123456789012345678901';
    $this->assertEquals(41, strlen($tooLong));
    $this->assertFalse($this->model->isValidFingerprint($tooLong));

    $notHex = 'X234567890123456789012345678901234567890';
    $this->assertEquals(40, strlen($notHex));
    $this->assertFalse($this->model->isValidFingerprint($notHex));
  }
}
