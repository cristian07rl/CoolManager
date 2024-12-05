<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'dbconexion.php';

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    echo json_encode(["success" => false, "message" => "Token no proporcionado"]);
    exit;
}

// Extraer el token del encabezado
$authHeader = $headers['Authorization'];
$token = str_replace('Bearer ', '', $authHeader);

// Verificar el token en la base de datos
$sql = "SELECT s.id, s.user_id, u.username, s.expires_at 
        FROM sessions s 
        INNER JOIN users u ON s.user_id = u.id 
        WHERE s.token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $session = $result->fetch_assoc();

    // Verificar si el token ha expirado
    if (strtotime($session['expires_at']) < time()) {
        echo json_encode(["success" => false, "message" => "Token expirado"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Token válido",
        "user" => [
            "id" => $session['user_id'],
            "username" => $session['username']
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Token inválido"]);
}

$conn->close();
?>
