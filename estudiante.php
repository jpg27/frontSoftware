<?php
session_start();
if (!isset($_SESSION['cargo']) || $_SESSION['cargo'] != 'estudiante') {
    header('Location: loginProyectoExtraClase.html');
    exit();
}

if (!isset($_SESSION['nombre'])) {
    echo "Error: La sesión no tiene un nombre definido.";
    exit();
}

$nombre = $_SESSION['nombre'];

// Conexión a la base de datos
$conexion = pg_connect("host=localhost dbname=tutorspace user=postgres password=1234");

// Comprobar conexión
if (!$conexion) {
    echo "Failed to connect to PostgreSQL: " . pg_last_error();
    exit;
}

// Obtener el ID del estudiante
$consulta_estudiante = "SELECT id_estudiante FROM estudiantes WHERE nombre = '$nombre'";
$resultado_estudiante = pg_query($conexion, $consulta_estudiante);

// Manejo de errores
if (!$resultado_estudiante) {
    echo "Error en la consulta de obtener el ID del estudiante: " . pg_last_error($conexion);
    exit;
}

// Obtener el ID del estudiante
$registro_estudiante = pg_fetch_assoc($resultado_estudiante);
$id_estudiante = $registro_estudiante['id_estudiante'];

// Verificar si se envió el formulario
if (isset($_POST['seleccionar_materia'])) {
    $id_materia = $_POST['materias'];
    $fecha_materia = $_POST['fecha_materia'];

    // Consulta para insertar la cita en la tabla "citas"
    $consulta_cita = "INSERT INTO citas (id_estudiante, id_materia, fecha_cita) VALUES ('$id_estudiante', '$id_materia', '$fecha_materia')";
    $resultado_cita = pg_query($conexion, $consulta_cita);

    // Manejo de errores
    if (!$resultado_cita) {
        echo "Error al insertar la cita: " . pg_last_error($conexion);
        exit;
    } else {
        echo "Cita registrada exitosamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estudiante - TutorSpace</title>

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
          <h1>Bienvenido <?php echo htmlspecialchars($nombre); ?> a TutorSpace</h1>

            <div><br></div>
            <label type="materias" class="form-label" for="materias">Selecciona una materia de la cual deseas un tutor o clase</label>
            <form action="" method="post">
                <select name="materias" id="materias" class="form-control">
                    <option value="selecciona">Seleccionar una materia</option>
                    <?php
                    // Consulta para obtener las materias de la base de datos
                    $consulta = "SELECT * FROM materias";
                    $resultado = pg_query($conexion, $consulta);

                    // Iterar sobre los resultados y crear opciones para el select
                    while ($fila = pg_fetch_assoc($resultado)) {
                        echo "<option value='" . $fila['id_materia'] . "'>" . $fila['materia'] . "</option>";
                    }

                    // Liberar resultado
                    pg_free_result($resultado);
                    ?>
                </select>

                <div class="mb-3">
                  <label for="fecha_materia" class="form-label" ALIGN=CENTER>Ingresa la fecha en la cual deseas ver la materia</label>
                  <input type="date" class="form-control" id="fecha_materia" name="fecha_materia" required>
                </div>

                <div class="mb-3">
                   <button type="submit" name="seleccionar_materia" class="btn btn-primary btn-login w-100">Registrar cita</button>
                </div>
            </form>
        </div>

          <!-- Mostrar lista de materias, monitores y fechas -->
  <div class="container">
    <h2>Lista de tus proximas clases de monitoria!!</h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Materia</th>
          <th>Monitor</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Consulta para obtener los datos de la tabla tutores_materias
        $consulta_tutores_materias = "SELECT m.materia, t.nombre, c.fecha_cita
                                      FROM tutores_materias tm
                                      JOIN materias m ON tm.id_materia = m.id_materia
                                      JOIN tutores t ON tm.id_tutor = t.id_tutor
                                      JOIN citas c ON tm.id_materia = c.id_materia";
        $resultado_tutores_materias = pg_query($conexion, $consulta_tutores_materias);

        // Iterar sobre los resultados y mostrar la información en la tabla
        while ($fila = pg_fetch_assoc($resultado_tutores_materias)) {
            echo "<tr>
                    <td>" . $fila['materia'] . "</td>
                    <td>" . $fila['nombre'] . "</td>
                    <td>" . $fila['fecha_cita'] . "</td>
                  </tr>";
        }

        // Liberar resultado
        pg_free_result($resultado_tutores_materias);
        ?>
      </tbody>
    </table>
    <div><br></div>

    <button type="submit" class="btn btn-danger btn-register w-100" onclick="window.location.href='LoginProyectoExtraClase.html'">SALIR</button>
      </div>
    </div>
  </div>


  </div>

<!-- Bootstrap JS y dependencias de jQuery y Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php
// Cerrar conexión
pg_close($conexion);
?>
</body>
</html>