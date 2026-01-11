<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir lógica de busca
require_once 'busca.php';

// Configurações
$API_KEY = 'CYBERCEL_API_v1_' . date('Ymd');
$RATE_LIMIT = 10; // Requisições por minuto

// Verificar chave API (opcional)
$headers = getallheaders();
$client_key = $headers['Authorization'] ?? $_GET['api_key'] ?? '';

// Rate limiting
session_start();
$ip = $_SERVER['REMOTE_ADDR'];
$current_time = time();
$requests = $_SESSION['requests'][$ip] ?? [];

// Limpar requisições antigas
$requests = array_filter($requests, function($time) use ($current_time) {
    return $time > $current_time - 60;
});

// Verificar limite
if (count($requests) >= $RATE_LIMIT) {
    http_response_code(429);
    echo json_encode([
        'status' => 'error',
        'message' => 'Rate limit exceeded. Max ' . $RATE_LIMIT . ' requests per minute.',
        'retry_after' => 60
    ]);
    exit();
}

// Adicionar requisição atual
$requests[] = $current_time;
$_SESSION['requests'][$ip] = $requests;

// Obter número
$numero = $_GET['numero'] ?? $_GET['phone'] ?? $_GET['celular'] ?? '';

// Se não tiver número, mostrar info da API
if (empty($numero)) {
    $host = $_SERVER['HTTP_HOST'];
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    
    echo json_encode([
        'api_name' => 'CYBERCEL API v1.0',
        'description' => 'API de busca de informações telefônicas',
        'endpoints' => [
            'GET' => [
                'busca_telefone' => $protocol . '://' . $host . '/api.php?numero=11999999999',
                'busca_completa' => $protocol . '://' . $host . '/api.php?numero=11999999999&full=true'
            ],
            'POST' => [
                'url' => $protocol . '://' . $host . '/api.php',
                'body' => ['numero' => '11999999999']
            ]
        ],
        'parameters' => [
            'numero' => 'Número com DDD (ex: 11999999999)',
            'full' => 'true/false - Retorna busca completa'
        ],
        'rate_limit' => $RATE_LIMIT . ' requests/minuto',
        'version' => '1.0.0',
        'author' => 'CYBERCEL API'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// Validar número
$numero = preg_replace('/[^0-9]/', '', $numero);
if (strlen($numero) < 10 || strlen($numero) > 11) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Número inválido. Use formato: DDD + Número (ex: 11999999999)',
        'received' => $numero,
        'expected_format' => 'DDD + 8 ou 9 dígitos'
    ]);
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
        'request_id' => uniqid('CYB_'),
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
    ]);
}
?>
