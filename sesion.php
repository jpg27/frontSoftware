<?php

$host='localhost';
$bd='tutorspace';
$user='postgres';
$pass='1234';

$conexion=pg_connect("host=$host dbname=$bd user=$user password=$pass");

session_start();

$correo = $_POST['email'];
$contraseña = $_POST['password'];

// Consulta en la tabla estudiantes
$query_estudiantes = "SELECT 'estudiante' as cargo, nombre FROM estudiantes WHERE correo='$correo' AND contraseña='$contraseña'";

// Consulta en la tabla tutores
$query_tutores = "SELECT 'tutor' as cargo, nombre FROM tutores WHERE correo='$correo' AND contraseña='$contraseña'";

// Unimos ambas consultas
$query = "($query_estudiantes) UNION ($query_tutores)";

$consulta = pg_query($conexion, $query);
$cantidad = pg_num_rows($consulta);

if($cantidad > 0){
    $fila = pg_fetch_assoc($consulta);
    $_SESSION['correo'] = $correo;
    $_SESSION['cargo'] = $fila['cargo'];
    $_SESSION['nombre'] = $fila['nombre']; // Guarda el nombre en la sesión
    
    if($fila['cargo'] == 'estudiante'){
        header('Location: estudiante.php'); // Cambia a estudiante.php
    } elseif($fila['cargo'] == 'tutor') {
        header('Location: monitor.php'); // Cambia a la página del tutor
    } else {
        header('Location: loginProyectoExtraClase.html');
    }
    exit();
} else {
    echo "Datos incorrectos";
}

pg_close($conexion);
?>
