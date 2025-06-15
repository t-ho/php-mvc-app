<?php

class HomeController
{
    public function index()
    {
        $data = [
          'title' => 'Home Page',
          'message' => 'Welcome to the Home Page!'
        ];

        render('home/index', $data, 'layouts/hero-layout');
    }

    public function about()
    {
        $data = [
          'title' => 'About Us',
          'message' => 'Learn more about us on this page.'
        ];

        render('home/about', $data);
    }
}
