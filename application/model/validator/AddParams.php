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

class AddParams extends AbstractParams {
  /**
   * valid values for the options query parameter
   * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-3.2.1
   * @param array of strings
   */
  protected $validOptions = array(
    // no modification
    'nm',
  );

  /**
   * rudimentary test if $keytext looks valid
   * @param string $keytext
   * @return bool
   */
  public function isValidKeytext ($keytext) {
    $k = trim($keytext);
    $beginRegex = '/^-----BEGIN PGP PUBLIC KEY BLOCK-----/';
    $endRegex = '/-----END PGP PUBLIC KEY BLOCK-----$/';
    return (
      preg_match($beginRegex, $k) == 1
      and preg_match($endRegex, $k) == 1
    );
  }
}
