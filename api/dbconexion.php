<?php
$servername="148.113.168.52";
$username="tsotecno_tsoadmin";
$password="Colombia-2024";
$port="3306";
$dbname="tsotecno_tso2024";
// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si hay errores en la conexión
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}