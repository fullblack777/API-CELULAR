<?php
// CYBERCEL - Página Inicial
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYBERCEL - Busca Telefônica</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            background: #0f172a;
            color: white; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        header { text-align: center; margin-bottom: 40px; padding: 30px 0; }
        h1 { 
            font-size: 3rem; 
            background: linear-gradient(90deg, #ff0080, #ff8c00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        .subtitle { color: #60a5fa; font-size: 1.2rem; margin-bottom: 20px; }
        .card { 
            background: #1e293b; 
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #334155;
        }
        h2 { color: #ff8c00; margin-bottom: 20px; }
        .api-url { 
            background: #0f172a; 
            padding: 15px; 
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
            overflow-x: auto;
            border: 1px solid #475569;
            color: #7dd3fc;
        }
        .test-area { margin: 30px 0; }
        input { 
            width: 100%; 
            padding: 12px; 
            background: #0f172a;
            border: 2px solid #475569;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            margin-bottom: 15px;
        }
        input:focus { outline: none; border-color: #60a5fa; }
        button { 
            background: #3b82f6; 
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
            transition: background 0.3s;
        }
        button:hover { background: #2563eb; }
        button.full { background: linear-gradient(90deg, #ff8c00, #ef4444); }
        button.full:hover { background: linear-gradient(90deg, #ea580c, #dc2626); }
        #result { margin-top: 20px; }
        .response { 
            background: #0f172a; 
            padding: 20px; 
            border-radius: 8px;
            border: 1px solid #475569;
            max-height: 500px;
            overflow-y: auto;
            margin-top: 15px;
        }
        pre { 
            white-space: pre-wrap;
            word-wrap: break-word;
            color: #cbd5e1;
        }
        .loading { color: #60a5fa; text-align: center; padding: 30px; }
        .error { color: #ef4444; padding: 15px; background: rgba(239,68,68,0.1); border-radius: 8px; }
        .success { color: #10b981; padding: 15px; background: rgba(16,185,129,0.1); border-radius: 8px; }
        .endpoint { 
            background: rgba(59, 130, 246, 0.1);
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        .method { 
            display: inline-block; 
            padding: 5px 10px; 
            background: #3b82f6; 
            color: white; 
            border-radius: 5px;
            margin-right: 10px;
            font-weight: bold;
        }
        .method.get { background: #10b981; }
        .method.post { background: #f59e0b; }
        footer { 
            text-align: center; 
            margin-top: 40px; 
            padding-top: 20px;
            border-top: 1px solid #334155;
            color: #94a3b8;
        }
        .highlight { color: #fbbf24; }
        .code-block { background: #1e293b; padding: 15px; border-radius: 8px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🔍 CYBERCEL API</h1>
            <p class="subtitle">API de Busca Telefônica Inteligente</p>
            <p>Obtenha informações detalhadas sobre qualquer número telefônico brasileiro</p>
        </header>
        
        <div class="card">
            <h2>🚀 Testar API</h2>
            <div class="test-area">
                <input type="text" id="phoneInput" placeholder="Digite o número com DDD (ex: 11999999999)" value="11999999999">
                <div>
                    <button onclick="testAPI(false)">Busca Básica</button>
                    <button onclick="testAPI(true)" class="full">Busca Completa</button>
                    <button onclick="clearResult()" style="background: #6b7280;">Limpar</button>
                </div>
                
                <div id="result"></div>
            </div>
        </div>
        
        <div class="card">
            <h2>🌐 Endpoints da API</h2>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <strong>Busca Básica</strong>
                <div class="api-url" id="url-basic"></div>
                <p>Retorna informações essenciais do número</p>
            </div>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <strong>Busca Completa</strong>
                <div class="api-url" id="url-full"></div>
                <p>Retorna todos os dados disponíveis (inclui redes sociais, vazamentos, etc.)</p>
            </div>
            
            <div class="endpoint">
                <span class="method post">POST</span>
                <strong>Busca via POST</strong>
                <div class="api-url">POST /api.php</div>
                <div class="code-block">
<pre>{
    "numero": "11999999999",
    "full": true
}</pre>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>📚 Exemplos de Uso</h2>
            
            <h3 style="color: #60a5fa; margin: 20px 0 10px 0;">JavaScript:</h3>
            <div class="code-block">
<pre>fetch('<?php echo ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/api.php?numero=11999999999')
    .then(response => response.json())
    .then(data => console.log(data));</pre>
            </div>
            
            <h3 style="color: #60a5fa; margin: 20px 0 10px 0;">Python:</h3>
            <div class="code-block">
<pre>import requests

response = requests.get('<?php echo ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/api.php?numero=11999999999')
data = response.json()
print(data)</pre>
            </div>
            
            <h3 style="color: #60a5fa; margin: 20px 0 10px 0;">PHP:</h3>
            <div class="code-block">
<pre>$json = file_get_contents('<?php echo ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/api.php?numero=11999999999');
$data = json_decode($json, true);
print_r($data);</pre>
            </div>
            
            <h3 style="color: #60a5fa; margin: 20px 0 10px 0;">cURL:</h3>
            <div class="code-block">
<pre>curl "<?php echo ($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/api.php?numero=11999999999"</pre>
            </div>
        </div>
        
        <div class="card">
            <h2>📊 Dados Retornados</h2>
            <ul style="list-style: none; margin-left: 20px;">
                <li>✅ <span class="highlight">Operadora</span> - Detecção automática</li>
                <li>✅ <span class="highlight">Localização</span> - Cidade e Estado por DDD</li>
                <li>✅ <span class="highlight">Dados do Titular</span> - Nome, CPF, email</li>
                <li>✅ <span class="highlight">Redes Sociais</span> - Possíveis associações</li>
                <li>✅ <span class="highlight">Verificação de Vazamentos</span> - Segurança</li>
                <li>✅ <span class="highlight">Histórico</span> - Eventos da linha</li>
                <li>✅ <span class="highlight">Números Associados</span> - Contatos relacionados</li>
                <li>✅ <span class="highlight">Geolocalização</span> - Coordenadas aproximadas</li>
            </ul>
        </div>
        
        <footer>
            <p>CYBERCEL API v1.0 - Busca Telefônica Completa</p>
            <p>API desenvolvida para integrações e consultas profissionais</p>
            <p style="margin-top: 20px; font-size: 0.9em; color: #6b7280;">
                Esta API retorna dados gerados para fins de demonstração e desenvolvimento.<br>
                Para uso em produção, contate-nos.
            </p>
        </footer>
    </div>
    
    <script>
        // URL base
        const baseUrl = window.location.origin;
        document.getElementById('url-basic').textContent = `${baseUrl}/api.php?numero=11999999999`;
        document.getElementById('url-full').textContent = `${baseUrl}/api.php?numero=11999999999&full=true`;
        
        // Testar API
        async function testAPI(full) {
            const phoneInput = document.getElementById('phoneInput').value.trim();
            const resultDiv = document.getElementById('result');
            
            // Validar número
            if (!phoneInput || phoneInput.length < 10) {
                resultDiv.innerHTML = `<div class="error">⚠ Digite um número válido com DDD (ex: 11999999999)</div>`;
                return;
            }
            
            // Limpar caracteres não numéricos
            const numeroLimpo = phoneInput.replace(/\D/g, '');
            if (numeroLimpo.length < 10 || numeroLimpo.length > 11) {
                resultDiv.innerHTML = `<div class="error">⚠ Número inválido. Use DDD + 8 ou 9 dígitos</div>`;
                return;
            }
            
            // Mostrar loading
            resultDiv.innerHTML = `<div class="loading">
                🔍 Buscando informações para ${phoneInput}...
                <br><small>Aguarde enquanto consultamos nossos sistemas...</small>
            </div>`;
            
            try {
                const url = `${baseUrl}/api.php?numero=${encodeURIComponent(numeroLimpo)}${full ? '&full=true' : ''}`;
                console.log('URL:', url);
                
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                // Formatar JSON
                const jsonStr = JSON.stringify(data, null, 2);
                
                // Destacar status
                let statusHtml = '';
                if (data.status === 'success' || data.status === 'successo') {
                    statusHtml = `<div class="success">✅ Busca realizada com sucesso!</div>`;
                } else if (data.status === 'error') {
                    statusHtml = `<div class="error">❌ ${data.message || 'Erro na busca'}</div>`;
                }
                
                resultDiv.innerHTML = `
                    ${statusHtml}
                    <div class="response">
                        <pre>${jsonStr}</pre>
                    </div>
                    <div style="margin-top: 15px;">
                        <button onclick="copyToClipboard('${jsonStr.replace(/'/g, "\\'")}')">
                            📋 Copiar JSON
                        </button>
                        <button onclick="downloadJSON('${jsonStr.replace(/'/g, "\\'")}', 'cybercel_${numeroLimpo}.json')"
                                style="background: #10b981; margin-left: 10px;">
                            💾 Download JSON
                        </button>
                    </div>
                `;
                
            } catch (error) {
                resultDiv.innerHTML = `<div class="error">❌ Erro: ${error.message}</div>`;
                console.error('Erro:', error);
            }
        }
        
        // Copiar para clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('✅ JSON copiado para clipboard!');
            }).catch(err => {
                alert('❌ Erro ao copiar: ' + err.message);
            });
        }
        
        // Download JSON
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
        
        // Limpar resultados
        function clearResult() {
            document.getElementById('result').innerHTML = '';
        }
        
        // Permitir Enter para buscar
        document.getElementById('phoneInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                testAPI(false);
            }
        });
    </script>
</body>
</html>
