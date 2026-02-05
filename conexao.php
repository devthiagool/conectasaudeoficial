<?php
// Configurações do sistema Conecta Saúde
// Sistema baseado em JSON (sem banco de dados MySQL)

// Timezone
date_default_timezone_set('America/Fortaleza');

// Inicia sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Diretórios importantes
define('DADOS_DIR', __DIR__ . '/dados');
define('UPLOADS_DIR', __DIR__ . '/uploads');

// Criar diretórios se não existirem
if (!is_dir(DADOS_DIR)) {
    mkdir(DADOS_DIR, 0755, true);
}
if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755, true);
}
?>