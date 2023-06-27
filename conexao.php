<?php
//Inicio da conexão com o banco de dados utilizando PDO
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "point";
$port = 3000;
try {
//Conexão com a porta
//$conn = new PDO ("mysql:host-$host; port-$port; dbname=". $dbname,$user, $pass);
//Conexão sem a porta
$conn = new PDO("mysql:host=$host; dbname=" . $dbname, $user, $pass);
//echo "Conexão com banco de dados realizado com sucesso.";
}catch (PDOException $err) { 
    echo "Erro: Conexão com banco de dados não realizado com sucesso. Erro gerado" 
    .$err->getMessage();
}
//Fim da conexao com banco de dados usando PDO