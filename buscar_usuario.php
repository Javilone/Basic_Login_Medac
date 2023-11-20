<?php
include 'header.php';
include 'sesiones.php';

// TODO Borrar los echo que imprimen directamente el error
// TODO Borrar la información específica de los códigos de error. Están documentados en txt 
// TODO Borrar estas líneas después :)

// Este script espera recibir el término de búsqueda desde Unity y responde con los resultados de la búsqueda en formato JSON.
// Busca usuarios en la base de datos que coincidan con un término de búsqueda proporcionado desde Unity y devuelve los resultados en formato JSON

try {
    $conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);
    if (!$conn){
        http_response_code(400);
        echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';

    }else {

        if(!sesionActiva()) {
            echo '{"codigo":411, "mensaje":"No estás autenticado", "respuesta":""}';
            exit;
        } else{

            // Maneja la búsqueda si se envió un término de búsqueda
            $searchTerm = isset($_POST['buscar']) ? $_POST['buscar'] : '';
        
            if (!empty($searchTerm)) {
                $sql = "SELECT id, usuario FROM usuarios WHERE usuario LIKE '%$searchTerm%'";
                $result = $conn->query($sql);

                if ($result) {
                    // Convierte los resultados a un array asociativo y lo envía como JSON
                    $usuarios = array();
                    while ($row = $result->fetch_assoc()) {
                        $usuarios[] = $row;
                    }
                    // El usuario está almacenado en este Json, en "respuesta"
                    echo '{"codigo":202, "mensaje":"El usuario existe en el sistema", "respuesta": '.$usuarios.'}';
                } else {
                    echo '{"codigo": 408, "mensaje": "No existe el usuario", "respuesta": ""}';
                }
            } else {
                echo '{"codigo": 405, "mensaje": "Término de búsqueda vacío", "respuesta": ""}';
            }
        }
    }
} catch (mysqli_sql_exception $e) {
    echo '{"codigo": 400, "mensaje": "Error intentando conectar", "respuesta": ""}';
}

mysqli_close($conn);
?>