<?php

// Establecer un tiempo de vida para la sesión (10 minutos en este caso)


function iniciarSesion($usuario) {

    session_name("loginUsuario");
    session_start();

    $_SESSION["autentificado"] = "SI";
    $_SESSION["ultimoAcceso"] = date("Y-n-j H:i:s");
    $_SESSION["usuario"] = $usuario;

    // calculamos el tiempo transcurrido
    $fechaGuardada = $_SESSION["ultimoAcceso"];
    $ahora = date("Y-n-j H:i:s");
    $tiempo_transcurrido = (strtotime($ahora) - strtotime($fechaGuardada));

    // comparamos el tiempo transcurrido
    if ($tiempo_transcurrido >= 600) {
        // si han pasado 10 minutos o más, destruimos la sesión
        session_destroy();
        header("Location: index.php"); // redirigimos al usuario a la página de autenticación
    } else {
        // sino, actualizamos la fecha de la sesión
        $_SESSION["ultimoAcceso"] = $ahora;
    }
}

function sesionActiva() {
    return isset($_SESSION['autentificado']) && $_SESSION['autentificado'] === "SI";
}



?>