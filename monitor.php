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

// Conexión a la base de datos
$conexion = pg_connect("host=localhost dbname=tutorspace user=postgres password=1234");

// Comprobar conexión
if (!$conexion) {
    echo "Fallo al conectar a PostgreSQL: " . pg_last_error();
    exit;
}

// Consulta para obtener el ID del tutor
$consulta_tutor = "SELECT id_tutor FROM tutores WHERE nombre = '$nombre'";
$resultado_tutor = pg_query($conexion, $consulta_tutor);

// Manejo de errores
if (!$resultado_tutor) {
    echo "Error en la consulta de obtener el ID del tutor: " . pg_last_error($conexion);
    exit;
}

// Obtener el ID del tutor
$registro_tutor = pg_fetch_assoc($resultado_tutor);
$id_tutor = $registro_tutor['id_tutor'];
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
          <h1>Bienvenido Tutor <?php echo htmlspecialchars($nombre); ?> a TutorSpace</h1>

            <label type="materias" class="form-label" for="materias">Selecciona una materia de la cual deseas dar clase</label>
            <div><br></div>
            <select name="materias" id="materias" class="form-control">
              <option value="selecciona">Selecciona una materia</option>
              <?php
              // Consulta para obtener las materias de la base de datos
              $consulta = "SELECT * FROM materias";
              $resultado = pg_query($conexion, $consulta);

              // Iterar sobre los resultados y imprimir las opciones para el select
              while ($fila = pg_fetch_assoc($resultado)) {
                  echo "<option value='" . $fila['id_materia'] . "'>" . $fila['materia'] . "</option>";
              }

              // Liberar resultado
              pg_free_result($resultado);
              ?>
            </select>

            <script>
                document.getElementById("materias").addEventListener("change", function() {
                    var idMateriaSeleccionada = this.value;
                    // Ahora puedes usar el ID de la materia seleccionada donde lo necesites
                    console.log("ID de la materia seleccionada: " + idMateriaSeleccionada);
                    // Si deseas enviar el ID de la materia seleccionada a través de un formulario,
                    // puedes asignarlo a un campo oculto y luego enviar el formulario
                    document.getElementById("id_materia_seleccionada").value = idMateriaSeleccionada;
                });
            </script>

            <form action="" method="post">
              <input type="hidden" id="id_materia_seleccionada" name="id_materia_seleccionada" value="">
              <div class="mb-3">
               <button type="submit" name="seleccionar_materia" class="btn btn-primary btn-login w-100">Seleccionar la materia</button>
              </div>
            </form>

            <?php
            // Verificar si se envió el formulario
            if (isset($_POST['seleccionar_materia'])) {
                $id_materia = $_POST['id_materia_seleccionada'];

                // Consulta para insertar la relación entre tutor y materia
                $consulta_relacion = "INSERT INTO tutores_materias (id_tutor, id_materia) VALUES ('$id_tutor', '$id_materia')";
                $resultado_relacion = pg_query($conexion, $consulta_relacion);

                // Manejo de errores
                if (!$resultado_relacion) {
                    echo "Error al insertar la relación tutor-materia: " . pg_last_error($conexion);
                    exit;
                }
            }
            ?>

        </div>

          <!-- Mostrar lista de materias, estudiantes y fechas -->
  <div class="container">
    <h2>Lista de tus proximas monitorias!!</h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Materia</th>
          <th>Estudiante</th>
          <th>Correo</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Consulta para obtener los datos de la tabla citas
        $consulta_citas = "SELECT m.materia, e.nombre, e.correo, c.fecha_cita
                           FROM citas c
                           JOIN materias m ON c.id_materia = m.id_materia
                           JOIN estudiantes e ON c.id_estudiante = e.id_estudiante
                           JOIN tutores_materias tm ON c.id_materia = tm.id_materia
                           WHERE tm.id_tutor = '$id_tutor'";
        $resultado_citas = pg_query($conexion, $consulta_citas);

        // Iterar sobre los resultados y mostrar la información en la tabla
        while ($fila = pg_fetch_assoc($resultado_citas)) {
            echo "<tr>
                    <td>" . $fila['materia'] . "</td>
                    <td>" . $fila['nombre'] . "</td>
                    <td>" . $fila['correo'] . "</td>
                    <td>" . $fila['fecha_cita'] . "</td>
                  </tr>";
        }

        // Liberar resultado
        pg_free_result($resultado_citas);
        ?>
      </tbody>
    </table>

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