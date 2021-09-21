<?php

namespace App\Controllers;

//os recursos do miniframework


use App\Modells\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action
{
    public function autenticar()
    {
        $usuario = Container::getModel('Usuario');

        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', $_POST['senha']);
        $this->view->email = $_POST['email'];

        if ($_POST['email'] == "" || $_POST['senha'] == "") {
            $this->view->erroautenticar = true;
            $this->view->informacao = "*Necessário o preenchimento de todos os campos!";
            $this->render('index');
        } else {
            $usuario->autenticar();
            if ($usuario->__get('id') && $usuario->__get('nome')) {
                session_start();

                $_SESSION['id'] = $usuario->__get('id');
                $_SESSION['nome'] = $usuario->__get('nome');

                header('Location: /timeline');
            } else {
                $this->view->erroautenticar = true;
                $this->view->informacao = "*Dados inválidos. Não foi possível autenticar, senha ou e-mail incorretos!";
                $this->render('index');
            }
        }
    }

    public static function confirmarautenticacao()
    {
        $boolean = false;
        session_start();
        if (isset($_SESSION['id'])) {
            $boolean = true;
        }
        return $boolean;
    }

    public function sair()
    {
        session_start();
        session_destroy();
        $this->render('index');
    }
}
