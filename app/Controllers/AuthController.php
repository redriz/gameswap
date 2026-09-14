<?php

namespace App\Controllers;

use App\Models\User;

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

        if (empty($name) || empty($username) || empty($email) || empty($password) || empty($passwordConfirmation)) {
            $this->render('auth/register', ['title' => 'Criar conta', 'error' => 'Deve preencher todos os campos']);
            return;
        }

        if ($password !== $passwordConfirmation) {
            $this->render('auth/register', ['title' => 'Criar conta', 'error' => 'As palavras-passes não coincidem']);
            return;
        }

        if (strlen($password) < 8) {
            $this->render('auth/register', ['title' => 'Criar conta', 'error' => 'Palavra-passe deve ter no mínimo 8 caracteres']);
            return;
        }

        if (User::emailOrUsernameExists($email, $username)) {
            $this->render('auth/register', ['title' => 'Criar conta', 'error' => 'Username ou Email já está em uso']);
            return;
        }

        $id = User::create($name, $username, $email, $password);
        echo "Usuário crido com sucesso! ID: " . $id;
    }
}
