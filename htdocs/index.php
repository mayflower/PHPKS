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
require '../application/config.php';
require '../vendor/autoload.php';

$app = new \Slim\Slim(array(
  'debug'           => (APPLICATION_MODE != 'PRODUCTION'),
  'templates.path'  => '../application/templates',
  'log.enabled'     => true,
));

$app->map('/', function () use ($app) {
  $controller = new Controller\Index($app);
  $controller->indexAction();
})->via('GET', 'HEAD')->name('index');

if (ADMIN_MODE_AVAILABLE) {
  $app->group('/admin', function () use ($app, $logger) {
    $app->map('/+add/*', function () use ($app) {
      $app->redirect('/admin', 301);
    })->via('GET', 'HEAD');

    $app->map('/+remove/*', function () use ($app) {
      $app->redirect('/admin', 301);
    })->via('GET', 'HEAD');

    $controller = new Controller\Admin($app);

    $app->map('/*', function () use ($controller) {
      $controller->indexAction();
    })->via('GET', 'HEAD')->name('admin');

    $app->post('/+add/*', function () use ($controller) {
      $controller->addAction();
    });

    $app->get('/+export-all/*', function () use ($controller) {
      $controller->exportAllAction();
    });

    $app->get('/+lookup/*', function () use ($controller) {
      // lookupAction sends a redirect to /admin when no params
      $controller->lookupAction();
    });

    $app->post('/+remove/*', function () use ($controller) {
      $controller->removeAction();
    });
  });
}

$app->group('/+pks', function () use ($app) {
  $app->map('/*', function () use ($app) {
    $app->redirect('/', 301);
  })->via('GET', 'HEAD');

  $app->map('/+add/*', function () use ($app) {
    $app->redirect('/', 301);
  })->via('GET', 'HEAD');

  $controller = new Controller\Pks($app);

  $app->post('/+add/*', function () use ($controller) {
    $controller->addAction();
  });

  $app->get('/+lookup/*', function () use ($controller) {
    // lookupAction sends a redirect to / when no params
    $controller->lookupAction();
  });
});
/*
$app->map('.*', function () use ($app) {
  $controller = new Controller\Index($app);
  $controller->setBadRequestResponse();
})->via('DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT', 'TRACE');
*/
$app->run();
