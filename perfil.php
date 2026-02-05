<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$sucesso = '';
$erro = '';

// Arquivo para armazenar dados do usuário
$arquivo_perfil = 'dados/perfil_' . $_SESSION['usuario_id'] . '.json';

// Criar diretório se não existir
if (!is_dir('dados')) {
    mkdir('dados', 0755, true);
}
if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
}

// Inicializar perfil do usuário
if (!file_exists($arquivo_perfil)) {
    $perfil_padrao = [
        'id' => $_SESSION['usuario_id'],
        'nome' => $_SESSION['usuario_nome'] ?? 'Usuário Teste',
        'email' => $_SESSION['usuario_email'] ?? 'teste@email.com',
        'tipo' => $_SESSION['usuario_tipo'] ?? 'paciente',
        'telefone' => '',
        'data_nascimento' => '',
        'cpf' => '',
        'endereco' => '',
        'cidade' => '',
        'estado' => '',
        'cep' => '',
        'foto' => ''
    ];
    file_put_contents($arquivo_perfil, json_encode($perfil_padrao, JSON_PRETTY_PRINT));
}

// Carregar perfil
$perfil = json_decode(file_get_contents($arquivo_perfil), true);

// Processar atualização do perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'atualizar_perfil') {
        $perfil['nome'] = htmlspecialchars($_POST['nome'] ?? $perfil['nome']);
        $perfil['email'] = htmlspecialchars($_POST['email'] ?? $perfil['email']);
        $perfil['telefone'] = htmlspecialchars($_POST['telefone'] ?? '');
        $perfil['data_nascimento'] = $_POST['data_nascimento'] ?? '';
        $perfil['cpf'] = htmlspecialchars($_POST['cpf'] ?? '');
        $perfil['endereco'] = htmlspecialchars($_POST['endereco'] ?? '');
        $perfil['cidade'] = htmlspecialchars($_POST['cidade'] ?? '');
        $perfil['estado'] = htmlspecialchars($_POST['estado'] ?? '');
        $perfil['cep'] = htmlspecialchars($_POST['cep'] ?? '');
        
        // Processar upload de foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto_temp = $_FILES['foto']['tmp_name'];
            $foto_nome = $_FILES['foto']['name'];
            $foto_ext = pathinfo($foto_nome, PATHINFO_EXTENSION);
            
            // Validar extensão
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower($foto_ext), $extensoes_permitidas)) {
                $erro = "Tipo de arquivo não permitido! Use: JPG, PNG ou GIF.";
            } else {
                // Validar tamanho (máximo 5MB)
                $max_size = 5 * 1024 * 1024;
                if ($_FILES['foto']['size'] > $max_size) {
                    $erro = "Arquivo muito grande! Máximo 5MB.";
                } else {
                    // Salvar foto com nome único
                    $foto_nome_unica = $_SESSION['usuario_id'] . '_' . time() . '.' . $foto_ext;
                    $caminho_foto = 'uploads/' . $foto_nome_unica;
                    
                    if (move_uploaded_file($foto_temp, $caminho_foto)) {
                        // Deletar foto antiga se existir
                        if (!empty($perfil['foto']) && file_exists('uploads/' . $perfil['foto'])) {
                            unlink('uploads/' . $perfil['foto']);
                        }
                        $perfil['foto'] = $foto_nome_unica;
                    } else {
                        $erro = "Erro ao fazer upload da foto!";
                    }
                }
            }
        }
        
        // Salvar perfil
        if (empty($erro)) {
            file_put_contents($arquivo_perfil, json_encode($perfil, JSON_PRETTY_PRINT));
            $_SESSION['usuario_nome'] = $perfil['nome'];
            $_SESSION['usuario_email'] = $perfil['email'];
            $_SESSION['usuario_foto'] = $perfil['foto']; // Atualizar sessão com a nova foto
            $sucesso = "✓ Perfil atualizado com sucesso!";
            
            // Redirecionar para o index após 2 segundos
            header("refresh:2; url=index.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Conecta Saúde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0077b6;
            --secondary: #00b4d8;
        }
        body {
            background-color: #f8f9fa;
        }
        .avatar-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 15px;
        }
        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary);
            cursor: pointer;
            transition: all 0.3s;
        }
        .avatar-img:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 119, 182, 0.3);
        }
        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
            cursor: pointer;
        }
        .avatar-container:hover .avatar-overlay {
            opacity: 1;
        }
        .avatar-overlay i {
            color: white;
            font-size: 2rem;
        }
        .form-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #dee2e6;
        }
        .form-section h5 {
            color: var(--primary);
            margin-bottom: 20px;
            font-weight: 600;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .toast-notification {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 2000;
        }
    </style>
</head>
<body>
    <?php include_once 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header text-center py-4">
                        <h4 class="mb-0"><i class="bi bi-person-circle"></i> Meu Perfil</h4>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if($sucesso): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i> <?php echo $sucesso; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <?php echo $erro; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Seção de Foto -->
                        <div class="text-center mb-4">
                            <div class="avatar-container">
                                <?php
                                $foto = '';
                                if (!empty($perfil['foto']) && file_exists('uploads/' . $perfil['foto'])) {
                                    $foto = 'uploads/' . $perfil['foto'];
                                } else {
                                    $foto = 'assets/default-avatar.png';
                                }
                                ?>
                                <img src="<?php echo $foto; ?>?t=<?php echo time(); ?>" class="avatar-img" alt="Foto de perfil" id="fotoDisplay">
                                <div class="avatar-overlay" onclick="document.getElementById('inputFoto').click()">
                                    <i class="bi bi-camera-fill"></i>
                                </div>
                            </div>
                            <p class="text-muted small">Clique na foto para alterar</p>
                        </div>

                        <!-- Formulário de Edição -->
                        <form method="POST" action="" enctype="multipart/form-data" id="formPerfil">
                            <input type="hidden" name="acao" value="atualizar_perfil">
                            <input type="file" id="inputFoto" name="foto" accept="image/*" style="display:none;">

                            <!-- Informações Básicas -->
                            <div class="form-section">
                                <h5><i class="bi bi-info-circle"></i> Informações Básicas</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nome" class="form-label">Nome Completo *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" 
                                               value="<?php echo htmlspecialchars($perfil['nome']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">E-mail *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($perfil['email']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="tel" class="form-control" id="telefone" name="telefone" 
                                               placeholder="(11) 98765-4321"
                                               value="<?php echo htmlspecialchars($perfil['telefone']); ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" 
                                               value="<?php echo $perfil['data_nascimento']; ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="cpf" class="form-label">CPF</label>
                                        <input type="text" class="form-control" id="cpf" name="cpf" 
                                               placeholder="000.000.000-00"
                                               value="<?php echo htmlspecialchars($perfil['cpf']); ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipo de Usuário</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo ucfirst($perfil['tipo']); ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div class="form-section">
                                <h5><i class="bi bi-geo-alt"></i> Endereço</h5>
                                
                                <div class="mb-3">
                                    <label for="endereco" class="form-label">Rua e Número</label>
                                    <input type="text" class="form-control" id="endereco" name="endereco" 
                                           placeholder="Rua das Flores, 123"
                                           value="<?php echo htmlspecialchars($perfil['endereco']); ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade" 
                                               value="<?php echo htmlspecialchars($perfil['cidade']); ?>">
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select class="form-select" id="estado" name="estado">
                                            <option value="">Selecione</option>
                                            <option value="SP" <?php echo $perfil['estado'] === 'SP' ? 'selected' : ''; ?>>SP</option>
                                            <option value="RJ" <?php echo $perfil['estado'] === 'RJ' ? 'selected' : ''; ?>>RJ</option>
                                            <option value="MG" <?php echo $perfil['estado'] === 'MG' ? 'selected' : ''; ?>>MG</option>
                                            <option value="BA" <?php echo $perfil['estado'] === 'BA' ? 'selected' : ''; ?>>BA</option>
                                            <option value="RS" <?php echo $perfil['estado'] === 'RS' ? 'selected' : ''; ?>>RS</option>
                                            <option value="PR" <?php echo $perfil['estado'] === 'PR' ? 'selected' : ''; ?>>PR</option>
                                            <option value="PE" <?php echo $perfil['estado'] === 'PE' ? 'selected' : ''; ?>>PE</option>
                                            <option value="CE" <?php echo $perfil['estado'] === 'CE' ? 'selected' : ''; ?>>CE</option>
                                            <option value="SC" <?php echo $perfil['estado'] === 'SC' ? 'selected' : ''; ?>>SC</option>
                                            <option value="GO" <?php echo $perfil['estado'] === 'GO' ? 'selected' : ''; ?>>GO</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label for="cep" class="form-label">CEP</label>
                                        <input type="text" class="form-control" id="cep" name="cep" 
                                               placeholder="00000-000"
                                               value="<?php echo htmlspecialchars($perfil['cep']); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="form-section">
                                <div class="d-flex justify-content-between">
                                    <a href="dashboard.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Voltar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Salvar Alterações
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Atualizar foto em tempo real
        function atualizarFoto(event) {
            const arquivo = event.target.files[0];
            
            if (!arquivo) {
                return;
            }
            
            // Validar tipo
            const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!tiposPermitidos.includes(arquivo.type)) {
                mostrarNotificacao('Tipo de arquivo não permitido! Use JPG, PNG ou GIF.', 'danger');
                event.target.value = ''; // Limpar input
                return;
            }
            
            // Validar tamanho (máximo 5MB)
            const maxSize = 5 * 1024 * 1024;
            if (arquivo.size > maxSize) {
                mostrarNotificacao('Arquivo muito grande! Máximo 5MB.', 'danger');
                event.target.value = ''; // Limpar input
                return;
            }
            
            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('fotoDisplay').src = e.target.result;
                mostrarNotificacao('✓ Foto selecionada! Clique em "Salvar Alterações" para confirmar.', 'info');
            };
            reader.readAsDataURL(arquivo);
        }
        
        // Listener para o input file
        document.addEventListener('DOMContentLoaded', function() {
            const inputFoto = document.getElementById('inputFoto');
            if (inputFoto) {
                inputFoto.addEventListener('change', atualizarFoto);
            }
        });
        
        // Validar CPF
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]/g, '');
            if (cpf.length !== 11) return false;
            
            let soma = 0;
            let resto;
            
            if (cpf === '00000000000' || cpf === '11111111111' || cpf === '22222222222' || 
                cpf === '33333333333' || cpf === '44444444444' || cpf === '55555555555' || 
                cpf === '66666666666' || cpf === '77777777777' || cpf === '88888888888' || 
                cpf === '99999999999') {
                return false;
            }
            
            for (let i = 1; i <= 9; i++) {
                soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
            }
            
            resto = (soma * 10) % 11;
            
            if (resto === 10 || resto === 11) {
                resto = 0;
            }
            
            if (resto !== parseInt(cpf.substring(9, 10))) {
                return false;
            }
            
            soma = 0;
            
            for (let i = 1; i <= 10; i++) {
                soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
            }
            
            resto = (soma * 10) % 11;
            
            if (resto === 10 || resto === 11) {
                resto = 0;
            }
            
            if (resto !== parseInt(cpf.substring(10, 11))) {
                return false;
            }
            
            return true;
        }
        
        // Validação do formulário
        document.getElementById('formPerfil').addEventListener('submit', function(e) {
            const cpf = document.getElementById('cpf').value.trim();
            
            if (cpf && !validarCPF(cpf)) {
                e.preventDefault();
                mostrarNotificacao('CPF inválido!', 'danger');
                return false;
            }
            
            mostrarNotificacao('✓ Salvando alterações...', 'info');
        });
        
        // Mostrar notificação
        function mostrarNotificacao(mensagem, tipo = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${tipo} alert-dismissible fade show toast-notification`;
            toast.innerHTML = `
                ${mensagem}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 4000);
        }
    </script>
</body>
</html>
