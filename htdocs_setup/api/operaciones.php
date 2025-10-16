<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

// Verificar autenticación
verificarAutenticacion();

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        // Obtener operaciones
        try {
            $stmt = $pdo->prepare("
                SELECT o.*, u.nombre as usuario_nombre 
                FROM operaciones o 
                LEFT JOIN usuarios u ON o.usuario_asignado = u.id 
                ORDER BY o.fecha_creacion DESC
            ");
            $stmt->execute();
            $operaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'data' => $operaciones]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener operaciones']);
        }
        break;
        
    case 'POST':
        // Crear nueva operación
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare("
                INSERT INTO operaciones (titulo, descripcion, tipo, estado, prioridad, usuario_asignado, fecha_vencimiento) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['descripcion'],
                $data['tipo'],
                $data['estado'],
                $data['prioridad'],
                $data['usuario_asignado'] ?: null,
                $data['fecha_vencimiento'] ?: null
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Operación creada correctamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al crear operación']);
        }
        break;
        
    case 'PUT':
        // Actualizar operación
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'];
            
            $stmt = $pdo->prepare("
                UPDATE operaciones 
                SET titulo = ?, descripcion = ?, tipo = ?, estado = ?, prioridad = ?, 
                    usuario_asignado = ?, fecha_vencimiento = ?, comentarios = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['descripcion'],
                $data['tipo'],
                $data['estado'],
                $data['prioridad'],
                $data['usuario_asignado'] ?: null,
                $data['fecha_vencimiento'] ?: null,
                $data['comentarios'],
                $id
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Operación actualizada correctamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar operación']);
        }
        break;
        
    case 'DELETE':
        // Eliminar operación
        try {
            $id = $_GET['id'];
            
            $stmt = $pdo->prepare("DELETE FROM operaciones WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Operación eliminada correctamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar operación']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        break;
}
?>