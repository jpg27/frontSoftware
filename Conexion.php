<?php

$host='localhost';
$bd='tutorspace';
$user='postgres';
$pass='1234';

$nombre = $_REQUEST['nombre'];
$email = $_REQUEST['email'];
$password = $_REQUEST['password'];
$fecha_nac = $_REQUEST['fecha_nac'];
$college = $_REQUEST['college'];
$cargo = $_REQUEST['cargo'];

$conexion=pg_connect("host=$host dbname=$bd user=$user password=$pass");

//$query=("Insert into estuante(nombre, correo, contraseña, fecha_nac, institucion, cargo) values('$nombre', '$email', '$password', '$fecha_nac', '$college', '$cargo')");

if($cargo == 'monitor'){
    $query=("Insert into tutores(nombre, correo, contraseña, fecha_nac, institucion, cargo) values('$nombre', '$email', '$password', '$fecha_nac', '$college', '$cargo')");
}else{
    $query=("Insert into estudiantes(nombre, correo, contraseña, fecha_nac, institucion, cargo) values('$nombre', '$email', '$password', '$fecha_nac', '$college', '$cargo')");
}


$consulta=pg_query($conexion, $query);
pg_close();

if ($consulta) {
    echo '<script type="text/javascript">
    alert("El ingreso fue exitoso");
    window.location.href = "LoginProyectoExtraClase.html";
    </script>';
    exit(); 
} else {
    echo 'Hubo un error al ingresar los datos';
}


?>