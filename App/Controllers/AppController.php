<?php

namespace App\Controllers;

//os recursos do miniframework


use App\Modells\Usuario;
use App\Controllers\AuthController;
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action
{
    public function timeline()
    {
        if (AuthController::confirmarautenticacao()) {
            $tweet = Container::getModel('Tweet');
            $usuario = Container::getModel('Usuario');
            $totaldados = $usuario->getTotal();

            $pagina = isset($_GET['pagina'])? $_GET['pagina'] : '1';
            $total_registros_pagina = 10;
            $deslocamento = ($pagina - 1) * $total_registros_pagina;

            //$tweets = $tweet->getAll();
            $tweets = $tweet->getPorPagina($total_registros_pagina, $deslocamento);
            
            $totaltweets = $tweet->getTotalTweets();

            $this->view->tweets = $tweets;
            $this->view->totaldados = $totaldados;
            $this->view->totalpagina = ceil($totaltweets['total'] / $total_registros_pagina);
            $this->render('timeline');
        } else {
            $this->erroauth();
        }
    }

    public function tweet()
    {
        if (AuthController::confirmarautenticacao()) {

            if (isset($_POST['tweet'])) {
                $tweet = Container::getModel('Tweet');
                $tweet->__set('tweet', $_POST['tweet']);
                $tweet->__set('id_usuario', $_SESSION['id']);
                $tweet->salvar();
            }
            header('Location: /timeline');
        } else {
            $this->erroauth();
        }
    }

    public function quemSeguir()
    {
        if (AuthController::confirmarautenticacao()) {
            $usuario = Container::getModel('Usuario');            
            $totaldados = $usuario->getTotal();

            if (isset($_GET['pesquisarPor']) && ($_GET['pesquisarPor'] != '')) {
                
                $usuario->__set('nome', $_GET['pesquisarPor']);
                $usuarios = $usuario->getAll();

                $this->view->usuarios = $usuarios;
            }
            
            $this->view->totaldados = $totaldados;
            $this->render('quemSeguir');
        } else {
            $this->erroauth();
        }
    }

    public function acao()
    {
        if (AuthController::confirmarautenticacao()) {

            if (isset($_GET['acao']) && ($_GET['acao'] != '')) {
                $usuario = Container::getModel('Usuario');
                $usuario->__set('id_usuario', $_GET['id_usuario']);

                if ($_GET['acao'] == "seguir") {
                    $usuario->seguirUsuario();
                } else if ($_GET['acao'] == "deixar_de_seguir") {
                    $usuario->deixarSeguirUsuario();
                }
            }
            header('Location: /quemSeguir');
            
        } else {
            $this->erroauth();
        }        
    }

    public function removertweet(){
        if (AuthController::confirmarautenticacao()) {
            if(isset($_POST['id'])){
                $tweet = Container::getModel('Tweet');
                $tweet->removertweet($_POST['id']);
            }
            header('Location: /timeline');

        }else {
            $this->erroauth();
        }
    }

    public function erroauth()
    {
        $this->view->erroautenticar = true;
        $this->view->informacao = "*Necessário efetuar autenticação!";
        $this->render('index');
    }
}
