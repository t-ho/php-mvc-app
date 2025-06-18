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

    public function contact()
    {
        $data = [
          'title' => 'Contact Page',
          'message' => 'Learn more about us on this page.'
        ];

        render('home/contact', $data);
    }
}
