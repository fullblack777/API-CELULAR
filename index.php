<?php
// CYBERCEL - Página Inicial
header('Content-Type: text/html; charset=utf-8');
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
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            color: white; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { text-align: center; margin-bottom: 40px; padding: 30px; }
        h1 { 
            font-size: 3.5rem; 
            background: linear-gradient(90deg, #ff0080, #ff8c00, #40e0d0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 20px rgba(255, 0, 128, 0.5);
            margin-bottom: 10px;
        }
        .subtitle { color: #40e0d0; font-size: 1.2rem; opacity: 0.9; }
        .card { 
            background: rgba(255, 255, 255, 0.1); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
        }
        h2 { color: #ff8c00; margin-bottom: 20px; border-left: 4px solid #ff0080; padding-left: 15px; }
        .api-url { 
            background: rgba(0, 0, 0, 0.5); 
            padding: 15px; 
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
            overflow-x: auto;
            border: 1px solid #40e0d0;
        }
        .test-area { margin-top: 30px; }
        input { 
            width: 100%; 
            padding: 15px; 
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid #40e0d0;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            margin-bottom: 15px;
        }
        button { 
            background: linear-gradient(90deg, #ff0080, #ff8c00);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: transform 0.3s;
            font-weight: bold;
        }
        button:hover { transform: translateY(-3px); }
        #result { margin-top: 30px; }
        .response { 
            background: rgba(0, 0, 0, 0.7); 
            padding: 20px; 
            border-radius: 10px;
            border: 1px solid #ff8c00;
            max-height: 500px;
            overflow-y: auto;
        }
        .endpoint { 
            background: rgba(64, 224, 208, 0.1);
            border-left: 4px solid #40e0d0;
            padding: 15px;
            margin: 15px 0;
            border-radius: 0 8px 8px 0;
        }
        .method { 
            display: inline-block; 
            padding: 5px 15px; 
            background: #ff0080; 
            color: white; 
            border-radius: 5px;
            margin-right: 10px;
            font-weight: bold;
        }
        .method.get { background: #40e0d0; }
        .method.post { background: #ff8c00; }
        footer { 
            text-align: center; 
            margin-top: 50px; 
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            color: #aaa;
        }
        .highlight { color: #ff8c00; font-weight: bold; }
        .status { 
            padding: 10px; 
            border-radius: 5px; 
            margin: 10px 0;
            display: inline-block;
        }
        .status.success { background: rgba(64, 224, 208, 0.2); color: #40e0d0; }
        .status.error { background: rgba(255, 0, 128, 0.2); color: #ff0080; }
        .loading { 
            text-align: center; 
            padding: 30px; 
            color: #40e0d0;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🔍 CYBERCEL API</h1>
            <p class="subtitle">API de Busca Telefônica Completa</p>
            <p>Obtenha informações detalhadas sobre qualquer número telefônico</p>
        </header>
        
        <div class="card">
            <h2>🌐 API Endpoints</h2>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <strong>Busca Básica</strong>
                <div class="api-url" id="url-basic"></div>
                <p>Retorna informações básicas do número</p>
            </div>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <strong>Busca Completa</strong>
                <div class="api-url" id="url-full"></div>
                <p>Retorna todas as informações disponíveis</p>
            </div>
            
            <div class="endpoint">
                <span class="method post">POST</span>
                <strong>Busca via POST</strong>
                <div class="api-url">POST /api.php</div>
                <pre style="color: #aaa; margin-top: 10px;">{
    "numero": "11999999999",
    "full": true
}</pre>
            </div>
        </div>
        
        <div class="card">
            <h2>🧪 Testar API</h2>
            <div class="test-area">
                <input type="text" id="phoneInput" placeholder="Digite o número com DDD (ex: 11999999999)" value="11999999999">
                <div>
                    <button onclick="testAPI(false)">Busca Básica</button>
                    <button onclick="testAPI(true)" style="margin-left: 10px; background: linear-gradient(90deg, #ff8c00, #40e0d0);">Busca Completa</button>
                </div>
                
                <div id="result"></div>
            </div>
        </div>
        
        <div class="card">
            <h2>📚 Exemplos de Integração</h2>
            
            <h3 style="color: #40e0d0; margin-top: 20px;">JavaScript:</h3>
            <div class="api-url">
                fetch('<?php echo $_SERVER['HTTP_HOST']; ?>/api.php?numero=11999999999')
                    .then(response => response.json())
                    .then(data => console.log(data));
            </div>
            
            <h3 style="color: #40e0d0; margin-top: 20px;">Python:</h3>
            <div class="api-url">
                import requests<br>
                response = requests.get('<?php echo $_SERVER['HTTP_HOST']; ?>/api.php?numero=11999999999')<br>
                data = response.json()
            </div>
            
            <h3 style="color: #40e0d0; margin-top: 20px;">PHP:</h3>
            <div class="api-url">
                $response = file_get_contents('https://<?php echo $_SERVER['HTTP_HOST']; ?>/api.php?numero=11999999999');<br>
                $data = json_decode($response, true);
            </div>
            
            <h3 style="color: #40e0d0; margin-top: 20px;">cURL:</h3>
            <div class="api-url">
                curl "https://<?php echo $_SERVER['HTTP_HOST']; ?>/api.php?numero=11999999999"
            </div>
        </div>
        
        <div class="card">
            <h2>📊 Dados Retornados</h2>
            <ul style="list-style: none; line-height: 2;">
                <li>✅ <span class="highlight">Operadora</span> - Detecção automática</li>
                <li>✅ <span class="highlight">Localização</span> - Cidade/Estado por DDD</li>
                <li>✅ <span class="highlight">Dados de Registro</span> - Informações do titular</li>
                <li>✅ <span class="highlight">Redes Sociais</span> - Possíveis associações</li>
                <li>✅ <span class="highlight">Vazamentos</span> - Verificação de segurança</li>
                <li>✅ <span class="highlight">Histórico</span> - Eventos da linha</li>
                <li>✅ <span class="highlight">Associações</span> - Números relacionados</li>
                <li>✅ <span class="highlight">Geolocalização</span> - Coordenadas aproximadas</li>
            </ul>
        </div>
        
        <footer>
            <p>CYBERCEL API v1.0 - Busca Telefônica Inteligente</p>
            <p>Desenvolvido para integrações e consultas profissionais</p>
            <p style="margin-top: 20px; font-size: 0.9rem; color: #777;">
                Esta API retorna dados gerados para fins de demonstração.<br>
                Para uso em produção, contate-nos.
            </p>
        </footer>
    </div>
    
    <script>
        // Atualizar URLs dinâmicas
        const baseUrl = window.location.origin;
        document.getElementById('url-basic').textContent = `${baseUrl}/api.php?numero=11999999999`;
        document.getElementById('url-full').textContent = `${baseUrl}/api.php?numero=11999999999&full=true`;
        
        // Função para testar API
        async function testAPI(full) {
            const phoneInput = document.getElementById('phoneInput').value;
            const resultDiv = document.getElementById('result');
            
            if (!phoneInput || phoneInput.length < 10) {
                resultDiv.innerHTML = `<div class="status error">⚠ Digite um número válido com DDD</div>`;
                return;
            }
            
            // Mostrar loading
            resultDiv.innerHTML = `<div class="loading">
                🔍 Buscando informações para ${phoneInput}...
                <br><small>Aguarde, isso pode levar alguns segundos</small>
            </div>`;
            
            try {
                const url = `${baseUrl}/api.php?numero=${encodeURIComponent(phoneInput)}${full ? '&full=true' : ''}`;
                const response = await fetch(url);
                const data = await response.json();
                
                // Formatar JSON bonito
                const jsonStr = JSON.stringify(data, null, 2);
                
                // Syntax highlighting básico
                const highlighted = jsonStr
                    .replace(/(".*?"):/g, '<span style="color: #40e0d0;">$1</span>:')
                    .replace(/(".*?")/g, '<span style="color: #ff8c00;">$1</span>')
                    .replace(/\b(true|false|null)\b/g, '<span style="color: #ff0080;">$1</span>')
                    .replace(/\b(\d+)\b/g, '<span style="color: #40e0d0;">$1</span>');
                
                resultDiv.innerHTML = `
                    <div class="status success">✅ Busca realizada com sucesso!</div>
                    <div class="response">
                        <pre style="color: #fff;">${highlighted}</pre>
                    </div>
                    <div style="margin-top: 15px;">
                        <button onclick="copyToClipboard('${jsonStr.replace(/'/g, "\\'")}')">
                            📋 Copiar JSON
                        </button>
                        <button onclick="downloadJSON(${JSON.stringify(data)}, 'cybercel_${phoneInput}.json')" 
                                style="margin-left: 10px; background: linear-gradient(90deg, #40e0d0, #ff0080);">
                            💾 Download JSON
                        </button>
                    </div>
                `;
                
            } catch (error) {
                resultDiv.innerHTML = `<div class="status error">❌ Erro: ${error.message}</div>`;
            }
        }
        
        // Função para copiar para clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('✅ JSON copiado para clipboard!');
            });
        }
        
        // Função para download JSON
        function downloadJSON(data, filename) {
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            URL.revokeObjectURL(url);
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
