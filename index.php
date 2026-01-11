<?php
// CYBERCEL API - Página Inicial SIMPLES
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYBERCEL API - Busca Telefônica</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            padding: 20px; 
            background: #0f172a; 
            color: white; 
            text-align: center;
            min-height: 100vh;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px;
        }
        h1 { 
            color: #3b82f6; 
            margin-bottom: 10px;
            font-size: 2.5rem;
        }
        .subtitle {
            color: #94a3b8;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        .box { 
            background: #1e293b; 
            padding: 25px; 
            border-radius: 10px; 
            margin: 20px auto; 
            max-width: 600px;
            border: 1px solid #334155;
        }
        h3 {
            color: #60a5fa;
            margin-bottom: 15px;
        }
        input { 
            padding: 12px; 
            margin: 5px; 
            width: 70%;
            border: 2px solid #475569;
            border-radius: 6px;
            background: #0f172a;
            color: white;
            font-size: 16px;
        }
        input:focus {
            outline: none;
            border-color: #3b82f6;
        }
        button { 
            padding: 12px 24px; 
            margin: 5px; 
            border: none;
            border-radius: 6px;
            background: #3b82f6;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
        }
        button:hover {
            background: #2563eb;
        }
        button.full {
            background: #10b981;
        }
        button.full:hover {
            background: #059669;
        }
        #resultado {
            margin-top: 20px;
            min-height: 50px;
        }
        pre { 
            text-align: left; 
            background: #0f172a; 
            padding: 20px; 
            border-radius: 8px;
            border: 1px solid #334155;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin-top: 15px;
        }
        .endpoint-box {
            background: rgba(59, 130, 246, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            text-align: left;
            border-left: 4px solid #3b82f6;
        }
        code {
            background: #0f172a;
            padding: 8px 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #7dd3fc;
            display: block;
            margin: 10px 0;
            overflow-x: auto;
        }
        .error {
            color: #ef4444;
            padding: 15px;
            background: rgba(239, 68, 68, 0.1);
            border-radius: 8px;
            margin: 10px 0;
        }
        .loading {
            color: #60a5fa;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 CYBERCEL API v2.0</h1>
        <p class="subtitle">API de busca telefônica com dados reais</p>
        
        <div class="box">
            <h3>🧪 Testar API</h3>
            <input type="text" id="numero" placeholder="11999999999" value="11999999999">
            <div style="margin-top: 15px;">
                <button onclick="testarAPI(false)">Busca Básica</button>
                <button onclick="testarAPI(true)" class="full">Busca Completa</button>
            </div>
        </div>
        
        <div id="resultado"></div>
        
        <div class="box">
            <h3>📡 Endpoints da API</h3>
            
            <div class="endpoint-box">
                <strong>Busca Básica:</strong>
                <code id="endpointBasic"></code>
            </div>
            
            <div class="endpoint-box">
                <strong>Busca Completa:</strong>
                <code id="endpointFull"></code>
            </div>
            
            <div class="endpoint-box">
                <strong>Exemplo de uso (JavaScript):</strong>
                <code id="exampleJS"></code>
            </div>
        </div>
        
        <div class="box">
            <h3>📊 Dados Retornados</h3>
            <ul style="text-align: left; margin-left: 20px; line-height: 1.8;">
                <li>✅ Operadora real por prefixo</li>
                <li>✅ Localização por DDD</li>
                <li>✅ Informações técnicas</li>
                <li>✅ Cobertura da rede</li>
                <li>✅ Dados da operadora</li>
                <li>✅ Status Anatel</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Configurações
        const baseUrl = window.location.origin;
        
        // Atualizar exemplos
        document.getElementById('endpointBasic').textContent = `${baseUrl}/api.php?numero=11999999999`;
        document.getElementById('endpointFull').textContent = `${baseUrl}/api.php?numero=11999999999&full=true`;
        document.getElementById('exampleJS').textContent = `fetch('${baseUrl}/api.php?numero=11999999999')\n  .then(r => r.json())\n  .then(data => console.log(data));`;
        
        // Função para testar API
        async function testarAPI(full = false) {
            const numeroInput = document.getElementById('numero');
            const numero = numeroInput.value.trim();
            const resultado = document.getElementById('resultado');
            
            // Validar entrada
            if (!numero) {
                resultado.innerHTML = '<div class="error">⚠ Digite um número para testar</div>';
                return;
            }
            
            // Validar formato (apenas números, 10-11 dígitos)
            const numeroLimpo = numero.replace(/\D/g, '');
            if (numeroLimpo.length < 10 || numeroLimpo.length > 11) {
                resultado.innerHTML = '<div class="error">⚠ Formato inválido. Use: DDD + 8 ou 9 dígitos</div>';
                return;
            }
            
            // Mostrar loading
            resultado.innerHTML = `
                <div class="loading">
                    🔍 Buscando informações para ${numero}...
                    <br><small>Isso pode levar alguns segundos</small>
                </div>
            `;
            
            try {
                // Construir URL
                const url = `${baseUrl}/api.php?numero=${encodeURIComponent(numeroLimpo)}${full ? '&full=true' : ''}`;
                
                // Fazer requisição
                const response = await fetch(url);
                
                // Verificar status
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${await response.text()}`);
                }
                
                // Processar resposta
                const data = await response.json();
                
                // Formatar resultado
                const jsonFormatado = JSON.stringify(data, null, 2);
                
                // Exibir resultado
                resultado.innerHTML = `
                    <div class="box">
                        <h3>✅ Resultado da Busca</h3>
                        <pre>${jsonFormatado}</pre>
                        <div style="margin-top: 15px;">
                            <button onclick="copiarJSON('${jsonFormatado.replace(/'/g, "\\'")}')">
                                📋 Copiar JSON
                            </button>
                            <button onclick="downloadJSON('${jsonFormatado.replace(/'/g, "\\'")}', 'cybercel_${numeroLimpo}.json')" 
                                    style="background: #10b981; margin-left: 10px;">
                                💾 Download
                            </button>
                        </div>
                    </div>
                `;
                
            } catch (error) {
                // Exibir erro
                resultado.innerHTML = `
                    <div class="error">
                        ❌ Erro na consulta
                        <br><small>${error.message}</small>
                        <br><br>
                        <button onclick="testarAPI(${full})" style="background: #6b7280;">
                            🔄 Tentar Novamente
                        </button>
                    </div>
                `;
                console.error('Erro:', error);
            }
        }
        
        // Função para copiar JSON
        function copiarJSON(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('✅ JSON copiado para a área de transferência!');
            }).catch(err => {
                alert('❌ Erro ao copiar: ' + err.message);
            });
        }
        
        // Função para download JSON
        function downloadJSON(jsonStr, filename) {
            const blob = new Blob([jsonStr], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
        
        // Permitir Enter para buscar
        document.getElementById('numero').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                testarAPI(false);
            }
        });
        
        // Testar automaticamente ao carregar (opcional)
        window.addEventListener('DOMContentLoaded', () => {
            // Pode remover esta linha se não quiser teste automático
            // testarAPI(false);
        });
    </script>
</body>
</html>
