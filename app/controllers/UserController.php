<?php

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLoginForm()
    {
        render('user/login');
    }

    public function showRegisterForm()
    {
        render('user/register');
    }

    public function login()
    {
        $user = $this->userModel->authenticate($_POST['emailOrUsername'], $_POST['password']);

        if ($user) {
            $_SESSION['user'] = $user;
            $_SESSION['logged_in'] = true;

            redirect('/');
        } else {
            // Return to form with errors
            render('user/login', ['errors' => ['general' => 'Invalid username or password']]);
        }
    }

    public function register()
    {
        $result = $this->userModel->create($_POST);

        if ($result['success']) {
            redirect('/', ['message' => 'Registration successful! Please log in.']);
        } else {
            // Return to form with errors
            render('user/register', ['errors' => $result['errors']]);
        }
    }
}
