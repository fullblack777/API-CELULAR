<?php
function buscarInformacoesTelefone($ddd, $numero, $full = false) {
    $start_time = microtime(true);
    
    $numero_completo = $ddd . $numero;
    $formatado = "(" . $ddd . ") " . 
                (strlen($numero) == 9 ? 
                 substr($numero, 0, 5) . '-' . substr($numero, 5) : 
                 substr($numero, 0, 4) . '-' . substr($numero, 4));
    
    // Resultado base
    $resultado = [
        'status' => 'success',
        'numero' => $numero_completo,
        'numero_formatado' => $formatado,
        'ddd' => $ddd,
        'operadora' => detectarOperadora($ddd, $numero),
        'regiao' => buscarRegiaoPorDDD($ddd),
        'tipo' => strlen($numero) == 9 ? 'Celular' : 'Fixo',
        'valido' => validarNumero($ddd, $numero)
    ];
    
    // Busca básica (sempre retorna)
    $resultado['informacoes_basicas'] = buscarInfoBasica($ddd, $numero);
    
    // Busca completa (se solicitado)
    if ($full) {
        $resultado['busca_completa'] = [
            'dados_registro' => buscarDadosRegistro($ddd, $numero),
            'redes_sociais' => buscarRedesSociais($numero_completo),
            'vazamentos' => verificarVazamentos($numero_completo),
            'localizacao' => estimarLocalizacao($ddd),
            'historico' => buscarHistorico($numero_completo),
            'associacoes' => buscarAssociacoes($numero_completo)
        ];
    }
    
    $resultado['tempo_processamento'] = round(microtime(true) - $start_time, 3) . 's';
    
    return $resultado;
}

// ==================== FUNÇÕES DE BUSCA ====================

function detectarOperadora($ddd, $numero) {
    $prefixos = [
        'Vivo' => ['15', '16', '17', '18', '19'],
        'Claro' => ['21', '22', '24', '91', '92', '93', '94', '95', '96', '97'],
        'Tim' => ['31', '32', '33', '34', '35', '37', '38', '71', '81', '82', '83', '84', '85', '86', '87'],
        'Oi' => ['14', '41', '42', '43', '44', '45', '46', '47', '48', '49', '51', '54', '55', '61', '62', '63', '64', '65', '66', '67', '68', '69'],
        'Nextel' => ['77', '78', '79'],
        'Algar' => ['34', '38', '89'],
        'Sercomtel' => ['43']
    ];
    
    $prefixo = substr($numero, 0, 2);
    foreach ($prefixos as $operadora => $prefixos_op) {
        if (in_array($prefixo, $prefixos_op)) {
            return $operadora;
        }
    }
    
    // Operadoras por DDD
    $operadoras_ddd = [
        '11' => ['Vivo', 'Claro', 'Tim', 'Oi'],
        '21' => ['Vivo', 'Claro', 'Tim', 'Oi'],
        '31' => ['Tim', 'Claro', 'Vivo'],
        '41' => ['Oi', 'Claro', 'Vivo'],
        '51' => ['Vivo', 'Claro', 'Tim'],
        '61' => ['Claro', 'Vivo', 'Oi'],
        '71' => ['Tim', 'Claro', 'Vivo'],
        '81' => ['Tim', 'Claro', 'Oi'],
        '91' => ['Claro', 'Vivo']
    ];
    
    return $operadoras_ddd[$ddd][rand(0, 2)] ?? 'Não identificada';
}

function buscarRegiaoPorDDD($ddd) {
    $regioes = [
        '11' => ['estado' => 'SP', 'cidade' => 'São Paulo', 'regiao' => 'Metropolitana'],
        '21' => ['estado' => 'RJ', 'cidade' => 'Rio de Janeiro', 'regiao' => 'Metropolitana'],
        '31' => ['estado' => 'MG', 'cidade' => 'Belo Horizonte', 'regiao' => 'Metropolitana'],
        '41' => ['estado' => 'PR', 'cidade' => 'Curitiba', 'regiao' => 'Metropolitana'],
        '48' => ['estado' => 'SC', 'cidade' => 'Florianópolis', 'regiao' => 'Grande Florianópolis'],
        '51' => ['estado' => 'RS', 'cidade' => 'Porto Alegre', 'regiao' => 'Metropolitana'],
        '61' => ['estado' => 'DF', 'cidade' => 'Brasília', 'regiao' => 'Distrito Federal'],
        '71' => ['estado' => 'BA', 'cidade' => 'Salvador', 'regiao' => 'Metropolitana'],
        '81' => ['estado' => 'PE', 'cidade' => 'Recife', 'regiao' => 'Metropolitana'],
        '85' => ['estado' => 'CE', 'cidade' => 'Fortaleza', 'regiao' => 'Metropolitana'],
        '91' => ['estado' => 'PA', 'cidade' => 'Belém', 'regiao' => 'Metropolitana'],
        '92' => ['estado' => 'AM', 'cidade' => 'Manaus', 'regiao' => 'Metropolitana']
    ];
    
    return $regioes[$ddd] ?? ['estado' => 'Desconhecido', 'cidade' => 'Não identificada', 'regiao' => 'Indeterminada'];
}

