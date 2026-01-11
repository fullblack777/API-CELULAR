<?php
// Página web simples
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>CYBERCEL API - Busca Real</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #0f172a; color: white; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #3b82f6; }
        .api-url { background: #1e293b; padding: 15px; border-radius: 5px; margin: 10px 0; }
        input, button { padding: 10px; margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 CYBERCEL API v2.0</h1>
        <p>API de busca telefônica com dados REAIS</p>
        
        <div class="api-url">
            <strong>Endpoint:</strong> GET /api.php?numero=DDDNUMERO<br>
            <strong>Exemplo:</strong> <?php echo ($_SERVER['HTTPS']?'https':'http') . '://' . $_SERVER['HTTP_HOST']; ?>/api.php?numero=11999999999
        </div>
        
        <input type="text" id="numero" placeholder="11999999999" value="11999999999">
        <button onclick="testar()">Testar API</button>
        
        <div id="resultado" style="margin-top: 20px;"></div>
    </div>
    
    <script>
    async function testar() {
        const numero = document.getElementById('numero').value;
        const resultado = document.getElementById('resultado');
        
        resultado.innerHTML = 'Consultando...';
        
        try {
            const response = await fetch(`/api.php?numero=${numero}`);
            const data = await response.json();
            resultado.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
        } catch(error) {
            resultado.innerHTML = `Erro: ${error.message}`;
        }
    }
    </script>
</body>
</html>
