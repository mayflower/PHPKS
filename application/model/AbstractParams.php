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

class AbstractParams {
  /**
   * @var \Model\Validator\*Params
   */
  protected $validator;

  /**
   * @var array
   */
  protected $options;

  /**
   * sanitize and set options
   * @param array $_GET or equivalent
   */
  public function setOptions (array $params) {
    $this->options = array();
    if (! array_key_exists('options', $params)) {
      return;
    }

    $options = explode(',', $params['options']);
    foreach ($options as $option) {
      if ($this->validator->isValidOption($option)
          and ! in_array($option, $this->options)
      ) {
        $this->options[] = $option;
      }
    }
  }

  /**
   * @return array of strings
   */
  public function getOptions () {
    return $this->options;
  }
}
