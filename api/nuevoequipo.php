<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'dbconexion.php'; // Archivo con la conexión a la base de datos

$data = json_decode(file_get_contents('php://input'), true);

$placa = trim($data['placa']);
$modelo = trim($data['modelo']);
$cliente = trim($data['cliente']);

// Validar que los campos no estén vacíos
if (empty($placa) || empty($modelo) || empty($cliente)) {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios.'
    ]);
    exit;
}

try {
    // Verificar si la placa ya existe
    $sql = "SELECT * FROM equipos WHERE placa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $placa);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'La placa ya está registrada.'
        ]);
        exit;
    }

    // Intentar insertar el nuevo equipo en la base de datos
    $sql = "INSERT INTO equipos (placa, modelo, codigo) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $placa, $modelo, $cliente);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Equipo registrado exitosamente.'
        ]);
    } else {
        throw new Exception('Error desconocido al registrar el equipo.');
    }
} catch (mysqli_sql_exception $e) {
    // Verificar si el error es por clave foránea
    if ($e->getCode() == 1452) { // Código de error para clave foránea violada
        echo json_encode([
            'success' => false,
            'message' => 'El código no existe.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    // Capturar errores generales
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

// Cerrar conexión
$conn->close();
?>
