<?php
$servername="localhost";
$username="root";
$password="mysqldb";
$port="3306";
$dbname="login_db";
// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si hay errores en la conexión
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}