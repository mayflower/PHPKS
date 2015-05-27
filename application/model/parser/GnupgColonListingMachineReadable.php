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
namespace Model\Parser;

/**
 * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-5.2
 */
class GnupgColonListingMachineReadable {
  /**
   *  @var int version in machine readable output
   */
  protected $version = 1;

  /**
   *  @var int index of field in colon separated gnupg listing
   */
  protected $recordTypeLineField = 0;

  /**
   *  @var array of ints, index of field in colon separated gnupg listing
   */
  protected $pubLineFields = array(
    'algo'           => 3,
    'creationdate'   => 5,
    'expirationdate' => 6,
    'flags'          => 1,
    'keyid'          => 4,
    'keylen'         => 2
  );

  /**
   *  @var array of ints, index of field in colon separated gnupg listing
   */
  protected $uidLineFields = array(
    'creationdate'   => 5,
    'expirationdate' => 6,
    'flags'          => 1,
    'uid'            => 9
  );

  /**
   *  parse gnupg output into machine readable format
   *  @param array $lines list of lines from gnupg
   *  @return string
   */
  public function parse (array $lines) {
    $parsed = array();
    $count = 0;

    foreach ($lines as $line) {
      $tokenLine = explode(':', $line);
      $recordType = $tokenLine[$this->recordTypeLineField];

      if ($recordType == 'pub') {
        $parsed[] = $this->buildPubLine($tokenLine);
        ++ $count;
      }

      if ($recordType == 'uid') {
        $parsed[] = $this->buildUidLine($tokenLine);
      }
    }

    if ($count == 0) {
      return '';
    }

    array_unshift($parsed, sprintf('info:%s:%s', $this->version, $count));
    return implode("\n", $parsed);
  }

  /**
   *  data for pub line
   *  @param array $tokenLine
   *  @return string
   */
  public function buildPubLine (array $tokenLine) {
    return sprintf(
      'pub:%s:%s:%s:%s:%s:%s',
      $tokenLine[$this->pubLineFields['keyid']],
      $tokenLine[$this->pubLineFields['algo']],
      $tokenLine[$this->pubLineFields['keylen']],
      $tokenLine[$this->pubLineFields['creationdate']],
      $tokenLine[$this->pubLineFields['expirationdate']],
      $this->getFlags($tokenLine[$this->pubLineFields['flags']])
    );
  }

  /**
   *  data for uid line
   *  @param array $tokenLine
   *  @return string
   */
  public function buildUidLine (array $tokenLine) {
    return sprintf(
      'uid:%s:%s:%s:%s',
      rawurlencode($tokenLine[$this->uidLineFields['uid']]),
      $tokenLine[$this->uidLineFields['creationdate']],
      $tokenLine[$this->uidLineFields['expirationdate']],
      $this->getFlags($tokenLine[$this->uidLineFields['flags']])
    );
  }

  /**
   *  @param string $flagString
   *  @return string
   */
  protected function getFlags ($flagString) {
    $flags = '';
    $flagsList = preg_split('//', $flagString);
    foreach(array('d', 'e', 'r') as $f) {
      if (in_array($f, $flagsList)) {
        $flags .= $f;
      }
    }
    return $flags;
  }
}
