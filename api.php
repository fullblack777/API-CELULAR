<?php
// CYBERCEL API v1.0
// Headers CORS - FAZER VIA PHP (não .htaccess)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir lógica de busca
require_once 'busca.php';

// Rate limiting
session_start();
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$RATE_LIMIT = 20;
$current_time = time();
$requests = $_SESSION['requests'][$ip] ?? [];

// Limpar requisições antigas (último minuto)
$requests = array_filter($requests, function($time) use ($current_time) {
    return $time > $current_time - 60;
});

// Verificar limite
if (count($requests) >= $RATE_LIMIT) {
    http_response_code(429);
    echo json_encode([
        'status' => 'error',
        'message' => "Rate limit exceeded. Max $RATE_LIMIT requests per minute.",
        'retry_after' => 60
    ], JSON_PRETTY_PRINT);
    exit();
}

// Adicionar requisição atual
$requests[] = $current_time;
$_SESSION['requests'][$ip] = $requests;

// Obter número
$numero = $_GET['numero'] ?? $_GET['phone'] ?? $_GET['celular'] ?? '';

// Se não tiver número, mostrar info da API
if (empty($numero)) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    echo json_encode([
        'api_name' => 'CYBERCEL API v1.0',
        'description' => 'API de busca de informações telefônicas',
        'version' => '1.0.0',
        'status' => 'online',
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoints' => [
            'GET' => [
                'busca_basica' => "$protocol://$host/api.php?numero=11999999999",
                'busca_completa' => "$protocol://$host/api.php?numero=11999999999&full=true"
            ]
        ],
        'parameters' => [
            'numero' => 'Número com DDD (ex: 11999999999)',
            'full' => 'true/false - Retorna todos os dados disponíveis'
        ],
        'rate_limit' => "$RATE_LIMIT requests/minuto",
        'author' => 'CYBERCEL API',
        'documentation' => "$protocol://$host/"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// Validar número
$numero = preg_replace('/[^0-9]/', '', $numero);
if (strlen($numero) < 10 || strlen($numero) > 11) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Número inválido. Use formato: DDD + Número (8 ou 9 dígitos)',
        'received' => $numero,
        'example' => '11999999999 ou 1133334444'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// Extrair DDD e número
$ddd = substr($numero, 0, 2);
$num = substr($numero, 2);

// Buscar informações
try {
    $full_search = isset($_GET['full']) && $_GET['full'] === 'true';
    $resultado = buscarInformacoesTelefone($ddd, $num, $full_search);
    
    // Adicionar metadata
    $resultado['metadata'] = [
        'api' => 'CYBERCEL',
        'version' => '1.0',
        'timestamp' => date('Y-m-d H:i:s'),
        'request_id' => 'CYB_' . uniqid(),
        'processing_time' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3) . 's'
    ];
    
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro interno na busca',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>
