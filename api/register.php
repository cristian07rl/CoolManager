<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'dbconexion.php'; // Archivo con la conexión a la base de datos

$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username']);
$email = trim($data['email']);
$password = trim($data['password']);

// Validar que los campos no estén vacíos
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios.'
    ]);
    exit;
}

// Validar formato de correo
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'El correo electrónico no es válido.'
    ]);
    exit;
}

// Verificar si el usuario ya existe
$sql = "SELECT * FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'El nombre de usuario o correo ya está registrado.'
    ]);
    exit;
}

// Hash de la contraseña
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insertar el nuevo usuario en la base de datos
$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Usuario registrado exitosamente.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar el usuario.'
    ]);
}

$conn->close();
?>