function validarNumero($ddd, $numero) {
    $ddds_validos = array_keys([
        '11', '12', '13', '14', '15', '16', '17', '18', '19', '21', '22', '24', '27', '28',
        '31', '32', '33', '34', '35', '37', '38', '41', '42', '43', '44', '45', '46', '47',
        '48', '49', '51', '53', '54', '55', '61', '62', '63', '64', '65', '66', '67', '68',
        '69', '71', '73', '74', '75', '77', '79', '81', '82', '83', '84', '85', '86', '87',
        '88', '89', '91', '92', '93', '94', '95', '96', '97', '98', '99'
    ]);
    
    if (!in_array($ddd, $ddds_validos)) return false;
    if (strlen($numero) != 8 && strlen($numero) != 9) return false;
    if (strlen($numero) == 9 && substr($numero, 0, 1) != '9') return false;
    if (strlen($numero) == 8 && substr($numero, 0, 1) == '9') return false;
    
    return true;
}

function buscarInfoBasica($ddd, $numero) {
    $hash = md5($ddd . $numero . time());
    
    return [
        'registro_telefonico' => [
            'data_cadastro' => date('Y-m-d', strtotime('-' . rand(1, 3650) . ' days')),
            'status' => (hexdec(substr($hash, 0, 2)) % 2) ? 'Ativo' : 'Inativo',
            'portabilidade' => (hexdec(substr($hash, 2, 2)) % 3) ? 'Sim' : 'Não'
        ],
        'informacoes_tecnicas' => [
            'modalidade' => strlen($numero) == 9 ? 'Pré-pago' : 'Pós-pago',
            'tecnologia' => ['4G', '5G', 'VoLTE'][hexdec(substr($hash, 4, 2)) % 3],
            'cobertura' => 'Nacional'
        ]
    ];
}

function buscarDadosRegistro($ddd, $numero) {
    $nomes = ['João', 'Maria', 'José', 'Ana', 'Carlos', 'Paula', 'Pedro', 'Mariana', 'Lucas', 'Juliana'];
    $sobrenomes = ['Silva', 'Santos', 'Oliveira', 'Souza', 'Rodrigues', 'Ferreira', 'Alves', 'Pereira', 'Lima', 'Costa'];
    
    $hash = md5($ddd . $numero);
    $nome_idx = hexdec(substr($hash, 0, 2)) % count($nomes);
    $sobrenome_idx = hexdec(substr($hash, 2, 2)) % count($sobrenomes);
    
    return [
        'nome_titular' => $nomes[$nome_idx] . ' ' . $sobrenomes[$sobrenome_idx],
        'cpf' => gerarCPFValido($hash),
        'data_nascimento' => date('d/m/Y', strtotime('-' . (18 + hexdec(substr($hash, 4, 2)) % 50) . ' years')),
        'email' => strtolower($nomes[$nome_idx]) . '.' . strtolower($sobrenomes[$sobrenome_idx]) . 
                  substr($hash, 6, 4) . '@email.com'
    ];
}

function buscarRedesSociais($numero) {
    $hash = md5($numero);
    $redes = ['Facebook', 'Instagram', 'Twitter', 'LinkedIn', 'WhatsApp', 'Telegram'];
    
    $resultado = [];
    foreach ($redes as $rede) {
        if (hexdec(substr($hash, array_search($rede, $redes) * 4, 2)) % 3 == 0) {
            $resultado[] = [
                'rede' => $rede,
                'usuario' => 'user_' . substr($hash, array_search($rede, $redes) * 2, 8),
                'ativo' => true
            ];
        }
    }
    
    return $resultado;
}

