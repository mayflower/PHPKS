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
namespace View;

class Formatter {
  public function escape ($text) {
    return htmlentities($text, ENT_HTML5 || ENT_SUBSTITUTE, 'UTF-8');
  }

  /**
   * format a fingerprint in groups of 4 chars separated by spaces
   * @param string $fingerprint
   * @return string
   */
  public function formatFingerprint ($fingerprint) {
    return sprintf(
      '%s %s %s %s %s  %s %s %s %s %s',
      substr($fingerprint, 0, 4),
      substr($fingerprint, 4, 4),
      substr($fingerprint, 8, 4),
      substr($fingerprint, 12, 4),
      substr($fingerprint, 16, 4),
      substr($fingerprint, 20, 4),
      substr($fingerprint, 24, 4),
      substr($fingerprint, 28, 4),
      substr($fingerprint, 32, 4),
      substr($fingerprint, 36, 4)
    );
  }

  /**
   * format unixtimestamp as YYYY-mm-dd, if empty return 10 underscores
   * @param string $tst
   * @return string
   */
  public function formatTst ($tst) {
    if ($tst == "") {
      return "__________";
    }
    else {
      return date("Y-m-d", intval($tst));
    }
  }

  /**
   * return a html link to get the keys with the $keyId and $text to display
   * @param string $path
   * @param string $keyId
   * @param string $text
   * @return string
   */
  public function formatGetLink ($path, $keyId, $text) {
    return sprintf(
        "<a href='%s/lookup?op=get&search=0x%s'>%s</a>",
        $path,
        $keyId,
        htmlentities($text, ENT_HTML5)
      );
  }

  /**
   * return a html link to get the keys with the $keyId, display short keyId
   * @param string $keyId
   * @return string
   */
  public function formatKeyIdLink ($path, $keyId) {
    return $this->formatGetLink($path, "$keyId", substr($keyId, -8));
  }
}
