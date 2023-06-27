<?php

session_start(); // Iniciar sessão 

//Limpar o buffer
ob_start();

// Definir um fuso horario padrao 
date_default_timezone_set('America/Sao_Paulo');

// Gerar com PHP o horario atual 
$horario_atual = date("H:i:s"); 

//var_dump($horario_atual);

// Gerar a data com PHP no formato que deve ser salvo no BD 
$data_entrada = date('Y/m/d');

//incluir conexao com BD
include_once"conexao.php";

//ID do usuario fixo para teste
$id_usuario = 1;

//Recuperar ultimo ponto do usuario
$query_ponto = "SELECT id AS id_ponto, saida_intervalo, retorno_intervalo, saida
                    FROM  pontos
                    WHERE usuario_id =:usuario_id
                    ORDER BY id DESC
                    LIMIT 1";
//Preparar a Query
$result_ponto = $conn->prepare($query_ponto);

//Substituir o link da Query pelo valor
$result_ponto->bindParam(':usuario_id', $id_usuario);

//Executar a Query
$result_ponto->execute();
//Verificar o Ultimo Ponto
if (($result_ponto) and ($result_ponto->rowCount() != 0)){
    //Realizar leitura do registro
   $row_ponto = $result_ponto->fetch(PDO::FETCH_ASSOC);
   //var_dump($row_ponto);
    
   //Extrair para imprimir o nome da chave no Array
   extract($row_ponto);

   //Verificar se já saiu para intervalo
   if (($saida_intervalo == "") or ($saida_intervalo == null)) {
    //coluna que deve receber o valor
    $col_tipo_registro = "saida_intervalo";

    //tipo de registro
    $tipo_registro = "editar";

    //Texto parcial apresentado ao usuário
    $text_tipo_registro = "Saída Intervalo";
   }elseif 
   (($retorno_intervalo == "") or ($retorno_intervalo == null)) {
    //coluna que receberá o valor
    $col_tipo_registro = "retorno_intervalo";

    //Tipo de Registro
    $tipo_registro = "editar";

    //Texto parcial para o usuário
    $text_tipo_registro = "Retorno Intervalo";
   }elseif 
   (($saida == "") or ($saida == null)) {
    //coluna que receberá o valor
    $col_tipo_registro = "saida";

    //Tipo de Registro
    $tipo_registro = "editar";

    //Texto parcial para o usuário
    $text_tipo_registro = "Saída registrada com sucesso";
   }else{ //criar novo registro no BD com horario de entrada
    //Tipo de Registro
    $tipo_registro = "entrada";

    //Texto parcial para o usuário
    $text_tipo_registro = "Entrada registrada com sucesso";
   }

}else {
   //criar novo registro no BD com horario de entrada
    //Tipo de Registro
    $tipo_registro = "entrada";

    //Texto parcial para o usuário
    $text_tipo_registro = "Entrada registrada com sucesso";
 }      
 //verificar tipo de registro, novo ponto ou editar registro existente
switch($tipo_registro){
    //acessa o case quando deve editar o registro
    case "editar":
    //Query para editar no banco de dados
    $query_horario = "UPDATE pontos SET  $col_tipo_registro = :horario_atual
                    WHERE id = :id
                    LIMIT 1
    ";
    //Preparar a query
    $cad_horario = $conn->prepare($query_horario);
    //substituir link da query pelo valor
    $cad_horario->bindParam(':horario_atual',$horario_atual);
    $cad_horario->bindParam(':id',$id_ponto);
    break;

    default:
    $query_horario = "INSERT INTO pontos (data_entrada,entrada,usuario_id ) VALUES(:data_entrada,:entrada,:usuario_id )
    ";
    //Preparar a query
    $cad_horario = $conn->prepare($query_horario);
    //substituir link da query pelo valor
    $cad_horario->bindParam(':data_entrada',$data_entrada);
    $cad_horario->bindParam(':entrada',$horario_atual);
    $cad_horario->bindParam(':usuario_id',$id_usuario);

    break;
}


//Executar a Query
$cad_horario->execute();

//Acessar o IF quando cadastrar com sucesso
if ($cad_horario -> rowCount()) {
    $_SESSION['msg'] = "<p style = 'color:green;'>Horario de $text_tipo_registro cadastrado com sucesso !</p>";
    header("Location: index.php");
}else {
    $_SESSION['msg'] = "<p style = 'color:#f00;'>Horario de $text_tipo_registro não cadastrado com sucesso !</p>";
    header("Location: index.php");
}
