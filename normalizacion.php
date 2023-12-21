<?php
// normalizacion.php
require_once 'config.php';

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

if (isset($_GET["criterios"])) {
    $criteriosSeleccionados = explode(',', $_GET["criterios"]);
    $numCriterios = count($criteriosSeleccionados);

    $datosCriterios = [];

    // Obtener datos de los criterios seleccionados
    $criteriosIds = implode(',', $criteriosSeleccionados);
    $sql = "SELECT id, nombre, precio, calidad, vida_util FROM criterios_tabla WHERE id IN ($criteriosIds)";
    $result = $conn->query($sql);

    if (!$result) {
        die("Error en la consulta: " . mysqli_error($conn));
    }

    while ($row = $result->fetch_assoc()) {
        $datosCriterios[] = $row;
    }

    // Calcular sumatorias
    $sumatoriaPrecio = 0;
    $sumatoriaCalidad = 0;
    $sumatoriaVidaUtil = 0;

    foreach ($datosCriterios as $proveedor) {
        $sumatoriaPrecio += $proveedor['precio'];
        $sumatoriaCalidad += $proveedor['calidad'];
        $sumatoriaVidaUtil += $proveedor['vida_util'];
    }

    // Construir la matriz de comparación de criterios
    $matrizComparacion = [
        'precio' => [],
        'calidad' => [],
        'vida_util' => [],
    ];

    foreach ($datosCriterios as $proveedor1) {
        $matrizComparacion['precio'][$proveedor1['id']] = [];
        $matrizComparacion['calidad'][$proveedor1['id']] = [];
        $matrizComparacion['vida_util'][$proveedor1['id']] = [];

        foreach ($datosCriterios as $proveedor2) {
            $matrizComparacion['precio'][$proveedor1['id']][$proveedor2['id']] = $proveedor1['precio'] / $proveedor2['precio'];
            $matrizComparacion['calidad'][$proveedor1['id']][$proveedor2['id']] = $proveedor1['calidad'] / $proveedor2['calidad'];
            $matrizComparacion['vida_util'][$proveedor1['id']][$proveedor2['id']] = $proveedor1['vida_util'] / $proveedor2['vida_util'];
        }
    }

    $mediasPrecio = [];
    $mediasCalidad = [];
    $mediasVidaUtil = [];

    foreach ($datosCriterios as $proveedor) {
        $mediasPrecio[$proveedor['id']] = $sumatoriaPrecio != 0 ? $sumatoriaPrecio / count($datosCriterios) : 0;
        $mediasCalidad[$proveedor['id']] = $sumatoriaCalidad != 0 ? $sumatoriaCalidad / count($datosCriterios) : 0;
        $mediasVidaUtil[$proveedor['id']] = $sumatoriaVidaUtil != 0 ? $sumatoriaVidaUtil / count($datosCriterios) : 0;
    }

    // Construir la matriz normalizada de comparación de criterios
    $matrizNormalizada = [
        'precio' => [],
        'calidad' => [],
        'vida_util' => [],
    ];

    foreach ($datosCriterios as $proveedor1) {
        foreach ($datosCriterios as $proveedor2) {
            $matrizNormalizada['precio'][$proveedor1['id']][$proveedor2['id']] =
                $matrizComparacion['precio'][$proveedor1['id']][$proveedor2['id']] / $mediasPrecio[$proveedor1['id']];

            $matrizNormalizada['calidad'][$proveedor1['id']][$proveedor2['id']] =
                $matrizComparacion['calidad'][$proveedor1['id']][$proveedor2['id']] / $mediasCalidad[$proveedor1['id']];

            $matrizNormalizada['vida_util'][$proveedor1['id']][$proveedor2['id']] =
                $matrizComparacion['vida_util'][$proveedor1['id']][$proveedor2['id']] / $mediasVidaUtil[$proveedor1['id']];
        }
    }

    $conn->close(); 
}
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

                    <!-- Tabla de Datos -->
                    <h2>Tabla de Datos</h2>
                    <table class="table-container-normalizacion">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Calidad</th>
                            <th>Vida Util</th>
                        </tr>
                        <?php foreach ($datosCriterios as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['nombre'] ?></td>
                                <td><?= $row['precio'] ?></td>
                                <td><?= $row['calidad'] ?></td>
                                <td><?= $row['vida_util'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <!-- Matriz Criterio Precio -->
                    <h3>Matriz Criterio Precio</h3>
                    <table class="table-container-normalizacion">
                        <tr>
                            <th></th>
                            <?php foreach ($datosCriterios as $proveedor): ?>
                                <th><?= $proveedor['nombre'] ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <?php foreach ($datosCriterios as $proveedor1): ?>
                            <tr>
                                <th><?= $proveedor1['nombre'] ?></th>
                                <?php $sumatoriaFilaPrecio = 0; ?>
                                <?php foreach ($datosCriterios as $proveedor2): ?>
                                    <?php
                                    $valorFormateado = number_format($matrizComparacion['precio'][$proveedor1['id']][$proveedor2['id']], 2);
                                    ?>
                                    <td><?= $valorFormateado ?></td>
                                    <?php $sumatoriaFilaPrecio += $matrizComparacion['precio'][$proveedor1['id']][$proveedor2['id']]; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>

                        <tr>
                            <th style="background-color: rgb(27, 27, 117); color: white;">Sumatoria</th>
                            <?php foreach ($datosCriterios as $proveedor): ?>
                                <?php
                                $sumatoriaColumnaPrecio = 0;
                                foreach ($datosCriterios as $proveedor2) {
                                    $sumatoriaColumnaPrecio += $matrizComparacion['precio'][$proveedor2['id']][$proveedor['id']];
                                }
                                $sumatoriaFormateada = number_format($sumatoriaColumnaPrecio, 2);
                                ?>
                                <td><?= $sumatoriaFormateada ?></td>
                            <?php endforeach; ?>
                        </tr>
                        
                    </table>

                    <!-- Matriz Criterio Precio Normalizada -->
                    <h3>Matriz Criterio Precio Normalizada</h3>
                    <table class="table-container-normalizacion">
                        <tr>
                            <th></th>
                            <?php foreach ($datosCriterios as $proveedor): ?>
                                <th><?= $proveedor['nombre'] ?></th>
                            <?php endforeach; ?>
                            <th style="background-color: rgb(27, 27, 117); color: white;">Promedio</th>
                        </tr>
                        <?php foreach ($datosCriterios as $proveedor1): ?>
                            <tr>
                                <th><?= $proveedor1['nombre'] ?></th>
                                <?php
                                $sumatoriaFilaPrecioNormalizada = 0; 
                                ?>
                                <?php foreach ($datosCriterios as $proveedor2): ?>
                                    <?php
                                    $valorFormateado = number_format($matrizNormalizada['precio'][$proveedor1['id']][$proveedor2['id']], 2);
                                    $sumatoriaFilaPrecioNormalizada += $matrizNormalizada['precio'][$proveedor1['id']][$proveedor2['id']];
                                    ?>
                                    <td><?= $valorFormateado ?></td>
                                <?php endforeach; ?>
                                <?php
                                $promedioFilaPrecioNormalizada = $sumatoriaFilaPrecioNormalizada / count($datosCriterios);
                                $promedioFormateado = number_format($promedioFilaPrecioNormalizada, 2);
                                ?>
                                <td><?= $promedioFormateado ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>


                     <!-- Matriz Criterio Calidad -->
                    <h3>Matriz Criterio Calidad</h3>
                    <table class="table-container-normalizacion">
                        <tr>
                            <th></th>
                            <?php foreach ($datosCriterios as $proveedor): ?>
                                <th><?= $proveedor['nombre'] ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <?php foreach ($datosCriterios as $proveedor1): ?>
                            <tr>
                                <th><?= $proveedor1['nombre'] ?></th>
                                <?php $sumatoriaFilaCalidad = 0; ?>
                                <?php foreach ($datosCriterios as $proveedor2): ?>
                                    <?php
                                    $valorFormateado = number_format($matrizComparacion['calidad'][$proveedor1['id']][$proveedor2['id']], 2);
                                    ?>
                                    <td><?= $valorFormateado ?></td>
                                    <?php $sumatoriaFilaCalidad += $matrizComparacion['calidad'][$proveedor1['id']][$proveedor2['id']]; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <th style="background-color: rgb(27, 27, 117); color: white;">Sumatoria</th>
                            <?php foreach ($datosCriterios as $proveedor): ?>
                                <?php
                                $sumatoriaColumnaCalidad = 0;
                                foreach ($datosCriterios as $proveedor2) {
                                    $sumatoriaColumnaCalidad += $matrizComparacion['calidad'][$proveedor2['id']][$proveedor['id']];
                                }
                                $sumatoriaFormateada = number_format($sumatoriaColumnaCalidad, 2);
                                ?>
                                <td><?= $sumatoriaFormateada ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </table>

                    <!-- Matriz Criterio Calidad Normalizada -->
                    <h3>Matriz Criterio Calidad Normalizada</h3>
                    <table class="table-container-normalizacion">
                        <tr>
                            <th></th>
                            <?php foreach ($datosCriterios as $proveedor): ?>
                                <th><?= $proveedor['nombre'] ?></th>
                            <?php endforeach; ?>
                            <th style="background-color: rgb(27, 27, 117); color: white;">Promedio</th> 
                        </tr>
                        <?php foreach ($datosCriterios as $proveedor1): ?>
                            <tr>
                                <th><?= $proveedor1['nombre'] ?></th>
                                <?php
                                $sumatoriaFilaCalidadNormalizada = 0; 
                                ?>
                                <?php foreach ($datosCriterios as $proveedor2): ?>
                                    <?php
                                    $valorFormateado = number_format($matrizNormalizada['calidad'][$proveedor1['id']][$proveedor2['id']], 2);
                                    $sumatoriaFilaCalidadNormalizada += $matrizNormalizada['calidad'][$proveedor1['id']][$proveedor2['id']];
                                    ?>
                                    <td><?= $valorFormateado ?></td>
                                <?php endforeach; ?>
                                <?php
                                $promedioFilaCalidadNormalizada = $sumatoriaFilaCalidadNormalizada / count($datosCriterios);
                                $promedioFormateado = number_format($promedioFilaCalidadNormalizada, 2);
                                ?>
                                <td><?= $promedioFormateado ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    
                    
                    <!-- Matriz Criterio Vida Util -->
                    <h3>Matriz Criterio Vida Util</h3>
                    <table class="table-container-normalizacion">
                        <tr>
                            <th></th>
                            <?php foreach ($datosCriterios as $proveedor): ?>
                                <th><?= $proveedor['nombre'] ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <?php foreach ($datosCriterios as $proveedor1): ?>
                            <tr>
                                <th><?= $proveedor1['nombre'] ?></th>
                                <?php $sumatoriaFilaVidaUtil = 0; ?>
                                <?php foreach ($datosCriterios as $proveedor2): ?>
                                    <?php
                                    $valorFormateado = number_format($matrizComparacion['vida_util'][$proveedor1['id']][$proveedor2['id']], 2);
                                    ?>
                                    <td><?= $valorFormateado ?></td>
                                    <?php $sumatoriaFilaVidaUtil += $matrizComparacion['vida_util'][$proveedor1['id']][$proveedor2['id']]; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <th style="background-color: rgb(27, 27, 117); color: white;">Sumatoria</th>
                            <?php foreach ($datosCriterios as $proveedor): ?>
                                <?php
                                $sumatoriaColumnaVidaUtil = 0;
                                foreach ($datosCriterios as $proveedor2) {
                                    $sumatoriaColumnaVidaUtil += $matrizComparacion['vida_util'][$proveedor2['id']][$proveedor['id']];
                                }
                                $sumatoriaFormateada = number_format($sumatoriaColumnaVidaUtil, 2);
                                ?>
                                <td><?= $sumatoriaFormateada ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </table>

                    <!-- Matriz Criterio Vida Util Normalizada -->
                    <h3>Matriz Criterio Vida Util Normalizada</h3>
                    <table class="table-container-normalizacion">
                        <tr>
                            <th></th>
                            <?php foreach ($datosCriterios as $proveedor): ?>
                                <th><?= $proveedor['nombre'] ?></th>
                            <?php endforeach; ?>
                            <th style="background-color: rgb(27, 27, 117); color: white;">Promedio</th> 
                        </tr>
                        <?php foreach ($datosCriterios as $proveedor1): ?>
                            <tr>
                                <th><?= $proveedor1['nombre'] ?></th>
                                <?php
                                $sumatoriaFilaVidaUtilNormalizada = 0; 
                                ?>
                                <?php foreach ($datosCriterios as $proveedor2): ?>
                                    <?php
                                    $valorFormateado = number_format($matrizNormalizada['vida_util'][$proveedor1['id']][$proveedor2['id']], 2);
                                    $sumatoriaFilaVidaUtilNormalizada += $matrizNormalizada['vida_util'][$proveedor1['id']][$proveedor2['id']];
                                    ?>
                                    <td><?= $valorFormateado ?></td>
                                <?php endforeach; ?>
                                <?php
                                $promedioFilaVidaUtilNormalizada = $sumatoriaFilaVidaUtilNormalizada / count($datosCriterios);
                                $promedioFormateado = number_format($promedioFilaVidaUtilNormalizada, 2);
                                ?>
                                <td><?= $promedioFormateado ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>


                    <!-- Matriz Final -->
                    <h3>Matriz Final (Ponderación)</h3>
                    <table class="table-container-normalizacion">
                        <thead>
                            <tr>
                                <th></th>
                                <?php foreach ($datosCriterios as $proveedor1): ?>
                                    <th><?= $proveedor1['nombre'] ?></th>
                                <?php endforeach; ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $columnaSumas = array();
                            $filaSumas = array();
                            $mejorProveedor = '';
                            $maxValor = 0;
                            ?>
                            <?php foreach ($datosCriterios as $index => $proveedor1): ?>
                                <tr>
                                    <th><?= $proveedor1['nombre'] ?></th>
                                    <?php
                                    $filaSuma = 0;
                                    ?>
                                    <?php foreach ($datosCriterios as $index2 => $proveedor2): ?>
                                        <?php
                                        $valorFormateado = number_format(
                                            ($matrizNormalizada['precio'][$proveedor1['id']][$proveedor2['id']] +
                                                $matrizNormalizada['calidad'][$proveedor1['id']][$proveedor2['id']] +
                                                $matrizNormalizada['vida_util'][$proveedor1['id']][$proveedor2['id']]) / 3,
                                            2
                                        );
                                        ?>
                                        <td><?= $valorFormateado ?></td>
                                        <?php
                                        $filaSuma += $valorFormateado;
                                        ?>
                                    <?php endforeach; ?>
                                    <?php
                                    $columnaSumas[] = $filaSuma;
                                    $filaSumas[$index] = $filaSuma;
                                    ?>
                                  
                                    <?php
                                    $totalFila = 0;
                                    foreach ($columnaSumas as $index2 => $suma) {
                                        $totalFila += $suma * $filaSumas[$index2];
                                    }
                                    ?>
                                    <td><?= number_format($totalFila, 2) ?></td>
                                </tr>
                                <?php
                                if ($totalFila > $maxValor) {
                                    $maxValor = $totalFila;
                                    $mejorProveedor = $proveedor1['nombre'];
                                }
                                ?>
                            <?php endforeach; ?>
                            <tr>
                                <th style="background-color: rgb(27, 27, 117); color: white;">Ponderación</th>
                                <?php
                                foreach ($columnaSumas as $suma) {
                                    $sumaFormateada = number_format($suma, 2);
                                    echo "<td>$sumaFormateada</td>";
                                }
                                ?>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Mostrar el proveedor con el puntaje más alto -->
                    <p style="background-color: rgb(27, 27, 117); color: white; padding: 10px; margin: 10px 0; border-radius: 5px;">
                        <?php
                        $maxValorFormateado = number_format($maxValor, 2);
                        echo "El mejor proveedor es: $mejorProveedor con un valor de $maxValorFormateado";
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>