<?php

namespace App\Controllers;

class GameController extends BaseController
{
    public function list()
    {
        $this->render('games/list', ['title' => 'Jogos disponíveis - Início']);
    }
}
