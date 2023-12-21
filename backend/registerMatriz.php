<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../config.php';

$resultado = array("mensaje" => "");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numeroCriterio = $_POST["numero_criterio"];
    $nombre = $_POST["nombre"];
    $precio = $_POST["precio"];
    $calidad = $_POST["calidad"];
    $vidaUtil = $_POST["vidautil"];

    $sql = "INSERT INTO criterios_tabla (nombre, precio, calidad, vida_util) VALUES ('$nombre', '$precio', '$calidad', '$vidaUtil')";

    $res = mysqli_query($conn, $sql);

    if ($res) {
        $resultado["mensaje"] = "Registrado con éxito";
    } else {
        $resultado["mensaje"] = "Error: " . mysqli_error($conn);
    }
}

echo json_encode($resultado);

$conn->close();
?>