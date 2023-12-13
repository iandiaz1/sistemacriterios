<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../config.php';

$resultado = array("mensaje" => "");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numeroCriterio = $_POST["numero_criterio"];
    $nombre = $_POST["nombre"];
    $prioridad = $_POST["prioridad"];

    $sql = "INSERT INTO criterios_tabla (numero_criterio, nombre, prioridad) VALUES ('$numeroCriterio', '$nombre', '$prioridad')";

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