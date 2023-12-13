<?php
// Normalizacion.php
require_once 'config.php';

$criteriosSeleccionados = array();

if (isset($_GET["criterios"])) {
    $criteriosSeleccionados = explode(',', $_GET["criterios"]);
}

$infoCriterios = array();

foreach ($criteriosSeleccionados as $id) {
    $sql = "SELECT * FROM criterios_tabla WHERE id='$id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $infoCriterio = mysqli_fetch_assoc($result);

        $numeroCriterio = floatval($infoCriterio['numero_criterio']);
        $prioridad = floatval($infoCriterio['prioridad']);

        $sumatoria = $numeroCriterio + $prioridad;

        $ponderacion = $sumatoria / 2;

        $porcentaje = ($ponderacion / $sumatoria) * 100;

        $prioridad_min = 0;
        $prioridad_max = 100;
        $prioridad_normalizada = ($prioridad - $prioridad_min) / ($prioridad_max - $prioridad_min);

        $updateSql = "UPDATE criterios_tabla SET sumatoria = $sumatoria, ponderacion = $ponderacion, porcentaje = $porcentaje, prioridad_normalizada = $prioridad_normalizada WHERE id = '$id'";
        $updateResult = mysqli_query($conn, $updateSql);

        if (!$updateResult) {
            echo "Error al actualizar la base de datos: " . mysqli_error($conn);
            exit;
        }

        $infoCriterio['sumatoria'] = $sumatoria;
        $infoCriterio['ponderacion'] = $ponderacion;
        $infoCriterio['porcentaje'] = $porcentaje;
        $infoCriterio['prioridad_normalizada'] = $prioridad_normalizada;

        $infoCriterios[] = $infoCriterio;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criterios</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="normalizacion-main">
        <div class="normalizacion-container">
            <h1>Comparación de Criterios</h1>
            <h2>Matriz de comparación de Criterios</h2>
            <div class="normalizacion">
                <div class="criterios-box-normalizacion-matriz">
                    <a href="matriz.php">Volver a Matriz</a>
                    <a href="index.html">Registrar Matriz</a>
                </div>
                <div class="normalizacion-box">
                    <?php if (!empty($infoCriterios)): ?>
                        <table class="table-container-normalizacion">
                            <tr>
                                <th>ID</th>
                                <th>Número de Criterio</th>
                                <th>Nombre</th>
                                <th>Prioridad</th>
                                <th>Sumatoria</th>
                                <th>Ponderación</th>
                                <th>Porcentaje %</th>
                                <th>Prioridad Normalizada</th>
                            </tr>
                            <?php foreach ($infoCriterios as $criterio) : ?>
                                <tr>
                                    <td><?php echo $criterio['id']; ?></td>
                                    <td><?php echo $criterio['numero_criterio']; ?></td>
                                    <td><?php echo $criterio['nombre']; ?></td>
                                    <td><?php echo $criterio['prioridad']; ?></td>
                                    <td><?php echo $criterio['sumatoria']; ?></td>
                                    <td><?php echo $criterio['ponderacion']; ?></td>
                                    <td><?php echo $criterio['porcentaje']; ?>%</td>
                                    <td><?php echo $criterio['prioridad_normalizada']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <p>No se ha seleccionado ningún criterio. Regresa a la página anterior y selecciona uno.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>