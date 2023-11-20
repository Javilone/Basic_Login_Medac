<?php
include 'header.php';

// Usado para verificar la conexión con el servidor y la bbdd
// TODO Borrar los echo que imprimen directamente el error
// TODO Borrar la información específica de los códigos de error. Están documentados en txt 
// TODO Borrar estas líneas después :)
try {
    $conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);

    if (!$conn){
        echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';
    }else {
        echo '{"codigo":200, "mensaje":"Conectado correctamente", "respuesta":""}';
    }
} catch (Exception $e){
    echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';
}

?>