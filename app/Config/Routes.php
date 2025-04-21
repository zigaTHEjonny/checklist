<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Checklist::index/1', ['filter' => 'auth']);

$routes->match(['get', 'post'], '/register', 'Users::register', ['filter' => 'noauth']);
$routes->match(['get', 'post'], '/login', 'Users::login', ['filter' => 'noauth']); 
$routes->post('/logout', 'Users::logout');

$routes->post('/update_item', 'Checklist::updateState', ['filter' => 'auth']);
$routes->post('/remove_item', 'Checklist::delete', ['filter' => 'auth']);
$routes->post('/remove_all_items', 'Checklist::delete_all/1', ['filter' => 'auth']);
$routes->post('/add_item', 'Checklist::add', ['filter' => 'auth']);

$routes->get('/export', 'Checklist::export/1', ['filter' => 'auth']);
$routes->post('/import', 'Checklist::import/1', ['filter' => 'auth']);


