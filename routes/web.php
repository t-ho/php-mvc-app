<?php

$routes = [
  'GET' => [
    '' => 'HomeController@index',
    'about' => 'HomeController@about',
    'user/login' => 'UserController@showLoginForm',
    'user/register' => 'UserController@showRegisterForm',
  ],
  'POST' => [
    'user/login' => 'UserController@login',
    'user/register' => 'UserController@register',
  ]
];
