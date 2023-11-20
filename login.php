<?php
include 'header.php';
include 'sesiones.php';

// TODO Borrar los echo que imprimen directamente el error
// TODO Borrar la información específica de los códigos de error. Están documentados en txt 
// TODO Borrar estas líneas después :)

try {
    $conn = mysqli_connect($db_servidor, $db_usuario, $db_pass, $db_baseDatos);
    if (!$conn){
        echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';
    }else {
        if  (isset($_POST['usuario']) && isset($_POST['password'])){

            $usuario =     $_POST['usuario'];
            $password =    $_POST['password'];

            $texto;

            // Verifica que un usuario existe utilizando un prepared statement
            $sql = "SELECT * FROM usuarios WHERE usuario = ?"; // No es necesario verificar la contraseña aqui
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0){   
                $row = $resultado->fetch_assoc();
                $hash = $row['password']; // Recupera la contraseña hash almacenada en la base de datos.

                if(password_verify($password, $hash)){

                    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $usuario);
                    $stmt->execute();
                    $resultado = $stmt->get_result();


                    // $texto será un json enmascarado como texto que en Unity C# se transforma sustituyendo los #
                    // De esta forma es más simple su implementación
                    
                    while ($row = $resultado->fetch_assoc()){
                        $texto =    "{#id#:" . $row['id'] .
                                    ",#usuario#:#" . $row['usuario'] .
                                    "#,#password#:#" . $row['password'] .
                                    "#,#rol#:#" . $row['rol'] .
                                    "#,#nombre#:#" . $row['nombre'] .
                                    "#,#apellido1#:#" . $row['apellido1'] .
                                    "#,#apellido2#:#" . $row['apellido2'] .
                                "}";
                    }
                    iniciarSesion($usuario);

                }echo '{"codigo":205, "mensaje":"Inicio de sesion correcto", "respuesta":"'.$texto.'"}';
            }else{
                echo '{"codigo":204, "mensaje":"El usuario o la contraseña son incorrectos", "respuesta":""}';
            }
        }else{
            echo '{"codigo":402, "mensaje":"Faltan datos para ejecutar la acción solicitada", "respuesta":""}';
        }

    }
} catch (Exception $e){
    echo '{"codigo":400, "mensaje":"Error intentando conectar", "respuesta":""}';
}
mysqli_close($conn);
?>