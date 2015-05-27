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

class AddParamsValidatorTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->model = new \Model\Validator\AddParams();
  }

  public function test_isValidKeytext_valid () {
    $validKeytext = trim(file_get_contents(sprintf(
      "%s/%s",
      KEYRING_DIR,
      "rsa-1024-sign-only.asc"
    )));
    $this->assertTrue($this->model->isValidKeytext($validKeytext));
  }

  public function test_isValidKeytext_invalid () {
    $invalid = "not a key";
    $this->assertFalse($this->model->isValidKeytext($invalid));
  }

  public function test_isValidOption () {
    $validValues = array('nm');
    foreach ($validValues as $value) {
      $this->assertTrue($this->model->isValidOption($value));
    }

    $invalidValues = array('NM', 'mr', 'nonsense', '');
    foreach ($invalidValues as $value) {
      $this->assertFalse($this->model->isValidOption($value));
    }
  }
}
