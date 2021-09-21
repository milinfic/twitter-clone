<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Modells\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action
{

	public function index()
	{
		$this->render('index');
	}

	public function inscreverse()
	{	
		$this->zerar();
		$this->view->erroCadastro = false;
		$this->render('inscreverse');
	}

	public function zerar(){
		$this->view->usuario = array(
			'nome' => '',
			'email' => '',
			'senha' => '',
		);
	}

	public function registrar()
	{
		$this->view->erroCadastro = true;
		if (!isset($_POST['nome'])) {			
			$this->view->erroCadastro = true;
			$this->zerar();
			$this->view->informacao = "*Rota inválida!";
			$this->render('inscreverse');
			
		} else {
			$usuario = Container::getModel('Usuario');

			$usuario->__set('nome', $_POST['nome']);
			$usuario->__set('email', $_POST['email']);
			$usuario->__set('senha', $_POST['senha']);


			if (count($usuario->getUsuarioPorEmail()) == 0) {
				$this->view->erroCadastro = false;
				$usuario->salvar();
				$this->render('cadastro');

			} else {
				$this->view->usuario = array(
					'nome' => $_POST['nome'],
					'email' => $_POST['email'],
					'senha' => $_POST['senha'],
				);
				$this->view->informacao = "*E-mail já cadastrado!";
				$this->render('inscreverse');
			}
		}
	}

	public function cadastro()
	{
		$this->render('cadastro');
	}
}
