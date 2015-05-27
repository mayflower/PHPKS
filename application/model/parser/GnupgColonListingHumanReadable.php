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

class GnupgColonListingHumanReadable {
  /**
   *  @var int index of field in colon separated gnupg listing
   */
  protected $recordTypeLineField = 0;

  /**
   *  @var array of ints, index of field in colon separated gnupg listing
   */
  protected $fprLineFields = array(
    'fingerprint'   => 9
  );

  /**
   *  @var array of ints, index of field in colon separated gnupg listing
   */
  protected $pubLineFields = array(
    'bits'          => 2,
    'creationTst'   => 5,
    'expirationTst' => 6,
    'keyId'         => 4,
  );

  /**
   *  @var array of ints, index of field in colon separated gnupg listing
   */
  protected $sigLineFields = array(
    'creationTst'   => 5,
    'expirationTst' => 6,
    'keyId'         => 4,
    'userId'        => 9
  );

  /**
   *  @var array of ints, index of field in colon separated gnupg listing
   */
  protected $uidLineFields = array(
    'creationTst'   => 5,
    'expirationTst' => 6,
    'userId'        => 9
  );

  /**
   *  parser for index listings
   *  @param array $lines list of lines from gnupg
   *  @return array
   */
  public function parseIndex (array $lines) {
    $parsed = array();

    $tokenSections = $this->buildSections($lines);
    foreach ($tokenSections as $tokenSection) {
      $sectionRecords = array($this->buildPubRecord($tokenSection));

      // primary uid is contained within pub record
      $isPrimaryUid = true;
      foreach ($tokenSection as $tokenLine) {
        $recordType = $tokenLine[$this->recordTypeLineField];
        if ($recordType == 'fpr') {
          $sectionRecords[] = $this->buildFprRecord($tokenLine);
        }
        elseif ($recordType == 'uid') {
          if ($isPrimaryUid) {
            $isPrimaryUid = false;
          }
          else {
            $sectionRecords[] = $this->buildUidRecord($tokenLine);
          }
        }
      }

      $parsed[] = $sectionRecords;
    }
    return $parsed;
  }

  /**
   *  parser for verbose index listings
   *  @param array $lines list of lines from gnupg
   *  @return array
   */
  public function parseVindex (array $lines) {
    $parsed = array();

    $tokenSections = $this->buildSections($lines);
    foreach ($tokenSections as $tokenSection) {
      $sectionRecords = array($this->buildPubRecord($tokenSection));

      foreach ($tokenSection as $tokenLine) {
        $recordType = $tokenLine[$this->recordTypeLineField];
        if ($recordType == 'fpr') {
          $sectionRecords[] = $this->buildFprRecord($tokenLine);
        }
        elseif ($recordType == 'sig') {
          $sectionRecords[] = $this->buildSigRecord($tokenLine);
        }
        elseif ($recordType == 'uid') {
          $sectionRecords[] = $this->buildUidRecord($tokenLine);
        }
      }

      $parsed[] = $sectionRecords;
    }
    return $parsed;
  }

  /**
   *  provide the lines grouped in sections, one per key
   *  @param array $lines list of lines from gnupg
   *  @return array
   */
  public function buildSections (array $lines) {
    $sections = array();
    $currentSection = array();

    $tokens = $this-> tokenizeLines($lines);
    foreach ($tokens as $lineTokens) {
      $recordType = $lineTokens[$this->recordTypeLineField];
      if ($recordType == 'pub') {
        $sections = $this->addSection($currentSection, $sections);
        $currentSection = array();
      }
      $currentSection[] = $lineTokens;
    }
    $sections = $this->addSection($currentSection, $sections);

    return $sections;
  }

  protected function addSection (array $section, array $sections) {
    if ($section != array()) {
      $sections[] = $section;
    }
    return $sections;
  }

  /**
   *  @param array $lines list of lines from gnupg
   *  @return array
   */
  protected function tokenizeLines (array $lines) {
    $tokens = array();

    foreach ($lines as $line) {
      $lineTokens = explode(':', $line);
      if ($lineTokens == array()) {
        continue;
      }

      $recordType = $lineTokens[$this->recordTypeLineField];
      if (! in_array($recordType, array('fpr', 'pub', 'uid', 'sig'))) {
        continue;
      }

      $tokens[] = $lineTokens;
    }
    return $tokens;
  }

  /**
   *  data for fingerprint line
   *  @param array $lineTokens
   *  @return array
   */
  public function buildFprRecord (array $lineTokens) {
    $record = array(
      'recordType'    => 'fpr',
      'fingerprint'   => $lineTokens[$this->fprLineFields['fingerprint']],
    );
    return $record;
  }

  /**
   *  data for pub line
   *  @param array $lineTokens
   *  @return array
   */
  public function buildPubRecord (array $section) {
    $record = array(
      'recordType'    => 'pub',
      'bits'          => '',
      'creationTst'   => '',
      'expirationTst' => '',
      'keyId'         => '',
      'userId'        => '',
    );

    $pubDone = false;
    $uidDone = false;
    foreach ($section as $lineTokens) {
      $recordType = $lineTokens[$this->recordTypeLineField];

      if (! $pubDone and $recordType == 'pub') {
        $record['bits'] = $lineTokens[$this->pubLineFields['bits']];
        $record['creationTst'] =
          $lineTokens[$this->pubLineFields['creationTst']];
        $record['expirationTst'] =
          $lineTokens[$this->pubLineFields['expirationTst']];
        $record['keyId'] = $lineTokens[$this->pubLineFields['keyId']];
        $pubDone = true;
      }

      if (! $uidDone and $recordType == 'uid') {
        $record['userId'] = $lineTokens[$this->uidLineFields['userId']];
        $uidDone = true;
      }

      if ($pubDone and $uidDone) {
        break;
      }
    }

    return $record;
  }

  /**
   *  data for signature line
   *  @param array $lineTokens
   *  @return array
   */
  public function buildSigRecord (array $lineTokens) {
    return array(
      'recordType'    => 'sig',
      'creationTst'   =>
        $lineTokens[$this->sigLineFields['creationTst']],
      'expirationTst' => $lineTokens[$this->sigLineFields['expirationTst']],
      'keyId'         => $lineTokens[$this->sigLineFields['keyId']],
      'userId'        => $lineTokens[$this->sigLineFields['userId']],
    );
  }

  /**
   *  data for uid line
   *  @param array $lineTokens
   *  @return array
   */
  public function buildUidRecord (array $lineTokens) {
    return array(
      'recordType'    => 'uid',
      'creationTst'   =>
        $lineTokens[$this->uidLineFields['creationTst']],
      'expirationTst' => $lineTokens[$this->uidLineFields['expirationTst']],
      'userId'        => $lineTokens[$this->uidLineFields['userId']],
    );
  }
}
