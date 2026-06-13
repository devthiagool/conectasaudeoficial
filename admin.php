<?php
session_start();
require_once __DIR__ . '/inc/users.php';
require_once __DIR__ . '/inc/log.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$usuarios = [];
$mensagem = '';

if (file_exists('usuarios.json')) {
    $usuarios = json_decode(file_get_contents('usuarios.json'), true) ?? [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $usuario_id = $_POST['usuario_id'] ?? '';

    // Use helpers for modifications and logging
    $target = find_user_by_id($usuario_id);
    if ($target) {
        if ($acao === 'autorizar') {
            update_user($usuario_id, ['status' => 'aprovado']);
            audit_log('user_authorized', $usuario_id);
            $mensagem = 'Usuário autorizado com sucesso.';
        } elseif ($acao === 'rejeitar') {
            update_user($usuario_id, ['status' => 'rejeitado']);
            audit_log('user_rejected', $usuario_id);
            $mensagem = 'Cadastro rejeitado.';
        } elseif ($acao === 'excluir') {
            if (($target['id'] ?? '') === 'admin000000000' || strtolower($target['email'] ?? '') === 'admin@conectasaude.com') {
                $mensagem = 'Não é possível excluir o usuário administrador.';
            } else {
                if (delete_user($usuario_id)) {
                    audit_log('user_deleted', $usuario_id);
                    $mensagem = 'Usuário excluído com sucesso.';
                } else {
                    $mensagem = 'Erro ao excluir usuário.';
                }
            }
        } elseif ($acao === 'alterar') {
            $new = [];
            $new['nome'] = htmlspecialchars($_POST['nome'] ?? $target['nome']);
            $new['email'] = htmlspecialchars($_POST['email'] ?? $target['email']);
            $new['tipo'] = in_array($_POST['tipo'] ?? $target['tipo'], ['paciente', 'agente', 'medico', 'admin']) ? $_POST['tipo'] : $target['tipo'];
            $new['status'] = in_array($_POST['status'] ?? ($target['status'] ?? 'pendente'), ['pendente', 'aprovado', 'rejeitado']) ? ($_POST['status'] ?? ($target['status'] ?? 'pendente')) : ($target['status'] ?? 'pendente');
            if ((($target['id'] ?? '') === 'admin000000000') || (strtolower($target['email'] ?? '') === 'admin@conectasaude.com')) {
                $new['tipo'] = 'admin';
                $new['status'] = 'aprovado';
            }
            if (update_user($usuario_id, $new)) {
                audit_log('user_updated', $usuario_id . ' by ' . ($_SESSION['usuario_email'] ?? '')); 
                $mensagem = 'Dados do usuário atualizados.';
            } else {
                $mensagem = 'Erro ao atualizar usuário.';
            }
        }
    }

    header('Location: admin.php?mensagem=' . urlencode($mensagem));
    exit;
}

if (isset($_GET['mensagem'])) {
    $mensagem = htmlspecialchars($_GET['mensagem']);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração - Conecta Saúde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .status-badge {
            border-radius: 10px;
            padding: 6px 10px;
            font-size: 0.85rem;
        }
        .status-aprovado { background: #d1e7dd; color: #0f5132; }
        .status-pendente { background: #fff3cd; color: #664d03; }
        .status-rejeitado { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>
    <?php include_once 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">Painel de Administração</h4>
                            <p class="text-muted mb-0">Gerencie usuários, autorize agentes comunitários e edite dados.</p>
                        </div>
                        <div>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar ao Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <?php if ($mensagem): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $mensagem; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th>E-mail</th>
                                        <th>Tipo</th>
                                        <th>Status</th>
                                        <th>Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                            <td><?php echo ucfirst($usuario['tipo']); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $usuario['status'] ?? 'pendente'; ?>">
                                                    <?php echo ucfirst($usuario['status'] ?? 'pendente'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($usuario['data_cadastro'] ?? ''); ?></td>
                                            <td>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <?php if (( $usuario['tipo'] === 'agente' || ($usuario['status'] ?? '') === 'pendente') && ($usuario['status'] ?? '') !== 'aprovado'): ?>
                                                        <form method="POST" action="" class="d-inline">
                                                            <input type="hidden" name="acao" value="autorizar">
                                                            <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-success">Autorizar</button>
                                                        </form>
                                                        <form method="POST" action="" class="d-inline">
                                                            <input type="hidden" name="acao" value="rejeitar">
                                                            <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-warning">Rejeitar</button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal-<?php echo $usuario['id']; ?>">
                                                        Editar
                                                    </button>
                                                    <?php if (($usuario['id'] ?? '') === 'admin000000000' || strtolower($usuario['email'] ?? '') === 'admin@conectasaude.com'): ?>
                                                        <button type="button" class="btn btn-sm btn-danger" disabled title="Conta administrativa protegida">Excluir</button>
                                                    <?php else: ?>
                                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este usuário?');">
                                                            <input type="hidden" name="acao" value="excluir">
                                                            <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Modal de edição -->
                                                <div class="modal fade" id="editarUsuarioModal-<?php echo $usuario['id']; ?>" tabindex="-1" aria-labelledby="editarUsuarioLabel-<?php echo $usuario['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editarUsuarioLabel-<?php echo $usuario['id']; ?>">Editar Usuário</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST" action="">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="acao" value="alterar">
                                                                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Nome</label>
                                                                            <input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">E-mail</label>
                                                                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Tipo</label>
                                                                            <?php if ((($usuario['id'] ?? '') === 'admin000000000') || (strtolower($usuario['email'] ?? '') === 'admin@conectasaude.com')): ?>
                                                                                <select class="form-select" name="tipo" disabled>
                                                                                    <option value="admin" selected>Administrador</option>
                                                                                </select>
                                                                                <input type="hidden" name="tipo" value="admin">
                                                                            <?php else: ?>
                                                                                <select class="form-select" name="tipo">
                                                                                    <option value="paciente" <?php echo $usuario['tipo'] === 'paciente' ? 'selected' : ''; ?>>Paciente</option>
                                                                                    <option value="agente" <?php echo $usuario['tipo'] === 'agente' ? 'selected' : ''; ?>>Agente</option>
                                                                                    <option value="medico" <?php echo $usuario['tipo'] === 'medico' ? 'selected' : ''; ?>>Médico</option>
                                                                                    <option value="admin" <?php echo $usuario['tipo'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                                                                </select>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Status</label>
                                                                            <?php if ((($usuario['id'] ?? '') === 'admin000000000') || (strtolower($usuario['email'] ?? '') === 'admin@conectasaude.com')): ?>
                                                                                <select class="form-select" name="status" disabled>
                                                                                    <option value="aprovado" selected>Aprovado</option>
                                                                                </select>
                                                                                <input type="hidden" name="status" value="aprovado">
                                                                            <?php else: ?>
                                                                                <select class="form-select" name="status">
                                                                                    <option value="pendente" <?php echo ($usuario['status'] ?? 'pendente') === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                                                                    <option value="aprovado" <?php echo ($usuario['status'] ?? '') === 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                                                                                    <option value="rejeitado" <?php echo ($usuario['status'] ?? '') === 'rejeitado' ? 'selected' : ''; ?>>Rejeitado</option>
                                                                                </select>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
