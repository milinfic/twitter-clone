<?php

namespace App;

class Connection {

	public static function getDb(){
        try{
            //$bd = 'mysql';//mysql, sqlserver, etc
            $host = 'localhost'; // local de conexão do bd
            $dbname = 'twitter_clone'; //nome do banco de dados
            $user = 'root'; //nome do usuário
            $pass = 'senha'; //senha

            $conn = new \PDO(
                "mysql:host=$host;dbname=$dbname","$user","$pass"
            );
            return $conn;
        }catch(\PDOException $e){
            echo '<p>'.$e->getMessage().'</p>';
        }
	}
}

?>