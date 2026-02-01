<?php
/**
 * API de Jugadores del Club Deportivo Andes
 * Endpoints para gestionar jugadores
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/db.php';

// Obtener método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obtener parámetros de consulta
$destacados = isset($_GET['destacados']) && $_GET['destacados'] === 'true';
$posicion = isset($_GET['posicion']) ? $_GET['posicion'] : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $conn = getDBConnection();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                // Obtener un jugador específico
                $query = "SELECT j.*, 
                         (SELECT COUNT(*) FROM partidos_jugadores pj WHERE pj.jugador_id = j.id) as partidos_jugados,
                         (SELECT SUM(pj.goles) FROM partidos_jugadores pj WHERE pj.jugador_id = j.id) as goles,
                         (SELECT SUM(pj.asistencias) FROM partidos_jugadores pj WHERE pj.jugador_id = j.id) as asistencias
                         FROM jugadores j
                         WHERE j.id = ?";
                $result = executeQuery($conn, $query, [$id]);
                
                if ($result && $result->num_rows > 0) {
                    $jugador = $result->fetch_assoc();
                    sendJSONResponse(['success' => true, 'data' => $jugador]);
                } else {
                    sendJSONResponse(['success' => false, 'message' => 'Jugador no encontrado'], 404);
                }
            } else {
                // Obtener lista de jugadores
                $query = "SELECT j.*, 
                         (SELECT COUNT(*) FROM partidos_jugadores pj WHERE pj.jugador_id = j.id) as partidos_jugados,
                         (SELECT SUM(pj.goles) FROM partidos_jugadores pj WHERE pj.jugador_id = j.id) as goles,
                         (SELECT SUM(pj.asistencias) FROM partidos_jugadores pj WHERE pj.jugador_id = j.id) as asistencias
                         FROM jugadores j";
                
                $params = [];
                $conditions = [];
                
                if ($posicion) {
                    $conditions[] = "j.posicion = ?";
                    $params[] = $posicion;
                }
                
                if ($destacados) {
                    $conditions[] = "j.destacado = 1";
                }
                
                if (!empty($conditions)) {
                    $query .= " WHERE " . implode(' AND ', $conditions);
                }
                
                $query .= " ORDER BY j.nombre ASC";
                
                if ($limit) {
                    $query .= " LIMIT ?";
                    $params[] = $limit;
                }
                
                $result = executeQuery($conn, $query, $params);
                
                if ($result) {
                    $jugadores = [];
                    while ($row = $result->fetch_assoc()) {
                        $jugadores[] = $row;
                    }
                    sendJSONResponse(['success' => true, 'data' => $jugadores]);
                } else {
                    sendJSONResponse(['success' => false, 'message' => 'Error al obtener jugadores'], 500);
                }
            }
            break;
            
        case 'POST':
            // Crear nuevo jugador
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validar campos requeridos
            if (!isset($input['nombre']) || !isset($input['posicion'])) {
                sendJSONResponse(['success' => false, 'message' => 'Faltan campos requeridos'], 400);
            }
            
            $query = "INSERT INTO jugadores (nombre, posicion, numero_camiseta, fecha_nacimiento, destacado, biografia) 
                     VALUES (?, ?, ?, ?, ?, ?)";
            
            $params = [
                $input['nombre'],
                $input['posicion'],
                $input['numero_camiseta'] ?? null,
                $input['fecha_nacimiento'] ?? null,
                $input['destacado'] ?? 0,
                $input['biografia'] ?? null
            ];
            
            $result = executeQuery($conn, $query, $params);
            
            if ($result) {
                $newId = $conn->insert_id;
                sendJSONResponse(['success' => true, 'message' => 'Jugador creado', 'data' => ['id' => $newId]], 201);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al crear jugador'], 500);
            }
            break;
            
        case 'PUT':
            // Actualizar jugador existente
            if (!$id) {
                sendJSONResponse(['success' => false, 'message' => 'ID de jugador requerido'], 400);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Construir query dinámico
            $fields = [];
            $params = [];
            
            if (isset($input['nombre'])) {
                $fields[] = "nombre = ?";
                $params[] = $input['nombre'];
            }
            if (isset($input['posicion'])) {
                $fields[] = "posicion = ?";
                $params[] = $input['posicion'];
            }
            if (isset($input['numero_camiseta'])) {
                $fields[] = "numero_camiseta = ?";
                $params[] = $input['numero_camiseta'];
            }
            if (isset($input['fecha_nacimiento'])) {
                $fields[] = "fecha_nacimiento = ?";
                $params[] = $input['fecha_nacimiento'];
            }
            if (isset($input['destacado'])) {
                $fields[] = "destacado = ?";
                $params[] = $input['destacado'];
            }
            if (isset($input['biografia'])) {
                $fields[] = "biografia = ?";
                $params[] = $input['biografia'];
            }
            
            if (empty($fields)) {
                sendJSONResponse(['success' => false, 'message' => 'No hay campos para actualizar'], 400);
            }
            
            $params[] = $id;
            $query = "UPDATE jugadores SET " . implode(', ', $fields) . " WHERE id = ?";
            
            $result = executeQuery($conn, $query, $params);
            
            if ($result) {
                sendJSONResponse(['success' => true, 'message' => 'Jugador actualizado']);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al actualizar jugador'], 500);
            }
            break;
            
        case 'DELETE':
            // Eliminar jugador
            if (!$id) {
                sendJSONResponse(['success' => false, 'message' => 'ID de jugador requerido'], 400);
            }
            
            $query = "DELETE FROM jugadores WHERE id = ?";
            $result = executeQuery($conn, $query, [$id]);
            
            if ($result) {
                sendJSONResponse(['success' => true, 'message' => 'Jugador eliminado']);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al eliminar jugador'], 500);
            }
            break;
            
        default:
            sendJSONResponse(['success' => false, 'message' => 'Método no permitido'], 405);
    }
    
    $conn->close();
} catch (Exception $e) {
    sendJSONResponse(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()], 500);
}
