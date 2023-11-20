<?php
include 'header.php';
include 'sesiones.php';

// TODO Borrar los echo que imprimen directamente el error
// TODO Borrar la información específica de los códigos de error. Están documentados en txt 
// TODO Borrar estas líneas después :)

// Este script espera recibir el ID del usuario que se va a eliminar desde Unity.

try {
    $conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);
    
    if (!$conn) {
        http_response_code(400);
        echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';
    } else {

        if(!sesionActiva()) {
            echo '{"codigo":411, "mensaje":"No estás autenticado", "respuesta":""}';
            exit;

        } else{

            // Maneja la eliminación si se envió el ID del usuario
            $idUsuario = isset($_POST['eliminarUsuario']) ? $_POST['eliminarUsuario'] : '';

            if (!empty($idUsuario)) {
                // Eliminar el usuario de la base de datos
                $sql = "DELETE FROM usuarios WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idUsuario);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo '{"codigo":208, "mensaje":"Usuario eliminado con éxito", "respuesta":""}';
                } else {
                    http_response_code(500);
                    echo '{"codigo":409, "mensaje":"Error eliminando usuario", "respuesta":""}';
                }
            } else {
                http_response_code(405);
                echo '{"codigo":410, "mensaje":"ID de usuario vacío", "respuesta":""}';
            }
        }
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(400);
    echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';
}

mysqli_close($conn);
?>
