<?php

//config.php

$servername = "localhost";
$username = "root";
$password = "";
$base = "criterios";

$conn = mysqli_connect($servername, $username, $password, $base);

if (!$conn) {
    echo "Error de conexión a la base de datos". mysqli_connect_error();
}

?>