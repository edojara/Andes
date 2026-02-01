<?php
/**
 * API de Contacto del Club Deportivo Andes
 * Endpoint para procesar el formulario de contacto
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/db.php';

// Obtener método HTTP
$method = $_SERVER['REQUEST_METHOD'];

try {
    $conn = getDBConnection();
    
    switch ($method) {
        case 'POST':
            // Procesar formulario de contacto
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $asunto = $_POST['asunto'] ?? '';
            $mensaje = $_POST['mensaje'] ?? '';
            
            // Validar campos requeridos
            if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
                sendJSONResponse(['success' => false, 'message' => 'Todos los campos son requeridos'], 400);
            }
            
            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                sendJSONResponse(['success' => false, 'message' => 'Email inválido'], 400);
            }
            
            // Sanitizar inputs
            $nombre = sanitizeInput($nombre);
            $email = sanitizeInput($email);
            $asunto = sanitizeInput($asunto);
            $mensaje = sanitizeInput($mensaje);
            
            // Guardar en base de datos (opcional - crear tabla de mensajes si se desea)
            // Por ahora, solo enviar email
            
            // Enviar email
            $to = 'contacto@andes.cl';
            $subject = "Contacto Web: $asunto";
            $headers = "From: $email\r\n";
            $headers .= "Reply-To: $email\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            $emailBody = "Nombre: $nombre\n";
            $emailBody .= "Email: $email\n";
            $emailBody .= "Asunto: $asunto\n\n";
            $emailBody .= "Mensaje:\n$mensaje\n";
            
            if (mail($to, $subject, $emailBody, $headers)) {
                sendJSONResponse(['success' => true, 'message' => 'Mensaje enviado correctamente']);
            } else {
                sendJSONResponse(['success' => false, 'message' => 'Error al enviar el mensaje'], 500);
            }
            break;
            
        default:
            sendJSONResponse(['success' => false, 'message' => 'Método no permitido'], 405);
    }
    
    $conn->close();
} catch (Exception $e) {
    sendJSONResponse(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()], 500);
}
