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

class LookupParamsValidatorTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->model = new \Model\Validator\LookupParams();
  }

  public function test_isValidExact () {
    $validValues = array('on', 'off');
    foreach ($validValues as $value) {
      $this->assertTrue($this->model->isValidExact($value));
    }

    $invalidValues = array('ON', 'nonsense', '');
    foreach ($invalidValues as $value) {
      $this->assertFalse($this->model->isValidExact($value));
    }
  }

  public function test_isValidFingerprint () {
    $validValues = array('on', 'off');
    foreach ($validValues as $value) {
      $this->assertTrue($this->model->isValidFingerprint($value));
    }

    $invalidValues = array('ON', 'nonsense', '');
    foreach ($invalidValues as $value) {
      $this->assertFalse($this->model->isValidFingerprint($value));
    }
  }

  public function test_isValidOperation () {
    $validValues = array('get', 'index', 'vindex');
    foreach ($validValues as $value) {
      $this->assertTrue($this->model->isValidOperation($value));
    }

    $invalidValues = array('GET', 'nonsense', '');
    foreach ($invalidValues as $value) {
      $this->assertFalse($this->model->isValidOperation($value));
    }
  }

  public function test_isValidOption () {
    $validValues = array('mr');
    foreach ($validValues as $value) {
      $this->assertTrue($this->model->isValidOption($value));
    }

    $invalidValues = array('MR', 'nm', 'nonsense', '');
    foreach ($invalidValues as $value) {
      $this->assertFalse($this->model->isValidOption($value));
    }
  }

  public function test_isValidSearch () {
    $validValues = array(
      '0x0123',
      'abc',
      '  GA GA  ',
    );
    foreach ($validValues as $value) {
      $this->assertTrue($this->model->isValidSearch($value));
    }

    $longestAllowed = '';
    for ($i = 1; $i <= 20; $i ++) {
      $longestAllowed .= '0123456789abcdef';
    }
    $this->assertEquals(320, strlen($longestAllowed));
    $this->assertTrue($this->model->isValidSearch($longestAllowed));

    $tooShort = array('', 'x', '12');
    foreach ($tooShort as $value) {
      $this->assertFalse($this->model->isValidSearch($value));
    }

    $tooLong = $longestAllowed . 'x';
    $this->assertFalse($this->model->isValidSearch($tooLong));
  }
}
