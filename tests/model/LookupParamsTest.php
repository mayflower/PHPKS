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

class LookupParamsTest extends PHPUnit_Framework_TestCase {
  public function setUp () {
    $this->model = new \Model\LookupParams();
  }

  public function test_valid_exact () {
    $params = array('exact' => 'on');
    $this->model->setExact($params);
    $expected = 'on';
    $actual = $this->model->getExact();
    $this->assertEquals($expected, $actual);

    $params = array('exact' => 'off');
    $this->model->setExact($params);
    $expected = 'off';
    $actual = $this->model->getExact();
    $this->assertEquals($expected, $actual);
  }

  public function test_invalid_exact () {
    $params = array();
    $this->model->setExact($params);
    $expected = 'off';
    $actual = $this->model->getExact();
    $this->assertEquals($expected, $actual);

    $params = array('exact' => 'nonsense');
    $this->model->setExact($params);
    $expected = 'off';
    $actual = $this->model->getExact();
    $this->assertEquals($expected, $actual);
  }

  public function test_valid_fingerprint () {
    $params = array('fingerprint' => 'on');
    $this->model->setFingerprint($params);
    $expected = 'on';
    $actual = $this->model->getFingerprint();
    $this->assertEquals($expected, $actual);

    $params = array('fingerprint' => 'off');
    $this->model->setFingerprint($params);
    $expected = 'off';
    $actual = $this->model->getFingerprint();
    $this->assertEquals($expected, $actual);
  }

  public function test_invalid_fingerprint () {
    $params = array();
    $this->model->setFingerprint($params);
    $expected = 'off';
    $actual = $this->model->getFingerprint();
    $this->assertEquals($expected, $actual);

    $params = array('fingerprint' => 'nonsense');
    $this->model->setFingerprint($params);
    $expected = 'off';
    $actual = $this->model->getFingerprint();
    $this->assertEquals($expected, $actual);
  }

  public function test_valid_operation () {
    $params = array('op' => 'get');
    $this->model->setOperation($params);
    $expected = 'get';
    $actual = $this->model->getOperation();
    $this->assertEquals($expected, $actual);

    $params = array('op' => 'index');
    $this->model->setOperation($params);
    $expected = 'index';
    $actual = $this->model->getOperation();
    $this->assertEquals($expected, $actual);

    $params = array('op' => 'vindex');
    $this->model->setOperation($params);
    $expected = 'vindex';
    $actual = $this->model->getOperation();
    $this->assertEquals($expected, $actual);
  }

  /**
   * @expectedException Model\Error\OperationParam
   */
  public function test_missing_operation () {
    $params = array();
    $this->model->setOperation($params);
  }

  /**
   * @expectedException Model\Error\OperationParam
   */
  public function test_invalid_operation () {
    $params = array('op' => 'nonsense');
    $this->model->setOperation($params);
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

    $params = array('options' => 'mr,mr,nonsense');
    $this->model->setOptions($params);
    $this->assertTrue(is_array($this->model->getOptions()));
    $this->assertEquals(1, count($this->model->getOptions()));
    $this->assertTrue(in_array('mr', $this->model->getOptions()));
  }

  public function test_valid_search () {
    $params = array('search' => 'whatTheFoo');
    $this->model->setSearch($params);

    $expected = 'whatTheFoo';
    $actual = $this->model->getSearch();
    $this->assertEquals($expected, $actual);
  }

  /**
   * @expectedException Model\Error\SearchParam
   */
  public function test_missing_search () {
    $params = array();
    $this->model->setSearch($params);
  }

  /**
   * @expectedException Model\Error\SearchParam
   */
  public function test_invalid_search () {
    $params = array('search' => '');
    $this->model->setSearch($params);
  }
}
