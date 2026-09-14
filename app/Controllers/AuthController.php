<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends BaseController
{
    public function loginForm()
    {
        $this->requireGuest();
        $this->render('auth/login', ['title' => 'Iniciar sessão']);
    }

    public function registerForm()
    {
        $this->requireGuest();
        $this->render('auth/register', ['title' => 'Criar conta']);
    }

    public function register()
    {
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $gender = $_POST['gender'];
        $birthDate = $_POST['birth_date'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];

        if (empty($firstName) || empty($lastName) || empty($gender) || empty($birthDate) || empty($username) || empty($email) || empty($password) || empty($passwordConfirmation)) {
            $this->render('auth/register', [
                'title' => 'Criar conta',
                'error' => 'Deve preencher todos os campos',
                'old' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'email' => $email,
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                ]
            ]);
            return;
        }

        $idade = (new \DateTime())->diff(new \DateTime($birthDate))->y;
        if ($idade < 13) {
            $this->render('auth/register', [
                'title' => 'Criar conta',
                'error' => 'É necessário ter pelo menos 13 anos para criar uma conta',
                'old' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'email' => $email,
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                ]
            ]);
            return;
        }

        if ($password !== $passwordConfirmation) {
            $this->render('auth/register', [
                'title' => 'Criar conta',
                'error' => 'As palavras-passes não coincidem',
                'old' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'email' => $email,
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                ]
            ]);
            return;
        }

        if (strlen($password) < 8) {
            $this->render('auth/register', [
                'title' => 'Criar conta',
                'error' => 'Palavra-passe deve ter no mínimo 8 caracteres',
                'old' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'email' => $email,
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                ]
            ]);
            return;
        }

        if (User::emailOrUsernameExists($email, $username)) {
            $this->render('auth/register', [
                'title' => 'Criar conta',
                'error' => 'Username ou Email já está em uso',
                'old' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'email' => $email,
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                ]
            ]);
            return;
        }

        $id = User::create($firstName, $lastName, $username, $email, $password, $gender, $birthDate);
        echo "Usuário crido com sucesso! ID: " . $id;
    }

    public function login()
    {
        $identifier = $_POST['identifier'];
        $password = $_POST['password'];

        if (empty($identifier) || empty($password)) {
            $this->render('auth/login', ['title' => 'Iniciar sessão', 'error' => 'Preenche todos os campos']);
            return;
        }

        $user = User::findByEmailOrUsername($identifier);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->render('auth/login', ['title' => 'Iniciar sessão', 'error' => 'Credenciais inválidas']);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        echo "Login bem-sucedido! Bem-vindo, " . $user['first_name'];
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
