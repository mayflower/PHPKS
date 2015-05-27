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

class RemoveParams extends AbstractParams {
  /**
   * @var string fingerprint of key to delete
   */
  protected $fingerprint;

  public function __construct () {
    $this->validator = new Validator\RemoveParams();
  }

  /**
   * sanitize and set fingerprint
   * @param array $params $_POST or equivalent
   * @throws \Model\Error\RemoveParam
   */
  public function setFingerprint (array $params) {
    if (! array_key_exists('fingerprint', $params)) {
      throw new Error\RemoveParam();
    }
    if (! $this->validator->isValidFingerprint($params['fingerprint'])) {
      throw new Error\RemoveParam();
    }
    $this->fingerprint = trim($params['fingerprint']);
  }

  /**
   * @return string
   */
  public function getFingerprint () {
    return $this->fingerprint;
  }
}
