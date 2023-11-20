<?php
include 'header.php';
include 'sesiones.php';

/* TODO ESTE SCRIPT NO LLEVA VERIFICACION HASH DE CONTRASEÑAS, PUES PARA LAS PRUEBAS, EL USUARIO EN LA BD 
    NO TENÍA LA CONTRASEÑA GENERADA MEDIANTE HASH. SE ESCRIBIÓ A MANO DIRECTAMENTE.*/

// TODO Borrar los echo que imprimen directamente el error
// TODO Borrar la información específica de los códigos de error. Están documentados en txt 
// TODO Borrar estas líneas después :)

try {
    $conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);
    if (!$conn){
        echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';
    } else {
        if (isset($_POST['usuario']) && isset($_POST['password'])){

            $usuario = $_POST['usuario'];
            $password = $_POST['password'];

            $texto;
            $nombre;
            $rol;
            $apellido1;
            $apellido2;

            // Verifica que un usuario existe utilizando un prepared statement
            $sql = "SELECT * FROM usuarios WHERE usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0){   
                $row = $resultado->fetch_assoc();
                // Recupera la contraseña almacenada en la base de datos
                $pass_bd = $row['password'];

                // Comprueba si la contraseña coincide con la almacenada
                if ($password === $pass_bd) {

                    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $usuario);
                    $stmt->execute();
                    $resultado = $stmt->get_result();

                    // $texto será un json enmascarado como texto que en Unity C# se transforma sustituyendo los #
                    // De esta forma es más simple su implementación
                    while ($row = $resultado->fetch_assoc()) {
                        // Asignar el valor de cada columna a una variable del mismo nombre
                        $id = $row['id'];
                        $usuario = $row['usuario'];
                        $rol = $row['rol'];
                        $nombre = $row['nombre'];
                        $apellido1 = $row['apellido1'];
                        $apellido2 = $row['apellido2'];
                    }
                    iniciarSesion($usuario);

                    echo '{"codigo":205 , "mensaje":"Inicio de sesion correcto" , "respuesta":"" , "id": "'.$id.'" , "rol": "'.$rol.'" , "usuario": "'.$usuario.'" , "nombre": "'.$nombre.'" , "apellido1": "'.$apellido1.'" , "apellido2": "'.$apellido2.'"}';
                } else {
                    echo '{"codigo":204, "mensaje":"El usuario o la contraseña son incorrectos", "respuesta":""}';
                }
            } else {
                echo '{"codigo":204, "mensaje":"El usuario o la contraseña son incorrectos", "respuesta":""}';
            }
        } else {
            echo '{"codigo":402, "mensaje":"Faltan datos para ejecutar la acción solicitada", "respuesta":""}';
        }

    }
} catch (Exception $e){
    echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';
}
mysqli_close($conn);
?>