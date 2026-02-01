<?php
/**
 * API del Club Deportivo Andes
 * Endpoints para gestionar información del club
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/db.php';

// Obtener método HTTP
$method = $_SERVER['REQUEST_METHOD'];

try {
    $conn = getDBConnection();
    
    switch ($method) {
        case 'GET':
            // Obtener información del club
            $query = "SELECT * FROM club LIMIT 1";
            $result = executeQuery($conn, $query);
            
            if ($result && $result->num_rows > 0) {
                $club = $result->fetch_assoc();
                sendJSONResponse(['success' => true, 'data' => $club]);
            } else {
                // Si no hay datos del club, devolver información por defecto
                $defaultClub = [
                    'nombre' => 'Club Deportivo Andes',
                    'fundado' => '2026',
                    'estadio' => 'Estadio Andes',
                    'ciudad' => 'Santiago',
                    'pais' => 'Chile',
                    'colores' => 'Negro y Verde',
                    'descripcion' => 'Club Deportivo Andes: Pasión, esfuerzo y compromiso en cada partido.',
                    'email' => 'contacto@andes.cl',
                    'telefono' => '+56 9 1234 5678',
                    'sitio_web' => 'https://andes.cl'
                ];
                sendJSONResponse(['success' => true, 'data' => $defaultClub]);
            }
            break;
            
        case 'POST':
            // Crear información del club
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validar campos requeridos
            if (!isset($input['nombre'])) {
                sendJSONResponse(['success' => false, 'message' => 'Nombre del club requerido'], 400);
            }
            
            $query = "INSERT INTO club (nombre, fundado, estadio, ciudad, pais, colores, descripcion, email, telefono, sitio_web) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $input['nombre'],
                $input['fundado'] ?? null,
                $input['estadio'] ?? null,
                $input['ciudad'] ?? null,
                $input['pais'] ?? null,
                $input['colores'] ?? null,
                $input['descripcion'] ?? null,
                $input['email'] ?? null,
                $input['telefono'] ?? null,
                $input['sitio_web'] ?? null
            ];
            
            $result = executeQuery($conn, $query, $params);
            
            if ($result) {
                $newId = $conn->insert_id;
                sendJSONResponse(['success' => true, 'message' => 'Información del club creada', 'data' => ['id' => $newId]], 201);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al crear información del club'], 500);
            }
            break;
            
        case 'PUT':
            // Actualizar información del club
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Construir query dinámico
            $fields = [];
            $params = [];
            
            if (isset($input['nombre'])) {
                $fields[] = "nombre = ?";
                $params[] = $input['nombre'];
            }
            if (isset($input['fundado'])) {
                $fields[] = "fundado = ?";
                $params[] = $input['fundado'];
            }
            if (isset($input['estadio'])) {
                $fields[] = "estadio = ?";
                $params[] = $input['estadio'];
            }
            if (isset($input['ciudad'])) {
                $fields[] = "ciudad = ?";
                $params[] = $input['ciudad'];
            }
            if (isset($input['pais'])) {
                $fields[] = "pais = ?";
                $params[] = $input['pais'];
            }
            if (isset($input['colores'])) {
                $fields[] = "colores = ?";
                $params[] = $input['colores'];
            }
            if (isset($input['descripcion'])) {
                $fields[] = "descripcion = ?";
                $params[] = $input['descripcion'];
            }
            if (isset($input['email'])) {
                $fields[] = "email = ?";
                $params[] = $input['email'];
            }
            if (isset($input['telefono'])) {
                $fields[] = "telefono = ?";
                $params[] = $input['telefono'];
            }
            if (isset($input['sitio_web'])) {
                $fields[] = "sitio_web = ?";
                $params[] = $input['sitio_web'];
            }
            
            if (empty($fields)) {
                sendJSONResponse(['success' => false, 'message' => 'No hay campos para actualizar'], 400);
            }
            
            $query = "UPDATE club SET " . implode(', ', $fields) . " WHERE id = (SELECT id FROM club LIMIT 1)";
            
            $result = executeQuery($conn, $query, $params);
            
            if ($result) {
                sendJSONResponse(['success' => true, 'message' => 'Información del club actualizada']);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al actualizar información del club'], 500);
            }
            break;
            
        case 'DELETE':
            // Eliminar información del club
            $query = "DELETE FROM club";
            $result = executeQuery($conn, $query);
            
            if ($result) {
                sendJSONResponse(['success' => true, 'message' => 'Información del club eliminada']);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al eliminar información del club'], 500);
            }
            break;
            
        default:
            sendJSONResponse(['success' => false, 'message' => 'Método no permitido'], 405);
    }
    
    $conn->close();
} catch (Exception $e) {
    sendJSONResponse(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()], 500);
}
