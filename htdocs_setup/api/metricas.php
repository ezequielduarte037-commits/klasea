<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

// Verificar autenticación
verificarAutenticacion();

$pdo = getDBConnection();

try {
    // Obtener métricas de operaciones
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_operaciones,
            SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completadas,
            SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN estado = 'en_proceso' THEN 1 ELSE 0 END) as en_proceso,
            SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as canceladas
        FROM operaciones
    ");
    $stmt->execute();
    $operaciones = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener métricas por prioridad
    $stmt = $pdo->prepare("
        SELECT 
            prioridad,
            COUNT(*) as cantidad
        FROM operaciones 
        WHERE estado != 'cancelado'
        GROUP BY prioridad
    ");
    $stmt->execute();
    $por_prioridad = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener métricas por tipo
    $stmt = $pdo->prepare("
        SELECT 
            tipo,
            COUNT(*) as cantidad
        FROM operaciones 
        WHERE estado != 'cancelado'
        GROUP BY tipo
    ");
    $stmt->execute();
    $por_tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener métricas de los últimos 30 días
    $stmt = $pdo->prepare("
        SELECT 
            DATE(fecha_creacion) as fecha,
            COUNT(*) as operaciones_dia
        FROM operaciones 
        WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(fecha_creacion)
        ORDER BY fecha
    ");
    $stmt->execute();
    $tendencia_30_dias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular tiempo promedio de completado
    $stmt = $pdo->prepare("
        SELECT 
            AVG(DATEDIFF(fecha_completado, fecha_creacion)) as tiempo_promedio
        FROM operaciones 
        WHERE estado = 'completado' AND fecha_completado IS NOT NULL
    ");
    $stmt->execute();
    $tiempo_promedio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calcular eficiencia
    $eficiencia = 0;
    if ($operaciones['total_operaciones'] > 0) {
        $eficiencia = round(($operaciones['completadas'] / $operaciones['total_operaciones']) * 100, 1);
    }
    
    $response = [
        'success' => true,
        'data' => [
            'operaciones' => $operaciones,
            'por_prioridad' => $por_prioridad,
            'por_tipo' => $por_tipo,
            'tendencia_30_dias' => $tendencia_30_dias,
            'tiempo_promedio' => round($tiempo_promedio['tiempo_promedio'] ?: 0, 1),
            'eficiencia' => $eficiencia
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener métricas']);
}
?>