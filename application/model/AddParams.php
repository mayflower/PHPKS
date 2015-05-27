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

class AddParams extends AbstractParams {
  /**
   * @var string search keytext
   */
  protected $keytext;

  public function __construct () {
    $this->validator = new Validator\AddParams();
  }

  /**
   * sanitize and set keytext
   * @param array $params $_POST or equivalent
   * @throws \Model\Error\AddParam
   */
  public function setKeytext (array $params) {
    if (! array_key_exists('keytext', $params)) {
      throw new Error\AddParam();
    }
    if (! $this->validator->isValidKeytext($params['keytext'])) {
      throw new Error\AddParam();
    }
    $this->keytext = trim($params['keytext']);
  }

  /**
   * @return string
   */
  public function getKeytext () {
    return $this->keytext;
  }
}
