<?php

include 'conexion.php';

session_start();// Inicia una nueva sesión o reanuda la existente
session_unset();// Elimina todas las variables de sesión actualmente registradas
session_destroy();// Destruye la sesión actual

header('location:admin_login.php');

?>