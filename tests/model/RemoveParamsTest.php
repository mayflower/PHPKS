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

class RemoveParamsTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->model = new \Model\RemoveParams();
  }

  public function test_valid_fingerprint () {
    $fingerprint = ' 089971A2AC8609C5BE264605C58461F9F4A79B93 ';
    $params = array('fingerprint' => $fingerprint);
    $this->model->setFingerprint($params);

    $expected = trim($fingerprint);
    $actual = $this->model->getFingerprint();
    $this->assertEquals($expected, $actual);
  }

  /**
   * @expectedException Model\Error\RemoveParam
   */
  public function test_missing_fingerprint () {
    $params = array();
    $this->model->setFingerprint($params);
  }

  /**
   * @expectedException Model\Error\RemoveParam
   */
  public function test_invalid_fingerprint () {
    $params = array('fingerprint' => 'invalid');
    $this->model->setFingerprint($params);
  }
}
