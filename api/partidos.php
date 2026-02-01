<?php
/**
 * API de Partidos del Club Deportivo Andes
 * Endpoints para gestionar partidos
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/db.php';

// Obtener método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obtener parámetros de consulta
$proximos = isset($_GET['proximos']) && $_GET['proximos'] === 'true';
$pasados = isset($_GET['pasados']) && $_GET['pasados'] === 'true';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $conn = getDBConnection();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                // Obtener un partido específico
                $query = "SELECT p.*, 
                         GROUP_CONCAT(CONCAT(j.nombre, ' (', pj.minutos_jugados, ' min)') SEPARATOR ', ') as jugadores
                         FROM partidos p
                         LEFT JOIN partidos_jugadores pj ON p.id = pj.partido_id
                         LEFT JOIN jugadores j ON pj.jugador_id = j.id
                         WHERE p.id = ?
                         GROUP BY p.id";
                $result = executeQuery($conn, $query, [$id]);
                
                if ($result && $result->num_rows > 0) {
                    $partido = $result->fetch_assoc();
                    sendJSONResponse(['success' => true, 'data' => $partido]);
                } else {
                    sendJSONResponse(['success' => false, 'message' => 'Partido no encontrado'], 404);
                }
            } else {
                // Obtener lista de partidos
                $query = "SELECT p.*, 
                         (SELECT COUNT(*) FROM fotos f WHERE f.partido_id = p.id) as num_fotos
                         FROM partidos p";
                
                $params = [];
                
                if ($proximos) {
                    $query .= " WHERE p.fecha >= NOW() ORDER BY p.fecha ASC";
                } elseif ($pasados) {
                    $query .= " WHERE p.fecha < NOW() ORDER BY p.fecha DESC";
                } else {
                    $query .= " ORDER BY p.fecha DESC";
                }
                
                if ($limit) {
                    $query .= " LIMIT ?";
                    $params[] = $limit;
                }
                
                $result = executeQuery($conn, $query, $params);
                
                if ($result) {
                    $partidos = [];
                    while ($row = $result->fetch_assoc()) {
                        $partidos[] = $row;
                    }
                    sendJSONResponse(['success' => true, 'data' => $partidos]);
                } else {
                    sendJSONResponse(['success' => false, 'message' => 'Error al obtener partidos'], 500);
                }
            }
            break;
            
        case 'POST':
            // Crear nuevo partido
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validar campos requeridos
            if (!isset($input['rival']) || !isset($input['fecha']) || !isset($input['lugar'])) {
                sendJSONResponse(['success' => false, 'message' => 'Faltan campos requeridos'], 400);
            }
            
            $query = "INSERT INTO partidos (rival, fecha, lugar, competicion, goles_local, goles_visitante, notas) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $input['rival'],
                $input['fecha'],
                $input['lugar'],
                $input['competicion'] ?? null,
                $input['goles_local'] ?? null,
                $input['goles_visitante'] ?? null,
                $input['notas'] ?? null
            ];
            
            $result = executeQuery($conn, $query, $params);
            
            if ($result) {
                $newId = $conn->insert_id;
                sendJSONResponse(['success' => true, 'message' => 'Partido creado', 'data' => ['id' => $newId]], 201);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al crear partido'], 500);
            }
            break;
            
        case 'PUT':
            // Actualizar partido existente
            if (!$id) {
                sendJSONResponse(['success' => false, 'message' => 'ID de partido requerido'], 400);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Construir query dinámico
            $fields = [];
            $params = [];
            
            if (isset($input['rival'])) {
                $fields[] = "rival = ?";
                $params[] = $input['rival'];
            }
            if (isset($input['fecha'])) {
                $fields[] = "fecha = ?";
                $params[] = $input['fecha'];
            }
            if (isset($input['lugar'])) {
                $fields[] = "lugar = ?";
                $params[] = $input['lugar'];
            }
            if (isset($input['competicion'])) {
                $fields[] = "competicion = ?";
                $params[] = $input['competicion'];
            }
            if (isset($input['goles_local'])) {
                $fields[] = "goles_local = ?";
                $params[] = $input['goles_local'];
            }
            if (isset($input['goles_visitante'])) {
                $fields[] = "goles_visitante = ?";
                $params[] = $input['goles_visitante'];
            }
            if (isset($input['notas'])) {
                $fields[] = "notas = ?";
                $params[] = $input['notas'];
            }
            
            if (empty($fields)) {
                sendJSONResponse(['success' => false, 'message' => 'No hay campos para actualizar'], 400);
            }
            
            $params[] = $id;
            $query = "UPDATE partidos SET " . implode(', ', $fields) . " WHERE id = ?";
            
            $result = executeQuery($conn, $query, $params);
            
            if ($result) {
                sendJSONResponse(['success' => true, 'message' => 'Partido actualizado']);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al actualizar partido'], 500);
            }
            break;
            
        case 'DELETE':
            // Eliminar partido
            if (!$id) {
                sendJSONResponse(['success' => false, 'message' => 'ID de partido requerido'], 400);
            }
            
            $query = "DELETE FROM partidos WHERE id = ?";
            $result = executeQuery($conn, $query, [$id]);
            
            if ($result) {
                sendJSONResponse(['success' => true, 'message' => 'Partido eliminado']);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al eliminar partido'], 500);
            }
            break;
            
        default:
            sendJSONResponse(['success' => false, 'message' => 'Método no permitido'], 405);
    }
    
    $conn->close();
} catch (Exception $e) {
    sendJSONResponse(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()], 500);
}
