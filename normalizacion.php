<?php
// Normalizacion.php
require_once 'config.php';

$criteriosSeleccionados = array();
$infoCriterios = array();
$sumaNumerosCriterio = array_fill(0, count($criteriosSeleccionados), 0);
$sumaPrioridades = 0;

$numProveedores = 0;

if (isset($_GET["criterios"])) {
    $criteriosSeleccionados = explode(',', $_GET["criterios"]);
    $numProveedores = count($criteriosSeleccionados);
}

// Obtener la suma de prioridades para la normalización
foreach ($criteriosSeleccionados as $id) {
    $sql = "SELECT * FROM criterios_tabla WHERE id='$id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $infoCriterio = mysqli_fetch_assoc($result);

        $prioridad = floatval($infoCriterio['prioridad']);
        $sumaPrioridades += $prioridad;

        $infoCriterio['prioridad'] = $prioridad;
        $infoCriterios[] = $infoCriterio;
    }
}

// Obtener la suma de los números de criterio para cada proveedor
foreach ($criteriosSeleccionados as $index => $id) {
    $sql = "SELECT * FROM criterios_tabla WHERE id='$id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $infoCriterio = mysqli_fetch_assoc($result);
        $numeroCriterio = floatval($infoCriterio['numero_criterio']);

        $sumaNumerosCriterio[$index] = $numeroCriterio;
    }
}

// Normalizar los datos
for ($i = 0; $i < count($infoCriterios); $i++) {
    $infoCriterios[$i]['prioridad_normalizada'] = $infoCriterios[$i]['prioridad'] / $sumaPrioridades;

    foreach ($criteriosSeleccionados as $index => $id) {
        $infoCriterios[$i]['numero_normalizado'][$index] = $infoCriterios[$i]['numero_criterio'] / $sumaNumerosCriterio[$index];
    }
}

// Calcular la suma y ponderación
$sumaPorProveedor = array_fill(0, $numProveedores, 0);
$ponderacionPorProveedor = array_fill(0, $numProveedores, 0);

foreach ($infoCriterios as $i => $criterio) {
    foreach ($criteriosSeleccionados as $index => $id) {
        $sumaPorProveedor[$index] += $criterio['numero_normalizado'][$index];
    }
}

foreach ($criteriosSeleccionados as $index => $id) {
    $ponderacionPorProveedor[$index] = $sumaPorProveedor[$index] / count($infoCriterios);
}

// Insertar sumatoria, ponderación y prioridad normalizada en la base de datos
foreach ($infoCriterios as $i => $criterio) {
    $sumatoria = $criterio['numero_normalizado'][$index];
    $ponderacion = $ponderacionPorProveedor[$i];
    $prioridadNormalizada = $criterio['prioridad_normalizada'];

    $nombre = mysqli_real_escape_string($conn, $criterio['nombre']);  
    $sqlUpdate = "UPDATE criterios_tabla SET sumatoria = $sumatoria, ponderacion = $ponderacion, prioridad_normalizada = $prioridadNormalizada WHERE nombre = '$nombre'";
    mysqli_query($conn, $sqlUpdate);
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
                        
                        

                        <!-- Tabla para mostrar la información normalizada de números -->
                        <table class="table-container-normalizacion">
                            <tr>
                                <th>Nombre</th>
                                <?php foreach ($criteriosSeleccionados as $index => $id): ?>
                                    <th>Criterio <?php echo $index + 1; ?> </th>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach ($infoCriterios as $i => $criterio): ?>
                                <tr>
                                    <td><?php echo $criterio['nombre']; ?></td>
                                    <?php foreach ($criteriosSeleccionados as $index => $id): ?>
                                        <td><?php echo number_format($criterio['numero_normalizado'][$index], 2); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td>Suma</td>
                                <?php foreach ($sumaPorProveedor as $suma): ?>
                                    <td><?php echo number_format($suma, 2); ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <td>Ponderación</td>
                                <?php foreach ($ponderacionPorProveedor as $ponderacion): ?>
                                    <td><?php echo number_format($ponderacion, 2); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </table>

                        <!-- Tabla para mostrar la información normalizada de prioridades -->
                        <table class="table-container-normalizacion">
                            <tr>
                                <th>Nombre</th>
                                <th>Prioridad Normalizada</th>
                            </tr>
                            <?php foreach ($infoCriterios as $i => $criterio): ?>
                                <tr>
                                    <td><?php echo $criterio['nombre']; ?></td>
                                    <td><?php echo number_format($criterio['prioridad_normalizada'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>

                        <!-- Calcular el total por Nombre -->
                        <?php
                        $totalPorProveedor = array_fill(0, $numProveedores, 0);

                        foreach ($infoCriterios as $i => $criterio) {
                            foreach ($criteriosSeleccionados as $index => $id) {
                                $totalPorProveedor[$index] += $criterio['numero_normalizado'][$index] * $ponderacionPorProveedor[$i];
                            }
                        }
                        ?>

                        <!-- Tabla para mostrar el total por Nombre -->
                        <table class="table-container-normalizacion">
                            <tr>
                                <th>Nombre</th>
                                <th>Total</th>
                            </tr>
                            <?php foreach ($totalPorProveedor as $index => $total): ?>
                                <tr>
                                    <td><?php echo $infoCriterios[$index]['nombre']; ?></td>
                                    <td><?php echo number_format($total, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>

                        <!-- Encontrar la mejor opción -->
                        <?php
                        $mejorOpcionIndex = array_search(max($totalPorProveedor), $totalPorProveedor);
                        ?>

                        <p style="color: white; background-color: rgb(38, 38, 152); text-align: center; margin-bottom: 5px; border-radius: 5px; padding: 6px; ">Mejor opción: <?php echo $infoCriterios[$mejorOpcionIndex]['nombre']; ?></p>
                   
                    <?php else: ?>
                        <p>No se ha seleccionado ningún criterio. Regresa a la página anterior y selecciona uno.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>