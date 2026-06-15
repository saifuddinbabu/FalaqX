<?php

/**
 * FalaqX - Route Definitions
 *
 * Format:  'METHOD /uri'  =>  'ControllerClass@method'
 *
 * Supported methods: GET, POST, PUT, PATCH, DELETE
 * Use {param} for URL parameters — they are passed to the method in order.
 *
 * Examples:
 *   'GET /users/{id}'          => 'UserController@show'
 *   'POST /users'              => 'UserController@store'
 *   'GET /blog/{year}/{slug}'  => 'BlogController@post'
 *
 * Plain paths without a method prefix match ANY HTTP method.
 */

$routes = [

    // ── Home ──────────────────────────────────────────────────────────────────
    'GET /'            => 'HomeController@index',
    'GET /about'       => 'HomeController@about',
    'GET /contact'     => 'HomeController@contact',
    'POST /contact'    => 'HomeController@contactSubmit',

    // ── Example: Users ────────────────────────────────────────────────────────
    'GET /users'          => 'UserController@index',
    'GET /users/{id}'     => 'UserController@show',
    'GET /users/create'   => 'UserController@create',
    'POST /users'         => 'UserController@store',
    'GET /users/{id}/edit'=> 'UserController@edit',
    'POST /users/{id}'    => 'UserController@update',
    'POST /users/{id}/delete' => 'UserController@destroy',

];
