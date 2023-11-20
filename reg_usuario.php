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
        
        if(!sesionActiva()) {
            echo '{"codigo":411, "mensaje":"No estás autenticado", "respuesta":""}';
            exit;

        } else{
            if  (isset($_POST['usuario']) &&
                isset($_POST['password']) &&
                isset($_POST['rol'])){
                // && isset($_POST['player']) // Esta variable está por definir cómo se va a tratar. Ésta será el progreso del alumno (txt json?).
            
                $usuario =    $_POST['usuario'];
                $password =   $_POST['password'];
                $rol =        $_POST['rol'];
                // $player =     $_POST['player']; // Si se habilita, hay que agregarla a la inserción en la base de datos

                $patron_password =     '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&#.$($)$-$_])[A-Za-z\d$@$!%*?&#.$($)$-$_]{8,15}$/';
                $patron_usuario =      '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';
                $roles_permitidos =    ["docente", "alumno"];

                // Verifica que usuario, email y rol están dentro de los valores permitidos
                if  (preg_match($patron_password, $password) && 
                    preg_match($patron_usuario, $usuario) &&
                    in_array($rol, $roles_permitidos)){

                    echo 'Todos los datos cumplen con los requisitos.'; // TODO Borrar esta linea más adelante
                    $pass = password_hash($password, PASSWORD_DEFAULT);

                    // Verifica si el usuario existe lanzando consulta SQL.
                    $sql_verificar_usuario = "SELECT * FROM usuarios WHERE usuario = ?";
                    $stmt_verificar_usuario = $conn->prepare($sql_verificar_usuario);
                    $stmt_verificar_usuario->bind_param("s", $usuario); // Vincula el parámtro '?' con un $usuario (String "s")
                    $stmt_verificar_usuario->execute();
                    $resultado_verificar_usuario = $stmt_verificar_usuario->get_result();

                    if ($resultado_verificar_usuario->num_rows > 0){
                        echo '{"codigo":202, "mensaje":"El usuario existe en el sistema", "respuesta":"'.$resultado_verificar_usuario->num_rows.'"}';
                    }else{
                        // Si no existe, crea un prepared statement para insertar al usuario en la base de datos y vincula usando marcadores de posición '?'
                        $stmt_insertar_usuario = $conn->prepare("INSERT INTO `usuarios` (`usuario`, `password`, `rol`) VALUES (?, ?, ?)");
                        $stmt_insertar_usuario->bind_param("sss", $usuario, $pass, $rol);
                        // Si la inserción tiene éxito
                        if ($stmt_insertar_usuario->execute()) {

                            // En adelante, se recoge la información del usuario introducido para ser tratada en Unity.
                            $sql = "SELECT * FROM usuarios WHERE usuario = ?";
                            $stmt_select = $conn->prepare($sql);
                            $stmt_select->bind_param("s", $usuario);
                            $stmt_select->execute();
                            $resultado = $stmt_select->get_result();

                            // $texto será un json enmascarado como texto que en C# se transforma sustituyendo los #
                            // De esta forma es más simple su implementación. La información dada del JSON será tratada en Unity.
                            // Se almacenará en una respuesta en C#
                            // TODO ¿Por ver de eliminar esto para evitar filtrar datos? (¿json encode?)
                            $texto = '';
                            while ($row = $resultado->fetch_assoc()){
                                $texto =    "{#id#:".$row['id'].
                                            ",#usuario#:".$row['usuario'].
                                            "#,#password#:#".$row['password'].
                                            "#,#player#:".$row['player'].
                                         ",#rol#:".$row['rol']."}";
                        
                            }echo '{"codigo":201, "mensaje":"Usuario creado correctamente", "respuesta":"'.$texto.'"}';

                        }else{
                            echo '{"codigo":401, "mensaje":"Error intentando crear el usuario", "respuesta":""}';
                        }            
                    }   
                }else {
                    echo 'La contraseña o el email no cumplen con los requisitos.'; // TODO Borrar la información específica en esta linea más adelante
                }
            }else{
                echo '{"codigo":402, "mensaje":"Faltan datos para ejecutar la acción solicitada", "respuesta":""}';
            }
        }
    } 
}catch (mysqli_sql_exception $e){
    echo '{"codigo":400, "mensaje":""Error intentando conectar", "respuesta":""}';
}
mysqli_close($conn);
?>