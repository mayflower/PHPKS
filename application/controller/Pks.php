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
namespace Controller;

class Pks extends Index {
  /**
   * @var \Model\GnupgCli
   */
  protected $model;

  /**
   * @var string path to directory where the keyring is
   */
  protected $keyringDir;

  /**
   * @param \Slim\Slim $app slim application
   */
  public function __construct ($app) {
    parent::__construct($app);
    $this->model = new \Model\GnupgCli();
    $this->setKeyringDir(KEYRING_DIR);
  }

  /**
   * for testing
   * @param string path to directory where the keyring is
   */
  public function setKeyringDir ($path) {
    $this->model->setKeyringDir($path);
  }

  /**
   * set \Slim\Slim->response for /pks/add POST requests
   */
  public function addAction () {
    try {
      $addParams = new \Model\AddParams();
      $addParams->setKeytext($this->app->request->post());
    }
    catch (\Model\Error\AddParam $e) {
      $msg = "invalid/missing keytext param";
      $this->setBadRequestResponse($msg);
      return;
    }
    catch (\Exception $e) {
      $this->setInternalServerErrorResponse();
      return;
    }

    try {
      $result = $this->model->addKey($addParams->getKeyText());
      $this->setKeyAddedResponse($result);
    }
    catch (\Model\Error\Gnupg $e) {
      $msg = $e->getMessage() . "\n";
      $this->setInternalServerErrorResponse($msg);
      return;
    }
    catch (\Exception $e) {
      $this->setInternalServerErrorResponse();
      return;
    }
  }

  /**
   * @param string $result several lines of gnupg output
   */
  protected function setKeyAddedResponse ($result) {
    $data = array(
      'keysAddedResponse' => $result,
    );
    $this->render('index.phtml', $data);
  }

  /**
   * set \Slim\Slim->response for /pks/lookup GET requests
   */
  public function lookupAction () {
    if ($this->noGetParamsProvided()) {
      $this->app->redirect('/', 301);
      return;
    }

    $lookupParams = new \Model\LookupParams();

    try {
      $lookupParams->setAll($this->app->request->get());
    }
    catch (\Model\Error\OperationParam $e) {
      $msg = "invalid/missing op param";
      $this->setBadRequestResponse($msg);
      return;
    }
    catch (\Model\Error\SearchParam $e) {
      $msg = "invalid/missing search param";
      $this->setBadRequestResponse($msg);
      return;
    }
    catch (\Exception $e) {
      $this->setInternalServerErrorResponse();
      return;
    }

    $operation = $lookupParams->getOperation();
    $isMachineReadable = in_array('mr', $lookupParams->getOptions());

    try {
      if ($operation == 'get' and $isMachineReadable) {
        $keys = $this->model->getArmoredKeys($lookupParams);
        $this->setMachineReadableResponse($keys);
      }
      elseif ($operation == 'get') {
        $keys = $this->model->getArmoredKeys($lookupParams);
        $this->setArmoredKeysResponse($keys, $lookupParams);
      }
      elseif ($operation == 'index' and $isMachineReadable) {
        $keys = $this->model->getIndexMachineReadable($lookupParams);
        $this->setMachineReadableResponse($keys);
      }
      elseif ($operation == 'index') {
        $keys = $this->model->getIndexHumanReadable($lookupParams);
        $this->setIndexResponse($keys, $lookupParams);
      }
      elseif ($operation == 'vindex' and $isMachineReadable) {
        $keys = $this->model->getIndexMachineReadable($lookupParams);
        $this->setMachineReadableResponse($keys);
      }
      elseif ($operation == 'vindex') {
        $keys = $this->model->getVindexHumanReadable($lookupParams);
        $this->setVindexResponse($keys, $lookupParams);
      }
    }
    catch (\Model\Error\Gnupg $e) {
      $msg = $e->getMessage() . "\n";
      $this->setInternalServerErrorResponse($msg);
      return;
    }
    catch (\Exception $e) {
      $this->setInternalServerErrorResponse();
      return;
    }
  }

  /**
   * @param string $keys
   * @param \Model\LookupParams $lookupParams
   */
  protected function setArmoredKeysResponse (
    $keys, \Model\LookupParams $lookupParams
  ) {
    if ($keys == '') {
      $this->setNoKeysFoundResponse();
      return;
    }

    $data = array(
      'keys'      => $keys,
      'title'     => sprintf(
        '%s -- GET "%s"', APPLICATION_TITLE, $lookupParams->getSearch()
      ),
      'useStyleSheet' => true,
      'search' => $lookupParams->getSearch(),
    );
    $this->render('pks-armoredKeys.phtml', $data);
  }

  /**
   / @param array $keys
   * @param \Model\LookupParams $lookupParams
   */
  protected function setIndexResponse (
    array $keys, \Model\LookupParams $lookupParams
  ) {
    if ($keys == array()) {
      $this->setNoKeysFoundResponse();
      return;
    }

    $data = array(
      'keys'            => $keys,
      'showFingerprint' => ($lookupParams->getFingerprint() == 'on'),
      'title'           => sprintf(
        '%s -- INDEX "%s"', APPLICATION_TITLE, $lookupParams->getSearch()
      ),
      'useStyleSheet'   => true,
      'search' => $lookupParams->getSearch(),
      'operation' => 'index',
    );
    $this->render('pks-indexList.phtml', $data);
  }

  /**
   * plain text response
   * @param string $keys several lines of gnupg output
   */
  protected function setMachineReadableResponse ($keys) {
    if ($keys == '') {
      $this->setNoKeysFoundResponse();
      return;
    }

    $this->app->response->headers->set('Content-Type', 'text/plain');

    $warning = '';
    if (APPLICATION_MODE != 'PRODUCTION') {
      $warning = "TESTING MODE, @see application/config.php, @see README\n\n";
    }

    $this->app->response->setBody($warning.$keys);
  }

  /**
   * @param array $keys
   * @param \Model\LookupParams $lookupParams
   */
  protected function setVindexResponse (
    array $keys, \Model\LookupParams $lookupParams
  ) {
    if ($keys == array()) {
      $this->setNoKeysFoundResponse();
      return;
    }

    $data = array(
      'keys'            => $keys,
      'showFingerprint' => ($lookupParams->getFingerprint() == 'on'),
      'title'           => sprintf(
        '%s -- VERBOSE INDEX "%s"',
        APPLICATION_TITLE,
        $lookupParams->getSearch()
      ),
      'useStyleSheet'   => true,
      'search' => $lookupParams->getSearch(),
      'operation' => 'vindex',
    );
    $this->render('pks-vindexList.phtml', $data);
  }

  /**
   * 404 response
   */
  protected function setNoKeysFoundResponse () {
    $data = array(
      'title' => sprintf('%s -- No results found', APPLICATION_TITLE),
    );
    $this->render('pks-noKeysFound.phtml', $data, 404);
  }
}
