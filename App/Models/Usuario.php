<?php

namespace App\Models;

use MF\Model\Model;
use PDOException;

class  Usuario extends Model
{
    private $id;
    private $nome;
    private $email;
    private $senha;

    //função get e set para trabalhar com as variáveis

    public function __get($atributo)
    {
        return $this->$atributo;
    }

    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    //Salvar
    public function salvar()    {
        try {
            $query = "insert into usuarios(nome, email, senha) 
                    values (?,?,md5(?))";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $this->__get('nome'));
            $stmt->bindValue(2, $this->__get('email'));
            $stmt->bindValue(3, $this->__get('senha'));
            $stmt->execute();

        } catch (\PDOException $e) {
            echo '<p>'.$e->getMessage().'</p>';
        }
    }

    //validar dados
    public function getUsuarioPorEmail()
    {
        $query = "select email from usuarios where email = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $this->__get('email'));
        $stmt->execute();


        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function autenticar(){
        $query = "Select id, nome, email 
                    from usuarios  
                    where email = ? and senha = md5(?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $this->__get('email'));
        $stmt->bindValue(2, $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if(isset($usuario['id'])){
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']); 
            $this->__set('email', $usuario['email']);
        }  else{            
            $this->__set('id', "");
            $this->__set('nome', "");
        }      
        return $this;        
    }

    public function getAll(){
        $query = "select u.id, u.nome, u.email ,
                    (select count(*) 
                        from usuarios_seguidores as us
                        where us.id_usuario = :id_usuario and
                        us.id_usuario_seguindo = u.id)
                    as seguindo_sn 
                    from usuarios as u 
                    where u.nome like :nome 
                    and u.id <> :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':id_usuario', $_SESSION['id']);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function seguirUsuario(){
        $query = "insert into usuarios_seguidores(id_usuario, id_usuario_seguindo) 
                    values (? , ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $_SESSION['id']);
        $stmt->bindValue(2, $this->__get('id_usuario'));        
        $stmt->execute();
    }

    public function deixarSeguirUsuario(){
        $query = "delete from usuarios_seguidores 
                    where id_usuario = ? 
                    and id_usuario_seguindo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,  $_SESSION['id']);
        $stmt->bindValue(2, $this->__get('id_usuario'));
        $stmt->execute();
    }

    public function getTotal(){
        $query = "select 
                    count(tweet) as total_Tweet,
                    (select 
                        count(id_usuario) 
                    from 
                        usuarios_seguidores where id_usuario = :id) as total_Seguindo,
                    (select 
                        count(id_usuario_seguindo)
                    from 
                        usuarios_seguidores where id_usuario_seguindo = :id) as total_Seguidores 
                from 
                    tweets where id_usuario = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $_SESSION['id']);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
