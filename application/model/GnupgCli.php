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

class GnupgCli {
  /**
   * @var string directory where pubring.gpg
   */
  protected $keyringDir;

  /**
   * gpg exits with rc 2 when key to look up was not found, UNKNOWN PACKET :/
   * @var int gpg return code when nothing found
   */
  protected $rcKeyNotFound = 2;

  public function __construct () {
    $this->setKeyringDir(KEYRING_DIR);
  }

  /**
   * @param string directory where pubring.gpg, set in constructor, for tests
   */
  public function setKeyringDir ($path) {
    $this->keyringDir = $path;
  }

  /**
   * add an armored key
   *
   * @param string $keyText the submitted key(s)
   * @return string gnupg messages, 2 lines per key
   * @throws \Model\Error\Gnupg
   */
  public function addKey ($keyText) {
    // NOTE: the messages are sent to stderr
    $tempFile = tempnam(sys_get_temp_dir(), 'PKS');
    file_put_contents($tempFile, $keyText);
    $cmd = sprintf(
      "%s --homedir %s --import --no-permission-warning %s %s 2>&1",
      escapeshellarg(GPG_BINARY),
      escapeshellarg($this->keyringDir),
      '--utf8-strings --display-charset=utf-8',
      escapeshellarg($tempFile)
    );
    $output = array();
    $rc = null;

    exec($cmd, $output, $rc);
    unlink($tempFile);
    if ($rc != 0) {
      $msg = sprintf('error while adding key, rc=%s', $rc);
      throw new Error\Gnupg($msg);
    }

    return implode("\n", $output);
  }

  public function exportAll () {
    $cmd = sprintf(
      "%s --homedir %s --export --armor --no-permission-warning %s 2>/dev/null",
      escapeshellarg(GPG_BINARY),
      escapeshellarg($this->keyringDir),
      '--utf8-strings --display-charset=utf-8'
    );
    $output = array();
    $rc = null;

    exec($cmd, $output, $rc);
    if ($rc != 0 and $rc != $this->rcKeyNotFound) {
      $msg = sprintf('error while trying to export all keys, rc=%s', $rc);
      throw new Error\Gnupg($msg);
    }

    return implode("\n", $output);
  }

  /**
   * get one or more armored keys in one PGP PUBLIC KEY BLOCK
   *
   * @param LookupParams $lookupParams
   * @return string PGP PUBLIC KEY BLOCK
   * @throws \Model\Error\Gnupg
   */
  public function getArmoredKeys (LookupParams $lookupParams) {
    $cmd = $this->getExportArmoredKeyCmd($lookupParams->getSearch());
    $output = array();
    $rc = null;

    exec($cmd, $output, $rc);
    if ($rc != 0 and $rc != $this->rcKeyNotFound) {
      $msg = sprintf('error while trying to export key, rc=%s', $rc);
      throw new Error\Gnupg($msg);
    }

    return implode("\n", $output);
  }

  /**
   *
   * @param LookupParams $lookupParams
   * @return array
   * @throws \Model\Error\Gnupg
   */
  public function getIndexHumanReadable (LookupParams $lookupParams) {
    $data = $this->getIndexData($lookupParams);
    if (count($data) == 1) {
      // nothing found, only trust database information
      return array();
    }
    $parser = new Parser\GnupgColonListingHumanReadable();
    return $parser->parseIndex($data);
  }

  /**
   * get colon separated pub and uid lines, 7 bit suitable
   * @see http://tools.ietf.org/html/draft-shaw-openpgp-hkp-00#section-5.2
   * @param LookupParams $lookupParams
   * @return string
   * @throws \Model\Error\Gnupg
   */
  public function getIndexMachineReadable (LookupParams $lookupParams) {
    $data = $this->getIndexData($lookupParams);
    $parser = new Parser\GnupgColonListingMachineReadable();
    return $parser->parse($data);
  }

  /**
   *
   * @param LookupParams $lookupParams
   * @return string
   * @throws \Model\Error\Gnupg
   */
  protected function getIndexData (LookupParams $lookupParams) {
    $cmd = sprintf(
      '%s --homedir %s --list-keys --fingerprint '
        .'--fixed-list-mode --with-colons --no-permission-warning  '
        .'%s %s 2>/dev/null',
      escapeshellarg(GPG_BINARY),
      escapeshellarg($this->keyringDir),
      '--utf8-strings --display-charset=utf-8',
      escapeshellarg($lookupParams->getSearch())
    );
    $output = array();
    $rc = null;

    exec($cmd, $output, $rc);
    if ($rc != 0 and $rc != $this->rcKeyNotFound) {
      $msg = sprintf('error while running index query, rc=%s', $rc);
      throw new Error\Gnupg($msg);
    }
    return $output;
  }

