<?php
// Asegurarnos de que la sesión esté iniciada para poder destruirla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vaciar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión en el servidor
session_destroy();

// Redirigir de vuelta al Login (index)
header("Location: index");
exit();
?>