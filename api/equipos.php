<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'dbconexion.php';

// Obtener parámetros
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Verifica si se ha recibido el parámetro "q" para realizar una búsqueda
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['q']); // Escapa el término de búsqueda para evitar inyecciones SQL

    // Consulta SQL para buscar en la tabla equipos, usando LIKE para búsqueda parcial
    // Añadimos LIMIT y OFFSET a la consulta de búsqueda
    $sql = "SELECT * FROM MB1L50 WHERE placa LIKE '%$searchQuery%' 
            OR modelo LIKE '%$searchQuery%' 
            OR ruta LIKE '%$searchQuery%' 
            OR nombre LIKE '%$searchQuery%' 
            OR codigo LIKE '%$searchQuery%' 
            OR municipio LIKE '%$searchQuery%' 
            OR barrio LIKE '%$searchQuery%' 
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $equipos = [];
        while ($row = $result->fetch_assoc()) {
            $equipos[] = $row; // Agrega cada fila al array de resultados
        }

        // Total de equipos encontrados para la búsqueda
        $totalQuery = "SELECT COUNT(*) AS total FROM MB1L50 WHERE placa LIKE '%$searchQuery%' 
                       OR modelo LIKE '%$searchQuery%' 
                       OR ruta LIKE '%$searchQuery%' 
                       OR nombre LIKE '%$searchQuery%' 
                       OR codigo LIKE '%$searchQuery%' 
                       OR municipio LIKE '%$searchQuery%' 
                       OR barrio LIKE '%$searchQuery%'";
        $totalResult = $conn->query($totalQuery);
        $total = $totalResult->fetch_assoc()['total'];

        echo json_encode([
            'success' => true,
            'message' => 'Búsqueda exitosa',
            'results' => $equipos,
            'totalPages' => ceil($total / $limit),
            'currentPage' => $page,
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron resultados',
            'results' => [],
        ]);
    }
} else {
    // Si no se recibe el parámetro de búsqueda, devuelve todos los resultados con paginación
    $sql = "SELECT * FROM MB1L50 LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $equipos = [];
    while ($row = $result->fetch_assoc()) {
        $equipos[] = $row;
    }

    // Total de equipos
    $totalQuery = "SELECT COUNT(*) AS total FROM MB1L50";
    $totalResult = $conn->query($totalQuery);
    $total = $totalResult->fetch_assoc()['total'];

    echo json_encode([
        'success' => true,
        'results' => $equipos,
        'totalPages' => ceil($total / $limit),
        'currentPage' => $page,
    ]);
}

$conn->close();
?>
