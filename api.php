<?php
// CYBERCEL API v2.0 - DADOS REAIS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'busca.php';

// Rate limiting
session_start();
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$RATE_LIMIT = 30;
$current_time = time();
$requests = $_SESSION['requests'][$ip] ?? [];
$requests = array_filter($requests, function($time) use ($current_time) {
    return $time > $current_time - 60;
});

if (count($requests) >= $RATE_LIMIT) {
    http_response_code(429);
    echo json_encode([
        'status' => 'error',
        'message' => "Limite de $RATE_LIMIT requisições por minuto atingido.",
        'retry_after' => 60
    ], JSON_PRETTY_PRINT);
    exit();
}

$requests[] = $current_time;
$_SESSION['requests'][$ip] = $requests;

// Obter número
$numero = $_GET['numero'] ?? $_GET['phone'] ?? $_GET['celular'] ?? '';

if (empty($numero)) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    echo json_encode([
        'api' => 'CYBERCEL API v2.0',
        'status' => 'online',
        'version' => '2.0.0',
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoints' => [
            'GET' => "$protocol://$host/api.php?numero=11999999999",
            'POST' => "$protocol://$host/api.php"
        ],
        'features' => [
            'Operadora real por prefixo',
            'Localização por DDD oficial',
            'Validação Anatel',
            'Dados técnicos reais',
            'Consulta multi-fontes'
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// Validar número
$numero = preg_replace('/[^0-9]/', '', $numero);
if (strlen($numero) < 10 || strlen($numero) > 11) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Número inválido. Formato: DDD + Número (8 ou 9 dígitos)',
        'example' => '11999999999 (celular) ou 1133334444 (fixo)'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

$ddd = substr($numero, 0, 2);
$num = substr($numero, 2);

try {
    $full_search = isset($_GET['full']) && $_GET['full'] === 'true';
    $resultado = buscarInformacoesReais($ddd, $num, $full_search);
    
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro interno',
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>
