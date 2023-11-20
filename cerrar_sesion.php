<?php

function cerrarSesion() {
    session_destroy();
    header("Location: index.php");
}
?>