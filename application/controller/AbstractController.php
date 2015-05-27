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

class AbstractController {
  /**
   * @var \Slim\Slim
   */
  protected $app;

  /**
   * @var \View\Formatter
   */
  protected $formatter;

  /**
   * @var bool
   */
  protected $isAdminMode = false;

  public function __construct ($app) {
    $this->app = $app;
    $this->formatter = new \View\Formatter();
  }

  protected function logException (\Exception $Exception) {
  }

  protected function noGetParamsProvided () {
    return $this->app->request->get() == array();
  }

  protected function render ($view, array $data=array(), $responseCode=200) {
    $defaultData = array(
      'controllerPath'  => $this->isAdminMode ? '/admin' : '/pks',
      'formatter'       => $this->formatter,
      'isAdminMode'     => $this->isAdminMode,
      'title'           => APPLICATION_TITLE,
      'useStyleSheet'   => true,
    );
    $this->app->render($view, array_merge($defaultData, $data), $responseCode);
  }

  public function setBadRequestResponse ($message='') {
    $data = array(
      'message'         => $message,
      'title'           => sprintf('%s -- 400 Bad Request', APPLICATION_TITLE),
    );
    $this->render('error.phtml', $data, 400);
  }

  public function setNotFoundResponse ($message='') {
    $data = array(
      'message'         => $message,
      'title'           => sprintf('%s -- 404 Not Found', APPLICATION_TITLE),
    );
    $this->render('error.phtml', $data, 404);
  }

  public function setInternalServerErrorResponse ($message='') {
    $data = array(
      'message'   => $message,
      'title'     => sprintf(
        '%s -- 500 Internal Server Error', APPLICATION_TITLE
      ),
    );
    $this->render('error.phtml', $data, 500);
  }
}