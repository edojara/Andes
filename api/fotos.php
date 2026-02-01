<?php
/**
 * API de Fotos del Club Deportivo Andes
 * Endpoints para gestionar fotos
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/db.php';

// Obtener método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obtener parámetros de consulta
$partidoId = isset($_GET['partido_id']) ? (int)$_GET['partido_id'] : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $conn = getDBConnection();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                // Obtener una foto específica
                $query = "SELECT f.*, p.rival, p.fecha as partido_fecha 
                         FROM fotos f
                         LEFT JOIN partidos p ON f.partido_id = p.id
                         WHERE f.id = ?";
                $result = executeQuery($conn, $query, [$id]);
                
                if ($result && $result->num_rows > 0) {
                    $foto = $result->fetch_assoc();
                    sendJSONResponse(['success' => true, 'data' => $foto]);
                } else {
                    sendJSONResponse(['success' => false, 'message' => 'Foto no encontrada'], 404);
                }
            } else {
                // Obtener lista de fotos
                $query = "SELECT f.*, p.rival, p.fecha as partido_fecha 
                         FROM fotos f
                         LEFT JOIN partidos p ON f.partido_id = p.id";
                
                $params = [];
                
                if ($partidoId) {
                    $query .= " WHERE f.partido_id = ?";
                    $params[] = $partidoId;
                }
                
                $query .= " ORDER BY f.fecha_subida DESC";
                
                if ($limit) {
                    $query .= " LIMIT ?";
                    $params[] = $limit;
                }
                
                $result = executeQuery($conn, $query, $params);
                
                if ($result) {
                    $fotos = [];
                    while ($row = $result->fetch_assoc()) {
                        $fotos[] = $row;
                    }
                    sendJSONResponse(['success' => true, 'data' => $fotos]);
                } else {
                    sendJSONResponse(['success' => false, 'message' => 'Error al obtener fotos'], 500);
                }
            }
            break;
            
        case 'POST':
            // Crear nueva foto
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validar campos requeridos
            if (!isset($input['url'])) {
                sendJSONResponse(['success' => false, 'message' => 'URL de la foto requerida'], 400);
            }
            
            $query = "INSERT INTO fotos (url, descripcion, partido_id) 
                     VALUES (?, ?, ?)";
            
            $params = [
                $input['url'],
                $input['descripcion'] ?? null,
                $input['partido_id'] ?? null
            ];
            
            $result = executeQuery($conn, $query, $params);
            
            if ($result) {
                $newId = $conn->insert_id;
                sendJSONResponse(['success' => true, 'message' => 'Foto creada', 'data' => ['id' => $newId]], 201);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al crear foto'], 500);
            }
            break;
            
        case 'PUT':
            // Actualizar foto existente
            if (!$id) {
                sendJSONResponse(['success' => false, 'message' => 'ID de foto requerido'], 400);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Construir query dinámico
            $fields = [];
            $params = [];
            
            if (isset($input['url'])) {
                $fields[] = "url = ?";
                $params[] = $input['url'];
            }
            if (isset($input['descripcion'])) {
                $fields[] = "descripcion = ?";
                $params[] = $input['descripcion'];
            }
            if (isset($input['partido_id'])) {
                $fields[] = "partido_id = ?";
                $params[] = $input['partido_id'];
            }
            
            if (empty($fields)) {
                sendJSONResponse(['success' => false, 'message' => 'No hay campos para actualizar'], 400);
            }
            
            $params[] = $id;
            $query = "UPDATE fotos SET " . implode(', ', $fields) . " WHERE id = ?";
            
            $result = executeQuery($conn, $query, $params);
            
            if ($result) {
                sendJSONResponse(['success' => true, 'message' => 'Foto actualizada']);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al actualizar foto'], 500);
            }
            break;
            
        case 'DELETE':
            // Eliminar foto
            if (!$id) {
                sendJSONResponse(['success' => false, 'message' => 'ID de foto requerido'], 400);
            }
            
            $query = "DELETE FROM fotos WHERE id = ?";
            $result = executeQuery($conn, $query, [$id]);
            
            if ($result) {
                sendJSONResponse(['success' => true, 'message' => 'Foto eliminada']);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al eliminar foto'], 500);
            }
            break;
            
        default:
            sendJSONResponse(['success' => false, 'message' => 'Método no permitido'], 405);
    }
    
    $conn->close();
} catch (Exception $e) {
    sendJSONResponse(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()], 500);
}
