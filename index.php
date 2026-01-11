<?php
// Se a URL não for a raiz, redirecionar para API
if ($_SERVER['REQUEST_URI'] !== '/' && $_SERVER['REQUEST_URI'] !== '/index.php') {
    // Extrair parâmetros
    $params = str_replace('/api.php', '', $_SERVER['REQUEST_URI']);
    
    if (strpos($params, '?') !== false) {
        // Se já tiver parâmetros, redirecionar para api.php
        header('Location: /api.php' . $params);
    } else {
        // Se for apenas um caminho, mostrar página inicial
        include 'index.html';
    }
    exit();
}

// Se for a raiz, mostrar página HTML normal
?>
<!DOCTYPE html>
<html>
<head>
    <title>CYBERCEL API</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #0f172a; color: white; text-align: center; }
        h1 { color: #3b82f6; }
        .box { background: #1e293b; padding: 20px; border-radius: 10px; margin: 20px auto; max-width: 600px; }
        input, button { padding: 10px; margin: 5px; }
        pre { text-align: left; background: #0f172a; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🔍 CYBERCEL API v2.0</h1>
    <p>API de busca telefônica com dados reais</p>
    
    <div class="box">
        <h3>Testar API:</h3>
        <input type="text" id="numero" placeholder="11999999999" value="11999999999">
        <button onclick="testar()">Buscar</button>
    </div>
    
    <div id="resultado"></div>
    
    <div class="box">
        <h3>📡 Endpoints:</h3>
        <p><code id="endpoint"></code></p>
        <p><code id="fullEndpoint"></code></p>
    </div>
    
    <script>
        const baseUrl = window.location.origin;
        document.getElementById('endpoint').textContent = `${baseUrl}/api.php?numero=11999999999`;
        document.getElementById('fullEndpoint').textContent = `${baseUrl}/api.php?numero=11999999999&full=true`;
        
        async function testar() {
            const numero = document.getElementById('numero').value.trim();
            const resultado = document.getElementById('resultado');
            
            if (!numero) {
                resultado.innerHTML = '<p style="color: #ef4444;">Digite um número</p>';
                return;
            }
            
            resultado.innerHTML = '<p>Buscando...</p>';
            
            try {
                const response = await fetch(`${baseUrl}/api.php?numero=${encodeURIComponent(numero)}`);
                const data = await response.json();
                resultado.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            } catch(error) {
                resultado.innerHTML = `<p style="color: #ef4444;">Erro: ${error.message}</p>`;
            }
        }
    </script>
</body>
</html>
