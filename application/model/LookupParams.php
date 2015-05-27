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
namespace Model;

class LookupParams extends AbstractParams {
  /**
   * @var string on or off
   */
  protected $exact;

  /**
   * @var string on or off
   */
  protected $fingerprint;

  /**
   * @var string get, index or vindex
   */
  protected $operation;

  /**
   * @var string search pattern
   */
  protected $search;

  public function __construct () {
    $this->validator = new Validator\LookupParams();
  }

  /*
   * sanitize and set all params
   * @param array $_GET or equivalent
   * @throws \Model\Error\OperationParam
   * @throws \Model\Error\SearchParam
   */
  public function setAll (array $params) {
    $this->setOperation($params);
    $this->setSearch($params);
    $this->setExact($params);
    $this->setFingerprint($params);
    $this->setOptions($params);
  }

  /**
   * sanitize and set exact
   * @param array $params $_GET or equivalent
   */
  public function setExact (array $params) {
    if (! array_key_exists('exact', $params)) {
      $this->exact = 'off';
      return;
    }
    if (! $this->validator->isValidExact($params['exact'])) {
      $this->exact = 'off';
      return;
    }
    $this->exact = $params['exact'];
  }

  /**
   * @return string
   */
  public function getExact () {
    return $this->exact;
  }

  /**
   * sanitize and set fingerprint
   * @param array $params $_GET or equivalent
   */
  public function setFingerprint (array $params) {
    if (! array_key_exists('fingerprint', $params)) {
      $this->fingerprint = 'off';
      return;
    }
    if (! $this->validator->isValidFingerprint($params['fingerprint'])) {
      $this->fingerprint = 'off';
      return;
    }
    $this->fingerprint = $params['fingerprint'];
  }

  /**
   * @return string
   */
  public function getFingerprint () {
    return $this->fingerprint;
  }

  /**
   * sanitize and set operation
   * @param array $params $_GET or equivalent
   * @throws \Model\Error\OperationParam
   */
  public function setOperation (array $params) {
    if (! array_key_exists('op', $params)) {
      throw new Error\OperationParam();
    }
    if (! $this->validator->isValidOperation($params['op'])) {
      throw new Error\OperationParam();
    }
    $this->operation = $params['op'];
  }

  /**
   * @return string
   */
  public function getOperation () {
    return $this->operation;
  }

  /**
   * sanitize and set search
   * @param array $params $_GET or equivalent
   * @throws \Model\Error\SearchParam
   */
  public function setSearch (array $params) {
    if (! array_key_exists('search', $params)) {
      throw new Error\SearchParam();
    }
    if (! $this->validator->isValidSearch($params['search'])) {
      throw new Error\SearchParam();
    }
    $this->search = $params['search'];
  }

  /**
   * @return string
   */
  public function getSearch () {
    return $this->search;
  }
}
