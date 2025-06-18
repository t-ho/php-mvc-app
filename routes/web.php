<?php

Router::get('', 'HomeController@index');
Router::get('about', 'HomeController@about');
Router::get('admin', 'AdminController@admin');
Router::get('contact', 'HomeController@contact');
Router::get('dashboard', 'AdminController@dashboard');
Router::get('user/login', 'UserController@showLoginForm');
Router::get('user/register', 'UserController@showRegisterForm');

Router::post('user/login', 'UserController@login');
Router::post('user/register', 'UserController@register');
Router::post('logout', 'UserController@logout');
