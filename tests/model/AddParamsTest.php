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

class AddParamsTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->model = new \Model\AddParams();
  }

  public function test_options () {
    $params = array();
    $this->model->setOptions($params);
    $this->assertTrue(is_array($this->model->getOptions()));
    $this->assertEquals(0, count($this->model->getOptions()));

    $params = array('options' => '');
    $this->model->setOptions($params);
    $this->assertTrue(is_array($this->model->getOptions()));
    $this->assertEquals(0, count($this->model->getOptions()));

    $params = array('options' => 'nm,nm,nonsense');
    $this->model->setOptions($params);
    $this->assertTrue(is_array($this->model->getOptions()));
    $this->assertEquals(1, count($this->model->getOptions()));
    $this->assertTrue(in_array('nm', $this->model->getOptions()));
  }

  public function test_valid_keytext () {
    $keytext = file_get_contents(sprintf(
      "%s/%s",
      KEYRING_DIR,
      "rsa-1024-sign-only.asc"
    ));
    $params = array('keytext' => $keytext);
    $this->model->setKeytext($params);

    $expected = trim($keytext);
    $actual = $this->model->getKeytext();
    $this->assertEquals($expected, $actual);
  }

  /**
   * @expectedException Model\Error\AddParam
   */
  public function test_missing_keytext () {
    $params = array();
    $this->model->setKeytext($params);
  }

  /**
   * @expectedException Model\Error\AddParam
   */
  public function test_invalid_keytext () {
    $params = array('keytext' => 'invalid');
    $this->model->setKeytext($params);
  }
}
