<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'csrf'], static function (RouteCollection $routes) {
    $routes->get('/', 'DashboardController::index');

    $routes->get('menus', 'MenuController::index');
    $routes->get('menus/create', 'MenuController::create');
    $routes->post('menus', 'MenuController::store');
    $routes->get('menus/(:num)/edit', 'MenuController::edit/$1');
    $routes->post('menus/(:num)', 'MenuController::update/$1');
    $routes->post('menus/(:num)/delete', 'MenuController::delete/$1');
});
