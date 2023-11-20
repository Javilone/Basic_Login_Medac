<?php
include 'header.php';
include 'sesiones.php';

// TODO Borrar los echo que imprimen directamente el error
// TODO Borrar la información específica de los códigos de error. Están documentados en txt 
// TODO Borrar estas líneas después :)

// Este script PHP espera recibir el nuevo valor de usuario desde Unity y actualiza el usuario en la base de datos.

try {
    $conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);
    if (!$conn){
        http_response_code(400);
        echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';
    } else {

        if(!sesionActiva()) {
            echo '{"codigo":411, "mensaje":"No estás autenticado", "respuesta":""}';
            exit;

        } else{
            // Maneja la actualización si se envió un nuevo valor de usuario y el ID del usuario
            $nuevoNombreUsuario =   isset($_POST['editarUsuario']) ? $_POST['editarUsuario'] : '';
            $idUsuario =            isset($_POST['idUsuario']) ? $_POST['idUsuario'] : '';
            $nuevoPassword =        isset($_POST['editarPassword']) ? $_POST['editarPassword'] : '';

            $patron_usuario =      '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';
            $patron_password =     '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&#.$($)$-$_])[A-Za-z\d$@$!%*?&#.$($)$-$_]{8,15}$/';


            if (!empty($nuevoNombreUsuario) && preg_match($patron_usuario, $nuevoNombreUsuario) &&
                !empty($nuevoPassword ) && preg_match($patron_password,$nuevoPassword) &&
                !empty($idUsuario)) {

                // Actualizar el campo 'usuario' en la base de datos
                $sql = "UPDATE usuarios SET usuario = ?, password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $nuevoNombreUsuario, $nuevoPassword, $idUsuario);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo '{"codigo":206, "mensaje":"Usuario editado con exito", "respuesta":"'.$nuevoNombreUsuario.'"}';
                } else {
                    http_response_code(500);
                    echo '{"codigo":406, "mensaje":"Error actualizando datos del usuario", "respuesta":""}';
                }
            } else {
            http_response_code(405);
            echo '{"codigo":407, "mensaje":"Nuevo valor de usuario o ID de usuario vacío", "respuesta":""}';
            }
        }
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(400);
    echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';
}

mysqli_close($conn);
?>