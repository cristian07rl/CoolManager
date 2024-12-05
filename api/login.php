<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'dbconexion.php'; // Archivo con la conexión a la base de datos

// Verificar si los datos llegaron correctamente
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Parámetros incompletos"
    ]);
    exit;
}

$username = mysqli_real_escape_string($conn, $data['username']);
$password = $data['password'];

// Buscar al usuario en la base de datos
$sql = "SELECT id, username, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verificar la contraseña utilizando password_verify
    if (password_verify($password, $user['password'])) {
        // Generar un token de sesión
        $token = bin2hex(random_bytes(32));
        $tokenExpiry = time() + (60 * 60); // Expira en 1 hora

        // Guardar el token en la base de datos
        $sqlToken = "INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmtToken = $conn->prepare($sqlToken);
        $tokenExpiry = time() + 3600; // Timestamp de expiración (ahora + 1 hora)
        $expiryDateTime = date('Y-m-d H:i:s', $tokenExpiry); // Formato de fecha y hora
        $stmtToken->bind_param("iss", $user['id'], $token, $expiryDateTime);
        $stmtToken->execute();

        echo json_encode([
            "success" => true,
            "message" => "Inicio de sesión exitoso",
            "token" => $token,
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Usuario o contraseña incorrectos"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Usuario o contraseña incorrectos"
    ]);
}

$conn->close();
?>
