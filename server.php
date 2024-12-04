<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");


$servername="148.113.168.52";
$username="tsotecno_tsoadmin";
$password="Colombia-2024";
$port="3306";
$dbname="tsotecno_tso2024";
// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Error de conexión a la base de datos"]));
}

// Función para manejar el endpoint del login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/api/login') !== false) {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'] ?? null;
    $password = $data['password'] ?? null;

    if (!$username || !$password) {
        echo json_encode(["success" => false, "message" => "Faltan credenciales"]);
        exit;
    }

    $query = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $query->bind_param("ss", $username, $password);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => true, "message" => "Login successful"]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid username or password"]);
    }
    exit;
}

// Función para obtener equipos con paginación
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/api/equipos') !== false) {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Consulta para total de filas
    $countQuery = "SELECT COUNT(*) AS total FROM equipos";
    $countResult = $conn->query($countQuery);
    $totalRows = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRows / $limit);

    // Consulta para datos paginados
    $query = $conn->prepare("SELECT * FROM equipos LIMIT ? OFFSET ?");
    $query->bind_param("ii", $limit, $offset);
    $query->execute();
    $result = $query->get_result();

    $equipos = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode([
        "success" => true,
        "message" => "Get successful",
        "results" => $equipos,
        "totalPages" => $totalPages,
        "currentPage" => $page,
    ]);
    exit;
}

// Función para buscar equipos
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/api/equipo') !== false) {
    $searchQuery = $_GET['q'] ?? null;

    if (!$searchQuery) {
        echo json_encode(["success" => false, "message" => "Se requiere un término de búsqueda"]);
        exit;
    }

    $searchTerm = "%$searchQuery%";
    $query = $conn->prepare("SELECT * FROM equipos WHERE Nombre LIKE ? OR Placa LIKE ?");
    $query->bind_param("ss", $searchTerm, $searchTerm);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $equipos = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(["success" => true, "message" => "Búsqueda exitosa", "results" => $equipos]);
    } else {
        echo json_encode(["success" => false, "message" => "No se encontraron resultados", "results" => []]);
    }
    exit;
}

// Respuesta para solicitudes no manejadas
http_response_code(404);
echo json_encode(["success" => false, "message" => "Endpoint no encontrado"]);
