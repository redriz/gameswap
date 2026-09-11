<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function loginForm()
    {
        $this->render('auth/login', ['title' => 'Iniciar sessão']);
    }
}
