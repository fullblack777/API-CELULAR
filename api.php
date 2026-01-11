<?php
// CYBERCEL API v2.0 - DADOS REAIS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir busca.php
if (!@include_once('busca.php')) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao carregar módulo de busca'
    ], JSON_PRETTY_PRINT);
    exit();
}

// Rate limiting simples
$RATE_LIMIT = 30;
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Obter número
$numero = $_GET['numero'] ?? $_GET['phone'] ?? $_GET['celular'] ?? '';

// Se não tiver número, mostrar info
if (empty($numero)) {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    echo json_encode([
        'api' => 'CYBERCEL API v2.0',
        'status' => 'online',
        'version' => '2.0.0',
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoint' => $protocol . '://' . $host . '/api.php?numero=11999999999',
        'exemplo' => $protocol . '://' . $host . '/api.php?numero=11999999999&full=true',
        'rate_limit' => $RATE_LIMIT . ' requests/min'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// Validar número
$numero = preg_replace('/[^0-9]/', '', $numero);
if (strlen($numero) < 10 || strlen($numero) > 11) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Número inválido. Use DDD + 8 ou 9 dígitos',
        'exemplo' => '11999999999 (celular) ou 1133334444 (fixo)'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

$ddd = substr($numero, 0, 2);
$num = substr($numero, 2);

try {
    $full_search = isset($_GET['full']) && $_GET['full'] === 'true';
    
    // Verificar se função existe
    if (!function_exists('buscarInformacoesReais')) {
        throw new Exception('Função de busca não disponível');
    }
    
    $resultado = buscarInformacoesReais($ddd, $num, $full_search);
    
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro na busca: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>
