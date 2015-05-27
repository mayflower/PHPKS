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

class Admin extends Pks {
  /**
   * @var bool
   */
  protected $isAdminMode = true;

  /**
   * set \Slim\Slim->response
   */
  public function exportAllAction () {
    try {
      $keys = $this->model->exportAll();
    }
    catch (\Model\Error\Gnupg $e) {
      $msg = $e->getMessage() . "\n";
      $this->setInternalServerErrorResponse($msg);
      return;
    }
    catch (\Exception $e) {
      $this->setInternalServerErrorResponse($e->getMessage());
      return;
    }

    if ($keys == '') {
      $this->setNoKeysFoundResponse();
      return;
    }
    $this->setExportAllResponse($keys);
  }

  /**
   * set \Slim\Slim->response for /pks/lookup GET requests
   */
  public function lookupAction () {
    if ($this->noGetParamsProvided()) {
      $this->app->redirect('/admin', 301);
      return;
    }

    parent::lookupAction();
  }

  /**
   * set \Slim\Slim->response
   */
  public function removeAction () {
    try {
      $params = new \Model\RemoveParams();
      $params->setFingerprint($this->app->request->params());
    }
    catch (\Model\Error\RemoveParam $e) {
      $msg = "invalid/missing fingerprint param";
      $this->setBadRequestResponse($msg);
      return;
    }
    catch (\Exception $e) {
      $this->setInternalServerErrorResponse($e->getMessage());
      return;
    }

    try {
      $result = $this->model->removeKey($params);
      $this->setKeyRemovedResponse($result);
    }
    catch (\Model\Error\Gnupg $e) {
      $msg = $e->getMessage() . "\n";
      $this->setInternalServerErrorResponse($msg);
      return;
    }
    catch (\Exception $e) {
      $this->setInternalServerErrorResponse($e->getMessage());
      return;
    }
  }

  /**
   * @param string $keys several lines of gnupg output
   */
  protected function setExportAllResponse ($keys) {
    $this->app->response->headers->set('Content-Type', 'text/plain');
    $this->app->response->headers->set('Content-Length', strlen($keys));
    $this->app->response->headers->set(
      'Content-Disposition', 'attachment; filename="all-keys.asc"'
    );
    $this->app->response->setBody($keys);
  }

  /**
   * @param string $result several lines of gnupg output
   */
  protected function setKeyRemovedResponse ($result) {
    $data = array(
      'keyWasRemovedMsg'  => $result
        ? 'The key was removed'
        : 'The key to remove did not exist',
    );
    $this->render('index.phtml', $data);
  }
}
