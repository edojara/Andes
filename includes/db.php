<?php
/**
 * Conexión a la base de datos del Club Deportivo
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'lamp_user');
define('DB_PASS', 'lamp_password');
define('DB_NAME', 'lamp_test');
define('DB_CHARSET', 'utf8mb4');

/**
 * Obtiene la conexión a la base de datos
 * @return mysqli|null Conexión a la base de datos o null en caso de error
 */
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if ($conn->connect_error) {
        error_log("Error de conexión a la base de datos: " . $conn->connect_error);
        return null;
    }
    
    // Establecer charset
    $conn->set_charset(DB_CHARSET);
    
    return $conn;
}

/**
 * Ejecuta una consulta preparada de forma segura
 * @param mysqli $conn Conexión a la base de datos
 * @param string $query Consulta SQL con placeholders (?)
 * @param array $params Parámetros para la consulta
 * @return mysqli_result|false Resultado de la consulta o false en caso de error
 */
function executeQuery($conn, $query, $params = []) {
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        error_log("Error al preparar la consulta: " . $conn->error);
        return false;
    }
    
    // Determinar tipos de parámetros
    $types = '';
    foreach ($params as $param) {
        if (is_int($param)) {
            $types .= 'i';
        } elseif (is_float($param)) {
            $types .= 'd';
        } elseif (is_string($param)) {
            $types .= 's';
        } else {
            $types .= 'b';
        }
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Error al ejecutar la consulta: " . $stmt->error);
        return false;
    }
    
    $result = $stmt->get_result();
    $stmt->close();
    
    return $result;
}

/**
 * Escapa una cadena para usar en HTML
 * @param string $string Cadena a escapar
 * @return string Cadena escapada
 */
function escapeHTML($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Envía una respuesta JSON
 * @param mixed $data Datos a enviar
 * @param int $code Código HTTP (default: 200)
 */
function sendJSONResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Envía una respuesta de error JSON
 * @param string $message Mensaje de error
 * @param int $code Código HTTP (default: 400)
 */
function sendErrorResponse($message, $code = 400) {
    sendJSONResponse([
        'success' => false,
        'message' => $message
    ], $code);
}

/**
 * Envía una respuesta de éxito JSON
 * @param mixed $data Datos a enviar
 * @param string $message Mensaje de éxito
 */
function sendSuccessResponse($data, $message = 'Operación exitosa') {
    sendJSONResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

/**
 * Verifica si el método de la petición es el correcto
 * @param string $method Método esperado (GET, POST, PUT, DELETE)
 * @return bool True si el método es correcto
 */
function checkRequestMethod($method) {
    return $_SERVER['REQUEST_METHOD'] === $method;
}

/**
 * Obtiene el cuerpo de la petición JSON
 * @return array|null Datos decodificados o null
 */
function getJSONInput() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error al decodificar JSON: " . json_last_error_msg());
        return null;
    }
    
    return $data;
}

/**
 * Valida y sanitiza un campo de texto
 * @param string $field Campo a validar
 * @param int $minLength Longitud mínima
 * @param int $maxLength Longitud máxima
 * @return string|null Campo validado o null
 */
function validateTextField($field, $minLength = 1, $maxLength = 255) {
    if (!isset($field) || empty(trim($field))) {
        return null;
    }
    
    $value = trim($field);
    
    if (strlen($value) < $minLength || strlen($value) > $maxLength) {
        return null;
    }
    
    return $value;
}

/**
 * Valida un email
 * @param string $email Email a validar
 * @return string|null Email validado o null
 */
function validateEmail($email) {
    if (!isset($email) || empty(trim($email))) {
        return null;
    }
    
    $email = trim($email);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return null;
    }
    
    return $email;
}

/**
 * Valida un número de teléfono
 * @param string $phone Teléfono a validar
 * @return string|null Teléfono validado o null
 */
function validatePhone($phone) {
    if (!isset($phone) || empty(trim($phone))) {
        return null;
    }
    
    $phone = preg_replace('/[^0-9]/', '', trim($phone));
    
    if (strlen($phone) < 8 || strlen($phone) > 15) {
        return null;
    }
    
    return $phone;
}
?>