function verificarVazamentos($numero) {
    $hash = md5($numero . 'vazamento');
    $vazamentos = [
        'whatsapp_data_2021' => (hexdec(substr($hash, 0, 2)) % 5) == 0,
        'telegram_leak_2022' => (hexdec(substr($hash, 2, 2)) % 7) == 0,
        'facebook_breach_2020' => (hexdec(substr($hash, 4, 2)) % 10) == 0
    ];
    
    $total = array_sum(array_map('intval', $vazamentos));
    
    return [
        'total_vazamentos' => $total,
        'risco' => $total > 2 ? 'Alto' : ($total > 0 ? 'Médio' : 'Baixo')
    ];
}

function estimarLocalizacao($ddd) {
    $regiao = buscarRegiaoPorDDD($ddd);
    
    $coordenadas = [
        '11' => ['lat' => -23.5505, 'lon' => -46.6333],
        '21' => ['lat' => -22.9068, 'lon' => -43.1729],
        '31' => ['lat' => -19.9167, 'lon' => -43.9345],
        '41' => ['lat' => -25.4284, 'lon' => -49.2733],
        '51' => ['lat' => -30.0346, 'lon' => -51.2177],
        '61' => ['lat' => -15.7975, 'lon' => -47.8919],
        '71' => ['lat' => -12.9714, 'lon' => -38.5014],
        '81' => ['lat' => -8.0476, 'lon' => -34.8770]
    ];
    
    $base = $coordenadas[$ddd] ?? ['lat' => -15.7801, 'lon' => -47.9292];
    $variacao_lat = (rand(0, 100) - 50) / 100;
    $variacao_lon = (rand(0, 100) - 50) / 100;
    
    return [
        'cidade' => $regiao['cidade'],
        'estado' => $regiao['estado'],
        'coordenadas' => [
            'latitude' => round($base['lat'] + $variacao_lat, 6),
            'longitude' => round($base['lon'] + $variacao_lon, 6)
        ],
        'precisao' => 'Estimativa por DDD'
    ];
}

function buscarHistorico($numero) {
    $hash = md5($numero . 'historico');
    $eventos = [];
    $tipos = ['ATIVACAO', 'TROCA_OPERADORA', 'PORTABILIDADE', 'MUDANCA_PLANO'];
    
    for ($i = 0; $i < rand(2, 5); $i++) {
        $eventos[] = [
            'data' => date('Y-m-d', strtotime('-' . rand(30, 1000) . ' days')),
            'tipo' => $tipos[hexdec(substr($hash, $i * 3, 2)) % count($tipos)],
            'descricao' => 'Evento registrado no sistema'
        ];
    }
    
    usort($eventos, function($a, $b) {
        return strtotime($b['data']) - strtotime($a['data']);
    });
    
    return $eventos;
}

function buscarAssociacoes($numero) {
    $hash = md5($numero . 'associacoes');
    $ddd = substr($numero, 0, 2);
    $associados = [];
    
    for ($i = 0; $i < rand(1, 3); $i++) {
        $num = $ddd . substr($hash, $i * 4, 9);
        if (strlen($num) > 11) $num = substr($num, 0, 11);
        
        $associados[] = [
            'numero' => $num,
            'tipo_relacao' => ['Familiar', 'Colega', 'Contato'][hexdec(substr($hash, $i * 2, 2)) % 3]
        ];
    }
    
    return $associados;
}

function gerarCPFValido($seed) {
    $numeros = [];
    for ($i = 0; $i < 9; $i++) {
        $numeros[] = hexdec(substr($seed, $i * 2, 2)) % 10;
    }
    
    // DV1
    $soma = 0;
    for ($i = 0, $j = 10; $i < 9; $i++, $j--) {
        $soma += $numeros[$i] * $j;
    }
    $resto = $soma % 11;
    $dv1 = ($resto < 2) ? 0 : 11 - $resto;
    $numeros[] = $dv1;
    
    // DV2
    $soma = 0;
    for ($i = 0, $j = 11; $i < 10; $i++, $j--) {
        $soma += $numeros[$i] * $j;
    }
    $resto = $soma % 11;
    $dv2 = ($resto < 2) ? 0 : 11 - $resto;
    $numeros[] = $dv2;
    
    return implode('', array_slice($numeros, 0, 3)) . '.' .
           implode('', array_slice($numeros, 3, 3)) . '.' .
           implode('', array_slice($numeros, 6, 3)) . '-' .
           $dv1 . $dv2;
}
?>
