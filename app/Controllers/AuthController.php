<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function loginForm()
    {
        $this->render('auth/login', ['title' => 'Iniciar sessão']);
    }

    public function registerForm()
    {
        $this->render('auth/register', ['title' => 'Criar conta']);
    }

    public function register()
    {
        $name = $_POST['name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];
    }
}
