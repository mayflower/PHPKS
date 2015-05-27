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
namespace Model\Validator;

class LookupParams extends AbstractParams {
  /**
   * @var int longest allowed search pattern
   */
  protected $maxSearchLength = 320;

  /**
   * @var int shortest allowed search pattern
   */
  protected $minSearchLength = 3;

  /**
   * valid values for the exact query parameter
   * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.2.3
   * @param array of strings
   */
  protected $validExactValues = array(
    'on',
    'off',
  );

  /**
   * valid values for the fingerprint query parameter
   * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.2.2
   * @param array of strings
   */
  protected $validFingerprintValues = array(
    'on',
    'off',
  );

  /**
   * valid values for the op variable
   * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.1.2
   * @param array of strings
   */
  protected $validOperations = array(
    'get',
    'index',
    'vindex'
  );

  /**
   * valid values for the options query parameter
   * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.2.1
   * @param array of strings
   */
  protected $validOptions = array(
    // machine readable
    'mr',
  );

  /**
   * is $value valid for fingerprint?
   * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.2.2
   * @param string $value
   * @return bool
   */
  public function isValidFingerprint ($value) {
    return in_array($value, $this->validFingerprintValues);
  }

  /**
   * is $value valid for exact?
   * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.2.3
   * @param string $value
   * @return bool
   */
  public function isValidExact ($value) {
    return in_array($value, $this->validExactValues);
  }

  /**
   * is $value valid for op?
   * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.1.2
   * @param string $value
   * @return bool
   */
  public function isValidOperation ($value) {
    return in_array($value, $this->validOperations);
  }

  /**
   * is $value an acceptable search pattern?
   * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.1.1
   * @param string $value
   * @return bool
   */
  public function isValidSearch ($value) {
    $len = strlen(trim($value));
    return ($len >= $this->minSearchLength and $len <= $this->maxSearchLength);
  }
}
