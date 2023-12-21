<?php
// matriz.php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])) {
    $idEliminar = $_POST["id_eliminar"];
    $sqlEliminar = "DELETE FROM criterios_tabla WHERE id='$idEliminar'";
    $resEliminar = mysqli_query($conn, $sqlEliminar);

    if ($resEliminar) {
        echo "Registro eliminado con éxito.";

        exit();
    } else {
        echo "Error al eliminar el registro: " . mysqli_error($conn);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["actualizar"])) {
    $idActualizar = $_POST["id_actualizar"];
    $nuevoNombre = $_POST["nuevo_nombre"];
    $nuevoPrecio = $_POST["nuevo_precio"];
    $nuevaCalidad = $_POST["nueva_calidad"];
    $nuevaVidaUtil = $_POST["nueva_vidautil"];


    $sqlActualizar = "UPDATE criterios_tabla SET nombre='$nuevoNombre', precio='$nuevoPrecio', calidad='$nuevaCalidad', vida_util='$nuevaVidaUtil'   WHERE id='$idActualizar'";
    $resActualizar = mysqli_query($conn, $sqlActualizar);

    if ($resActualizar) {
        echo "Registro actualizado con éxito.";
        header("Location: matriz.php");
        exit();
    } else {
        echo "Error al actualizar el registro: " . mysqli_error($conn);
    }
}

$sql = "SELECT * FROM criterios_tabla";
$result = mysqli_query($conn, $sql);
$criterios = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $criterios[] = $row;
    }
} else {
    $criterios = array();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Criterios</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div class="matriz-main">
      <div class="matriz-container">
      <h1>Iniciar Matriz</h1>
      <h2>Elige los criterios para tomar en cuenta</h2>
  
      <div class="matriz">
          <div class="matriz-box-register-matriz">
              <a href="index.html">Registrar Matriz</a>
          </div>

          <div class="matriz-box-form-register">
          <form method="post" id="form-generar" action="normalizacion.php">
             <?php foreach ($criterios as $criterio) : ?>
                <h3>Registro ID: <?php echo $criterio['id']; ?></h3>
                 
                 <table class="table-container-matriz">
                     <tr>
                         <th>Nombre</th>
                         <th>Precio</th>
                         <th>Calidad</th>
                         <th>Vida Util</th>
                     </tr>
                     <tr>
                         <td><?php echo $criterio['nombre']; ?></td>
                         <td><?php echo $criterio['precio']; ?></td>
                         <td><?php echo $criterio['calidad']; ?></td>
                         <td><?php echo $criterio['vida_util']; ?></td>
                         <td>
                             <button type="button" onclick="mostrarForm('<?php echo $criterio['id']; ?>')">Actualizar</button>
  
                             <form method="post" action="" class="formulario-eliminar" onsubmit="eliminarCriterio(event, '<?php echo $criterio['id']; ?>')">
                                 <input type="hidden" name="id_eliminar" value="<?php echo $criterio['id']; ?>">
                                 <button type="button" name="eliminar" onclick="eliminarCriterio(event, '<?php echo $criterio['id']; ?>')">Eliminar</button>
                             </form>
  
                             <input type="checkbox" name="criterios_seleccionados[]" value="<?php echo $criterio['id']; ?>">
                         </td>
                     </tr>
                 </table>
     
                 <form method="post" action="matriz.php" class="formulario-actualizar" id="form<?php echo $criterio['id']; ?>" style="display: none;">
                     <input type="hidden" name="id_actualizar" value="<?php echo $criterio['id']; ?>">
                     <input type="text" name="nuevo_nombre" placeholder="Nuevo Nombre...">
                     <input type="number" name="nuevo_precio" placeholder="Nuevo Precio...">
                     <input type="number" name="nueva_calidad" placeholder="Nueva Calidad...">
                     <input type="number" name="nueva_vidautil" placeholder="Nueva Vida Util...">
                     <button type="submit" name="actualizar">Enviar</button>
                     <button type="button" onclick="ocultarForm('<?php echo $criterio['id']; ?>')">Cerrar</button>
                 </form>
             <?php endforeach; ?>
          <button type="button" onclick="generarNormalizacion()" class="selected-matriz">Generar</button>
          </form>
          </div>
          
      </div>
      
      </div>
    </div>

    <script src="./js/updateMatriz.js"></script>
</body>
</html>