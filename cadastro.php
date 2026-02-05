<?php
session_start();

$erro = '';
$sucesso = '';

// Processar cadastro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha_form = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $tipo = $_POST['tipo'] ?? 'paciente';
    
    // Upload da foto
    $foto_perfil = 'default.png'; // Foto padrão
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        
        if (in_array($extensao, $extensoes_permitidas)) {
            // Cria nome único para o arquivo
            $nome_arquivo = uniqid() . '.' . $extensao;
            $caminho_destino = 'uploads/' . $nome_arquivo;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_destino)) {
                $foto_perfil = $nome_arquivo;
            }
        }
    }
    
    // Validações
    if (empty($nome) || empty($email) || empty($senha_form)) {
        $erro = "Preencha todos os campos obrigatórios!";
    } elseif ($senha_form != $confirmar_senha) {
        $erro = "As senhas não coincidem!";
    } elseif (strlen($senha_form) < 6) {
        $erro = "A senha deve ter no mínimo 6 caracteres!";
    } else {
        // Simulação de banco de dados em arquivo JSON
        $usuarios = [];
        if (file_exists('usuarios.json')) {
            $usuarios = json_decode(file_get_contents('usuarios.json'), true);
        }
        
        // Verifica se email já existe
        foreach ($usuarios as $usuario) {
            if ($usuario['email'] == $email) {
                $erro = "Este e-mail já está cadastrado!";
                break;
            }
        }
        
        if (empty($erro)) {
            // Cria novo usuário
            $novo_usuario = [
                'id' => uniqid(),
                'nome' => $nome,
                'email' => $email,
                'senha' => password_hash($senha_form, PASSWORD_DEFAULT),
                'tipo' => $tipo,
                'foto' => $foto_perfil,
                'data_cadastro' => date('Y-m-d H:i:s')
            ];
            
            $usuarios[] = $novo_usuario;
            file_put_contents('usuarios.json', json_encode($usuarios, JSON_PRETTY_PRINT));
            
            // Login automático
            $_SESSION['usuario_id'] = $novo_usuario['id'];
            $_SESSION['usuario_nome'] = $novo_usuario['nome'];
            $_SESSION['usuario_email'] = $novo_usuario['email'];
            $_SESSION['usuario_tipo'] = $novo_usuario['tipo'];
            $_SESSION['usuario_foto'] = $novo_usuario['foto'];
            
            $sucesso = "Cadastro realizado com sucesso! Você será redirecionado.";
            
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Conecta Saúde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0077b6;
            --secondary: #00b4d8;
        }
        body {
            background: linear-gradient(135deg, #e0f7fa, #ffffff);
            min-height: 100vh;
        }
        .cadastro-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: var(--primary);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background-color: #005a87;
            border-color: #005a87;
        }
        .preview-foto {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            cursor: pointer;
        }
        .upload-label {
            cursor: pointer;
        }
        .btn-option {
            border: 2px solid #dee2e6;
            padding: 15px;
            text-align: center;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-option:hover {
            border-color: var(--primary);
        }
        .btn-option.active {
            border-color: var(--primary);
            background-color: rgba(0, 119, 182, 0.1);
        }
    </style>
</head>
<body>
    <?php include_once 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="cadastro-card">
                    <div class="card-header text-center py-3">
                        <h4 class="mb-0"><i class="bi bi-person-plus"></i> Cadastro</h4>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $erro; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($sucesso): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $sucesso; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" enctype="multipart/form-data" id="formCadastro">
                            <!-- Foto de Perfil -->
                            <div class="text-center mb-4">
                                <label for="foto" class="upload-label">
                                    <img id="preview" src="assets/default-avatar.png" class="preview-foto mb-2" 
                                         alt="Foto de perfil">
                                    <p class="text-muted small">Clique para adicionar foto</p>
                                </label>
                                <input type="file" id="foto" name="foto" accept="image/*" 
                                       class="d-none" onchange="previewImage(event)">
                            </div>
                            
                            <!-- Tipo de Usuário -->
                            <div class="mb-4">
                                <label class="form-label mb-3"><strong>Você é:</strong></label>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="btn-option <?php echo ($_POST['tipo'] ?? 'paciente') == 'paciente' ? 'active' : ''; ?>" 
                                             onclick="selectTipo('paciente')">
                                            <input type="radio" name="tipo" value="paciente" 
                                                   id="paciente" <?php echo ($_POST['tipo'] ?? 'paciente') == 'paciente' ? 'checked' : ''; ?> 
                                                   style="display: none;">
                                            <i class="bi bi-person fs-1 d-block mb-2"></i>
                                            <strong>Paciente</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="btn-option <?php echo ($_POST['tipo'] ?? 'paciente') == 'medico' ? 'active' : ''; ?>" 
                                             onclick="selectTipo('medico')">
                                            <input type="radio" name="tipo" value="medico" 
                                                   id="medico" <?php echo ($_POST['tipo'] ?? 'paciente') == 'medico' ? 'checked' : ''; ?> 
                                                   style="display: none;">
                                            <i class="bi bi-heart-pulse fs-1 d-block mb-2"></i>
                                            <strong>Profissional de Saúde</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Nome -->
                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           value="<?php echo $_POST['nome'] ?? ''; ?>" required>
                                </div>
                                
                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">E-mail *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo $_POST['email'] ?? ''; ?>" required>
                                </div>
                                
                                <!-- Senha -->
                                <div class="col-md-6 mb-3">
                                    <label for="senha" class="form-label">Senha *</label>
                                    <input type="password" class="form-control" id="senha" name="senha" required>
                                    <small class="text-muted">Mínimo 6 caracteres</small>
                                </div>
                                
                                <!-- Confirmar Senha -->
                                <div class="col-md-6 mb-3">
                                    <label for="confirmar_senha" class="form-label">Confirmar Senha *</label>
                                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                                </div>
                            </div>
                            
                            <!-- Termos -->
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="termos" required>
                                <label class="form-check-label" for="termos">
                                    Aceito os <a href="#" data-bs-toggle="modal" data-bs-target="#termosModal">Termos de Uso</a> 
                                    e <a href="#">Política de Privacidade</a>
                                </label>
                            </div>
                            
                            <!-- Botões -->
                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">Voltar</a>
                                <button type="submit" class="btn btn-primary">Cadastrar</button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p>Já tem uma conta? <a href="login.php">Faça login aqui</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Termos -->
    <div class="modal fade" id="termosModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Termos de Uso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Termos de uso do Conecta Saúde...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function previewImage(event) {
        const preview = document.getElementById('preview');
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function() {
            preview.src = reader.result;
        }
        
        if (file) {
            reader.readAsDataURL(file);
        }
    }
    
    function selectTipo(tipo) {
        document.querySelectorAll('.btn-option').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`.btn-option[onclick="selectTipo('${tipo}')"]`).classList.add('active');
        document.getElementById(tipo).checked = true;
    }
    
    document.getElementById('formCadastro').addEventListener('submit', function(e) {
        const senha = document.getElementById('senha').value;
        const confirmar = document.getElementById('confirmar_senha').value;
        
        if (senha !== confirmar) {
            e.preventDefault();
            alert('As senhas não coincidem!');
            return false;
        }
        
        if (senha.length < 6) {
            e.preventDefault();
            alert('A senha deve ter no mínimo 6 caracteres!');
            return false;
        }
        
        return true;
    });
    </script>
</body>
</html>