  /**
   *
   * @param LookupParams $lookupParams
   * @return array
   * @throws \Model\Error\Gnupg
   */
  public function getVindexHumanReadable (LookupParams $lookupParams) {
    $data = $this->getVindexData($lookupParams);
    if (count($data) == 1) {
      // nothing found, only trust database information
      return array();
    }

    $parser = new Parser\GnupgColonListingHumanReadable();
    return $parser->parseVindex($data);
  }

  /**
   *
   * @param LookupParams $lookupParams
   * @return string
   * @throws \Model\Error\Gnupg
   */
  protected function getVindexData (LookupParams $lookupParams) {
    $cmd = sprintf(
      '%s --homedir %s --list-sigs --fingerprint '
        .'--fixed-list-mode --with-colons --no-permission-warning  '
        .'%s %s 2>/dev/null',
      escapeshellarg(GPG_BINARY),
      escapeshellarg($this->keyringDir),
      '--utf8-strings --display-charset=utf-8',
      escapeshellarg($lookupParams->getSearch())
    );
    $output = array();
    $rc = null;

    exec($cmd, $output, $rc);
    if ($rc != 0 and $rc != $this->rcKeyNotFound) {
      $msg = sprintf('error while running index query, rc=%s', $rc);
      throw new Error\Gnupg($msg);
    }

    return $output;
  }

  /**
   * remove a key by fingerprint
   *
   * @param RemoveParams $removeParams
   * @return bool false if no key found else true
   * @throws \Model\Error\Gnupg
   */
  public function removeKey (RemoveParams $removeParams) {
    $fingerprint = $removeParams->getFingerprint();
    if (! $this->doesKeyExist($fingerprint)) {
      return false;
    };

    // NOTE: removal of public key fails if there is a secret key for the key
    // NOTE: never have any secret keys on any keyserver
    $this->removeSecretKey($fingerprint);
    $this->removePublicKey($fingerprint);
    return true;
  }

  /**
   * @param string $fingerprint
   * @return bool
   * @throws \Model\Error\Gnupg
   */
  protected function doesKeyExist ($fingerprint) {
    $cmd = $this->getExportArmoredKeyCmd($fingerprint);
    $output = array();
    $rc = null;

    exec($cmd, $output, $rc);
    if ($rc != 0 and $rc != $this->rcKeyNotFound) {
      $msg = sprintf('error while looking up key, rc=%s', $rc);
      throw new Error\Gnupg($msg);
    }

    return (count($output) > 0);
  }

  /**
   * @param string $fingerprint
   * @throws \Model\Error\Gnupg
   */
  protected function removeSecretKey ($fingerprint) {
    $cmd = sprintf(
      "%s --homedir %s --batch --delete-secret-keys --no-permission-warning %s 2>/dev/null",
      escapeshellarg(GPG_BINARY),
      escapeshellarg(KEYRING_DIR),
      escapeshellarg($fingerprint)
    );
    $output = array();
    $rc = null;

    exec($cmd, $output, $rc);
    if ($rc != 0 and $rc != $this->rcKeyNotFound) {
      $msg = sprintf(
        'error while trying to delete secret key, rc=%s',
        $rc
      );
      throw new Error\Gnupg($msg);
    }
  }

  /**
   * @param string $fingerprint
   * @throws \Model\Error\Gnupg
   */
  protected function removePublicKey ($fingerprint) {
    $cmd = sprintf(
      "%s --homedir %s --batch --delete-key --no-permission-warning %s 2>/dev/null",
      escapeshellarg(GPG_BINARY),
      escapeshellarg(KEYRING_DIR),
      escapeshellarg($fingerprint)
    );
    $output = array();
    $rc = null;

    exec($cmd, $output, $rc);
    if ($rc != 0 and $rc != $this->rcKeyNotFound) {
      $msg = sprintf(
        'error while trying to delete public key, rc=%s',
        $rc
      );
      throw new Error\Gnupg($msg);
    }
  }

  /**
   * @param string $search
   * @return string
   */
  protected function getExportArmoredKeyCmd ($search) {
    return sprintf(
      "%s --homedir %s --export --armor --no-permission-warning %s %s 2>/dev/null",
      escapeshellarg(GPG_BINARY),
      escapeshellarg($this->keyringDir),
      '--utf8-strings --display-charset=utf-8',
      escapeshellarg($search)
    );
  }
}
