<?php
session_start();
if (!isset($_SESSION['cargo']) || $_SESSION['cargo'] != 'monitor') {
    header('Location: loginProyectoExtraClase.html');
    exit();
}

if (!isset($_SESSION['nombre'])) {
    echo "Error: La sesión no tiene un nombre definido.";
    exit();
}

$nombre = $_SESSION['nombre'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Monitor - TutorSpace</title>

  <script src="libs/jquery.js"></script>

  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  
  <script src="libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <link rel="stylesheet" href="libs/bootstrap-datepicker/css/bootstrap-datepicker.css">

  <style>
    .bg {
      background-color: beige;
      background-position: center center;
      background-size: cover;
      height: 150vh;
    }
    .registro-container {
      background-color: rgba(255, 255, 255, 0.8);
      border-radius: 10px;
      box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }
    .registro-title {
      font-size: 24px;
      font-weight: bold;
    }
    .registro-form {
      margin-top: 20px;
    }
    .form-control {
      font-size: 14px;
    }
    .btn-register {
      font-size: 16px;
      padding: 12px;
    }
    .link-login {
      font-size: 12px;
    }
  </style>
</head>
<body>

  <div class="container-fluid">
    <div class="row">
      <div class="col bg">
        <div><br></div>
        <div class="text-end">
            <img src="imagenes/TutorSpaceimg.png" width="140" height="110" class="rounded mx-auto d-block" ALIGN=LEFT>
        </div>
        <div class="registro-title text-center">
          <h1>Bienvenido Tutor <?php echo htmlspecialchars($nombre); ?> a tutorspace</h1>

            <label type="materias" class="form-label" for="materias">Selecciona una materia de la cual deseas dar clase</label>
            <div><br></div>
            <select name="materias" id="materias" class="form-control">
                <option value="selecciona">Selecciona una materia</option>
                <?php
                // Conexión a la base de datos
                $conexion = pg_connect("host=localhost dbname=tutorspace user=postgres password=1234");

                // Comprobar conexión
                if (!$conexion) {
                    echo "Failed to connect to PostgreSQL: " . pg_last_error();
                    exit;
                }

                // Consulta para obtener las materias de la base de datos
                $consulta = "SELECT * FROM materias";
                $resultado = pg_query($conexion, $consulta);

                // Iterar sobre los resultados y crear opciones para el select
                while ($fila = pg_fetch_assoc($resultado)) {
                    echo "<option value='" . $fila['id_materia'] . "'>" . $fila['materia'] . "</option>";
                }

                // Liberar resultado
                pg_free_result($resultado);

                // Cerrar conexión
                pg_close($conexion);
                ?>
            </select>

            <div class="mb-3">
              <button type="submit" class="btn btn-primary btn-login w-100">Iniciar Sesión</button>
            </div>

        </div>
      </div>
    </div>
  </div>

<!-- Bootstrap JS y dependencias de jQuery y Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>  
</html>