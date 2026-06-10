<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_tipo'], ['medico', 'agente'])) {
    header('Location: login.php');
    exit;
}

$sucesso = '';
$erro = '';

// Diretório de dados
if (!is_dir('dados')) {
    mkdir('dados', 0755, true);
}

// Carregar todas as consultas agendadas de todos os pacientes
$consultas_medico = [];
$dados_dir = 'dados/';

if (is_dir($dados_dir)) {
    $arquivos = scandir($dados_dir);
    foreach ($arquivos as $arquivo) {
        if (strpos($arquivo, 'consultas_') === 0 && pathinfo($arquivo, PATHINFO_EXTENSION) === 'json') {
            $arquivo_path = $dados_dir . $arquivo;
            $consultas = json_decode(file_get_contents($arquivo_path), true) ?? [];
            
            // Adicionar todas as consultas (futuramente filtrar por médico)
            foreach ($consultas as $consulta) {
                $consulta['paciente_id'] = str_replace(['consultas_', '.json'], '', $arquivo);
                $consultas_medico[] = $consulta;
            }
        }
    }
}

// Ordenar por data
usort($consultas_medico, function($a, $b) {
    return strtotime($a['data']) - strtotime($b['data']);
});

// Filtro por status
$status_filtro = $_GET['status'] ?? 'todos';
$consultas_filtradas = $consultas_medico;

if ($status_filtro !== 'todos') {
    $consultas_filtradas = array_filter($consultas_filtradas, function($c) use ($status_filtro) {
        return $c['status'] === $status_filtro;
    });
}

// Processar ações (confirmar, cancelar, etc)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = $_POST['acao'] ?? '';
    $consulta_id = intval($_POST['consulta_id'] ?? 0);
    $paciente_id = $_POST['paciente_id'] ?? '';
    
    if ($acao && $consulta_id && $paciente_id) {
        $arquivo_consultas = "dados/consultas_$paciente_id.json";
        
        if (file_exists($arquivo_consultas)) {
            $consultas = json_decode(file_get_contents($arquivo_consultas), true) ?? [];
            
            foreach ($consultas as &$consulta) {
                if ($consulta['id'] == $consulta_id) {
                    if ($acao === 'confirmar') {
                        $consulta['status'] = 'confirmado';
                        $sucesso = "Consulta confirmada com sucesso!";
                    } elseif ($acao === 'cancelar') {
                        $consulta['status'] = 'cancelado';
                        $sucesso = "Consulta cancelada!";
                    } elseif ($acao === 'concluir') {
                        $consulta['status'] = 'concluído';
                        $sucesso = "Consulta marcada como concluída!";
                    }
                    break;
                }
            }
            
            file_put_contents($arquivo_consultas, json_encode($consultas, JSON_PRETTY_PRINT));
            
            // Recarregar dados
            $consultas_medico = [];
            $arquivos = scandir($dados_dir);
            foreach ($arquivos as $arquivo) {
                if (strpos($arquivo, 'consultas_') === 0 && pathinfo($arquivo, PATHINFO_EXTENSION) === 'json') {
                    $arquivo_path = $dados_dir . $arquivo;
                    $consultas_temp = json_decode(file_get_contents($arquivo_path), true) ?? [];
                    foreach ($consultas_temp as $consulta) {
                        $consulta['paciente_id'] = str_replace(['consultas_', '.json'], '', $arquivo);
                        $consultas_medico[] = $consulta;
                    }
                }
            }
            
            usort($consultas_medico, function($a, $b) {
                return strtotime($a['data']) - strtotime($b['data']);
            });
            
            $consultas_filtradas = $consultas_medico;
            if ($status_filtro !== 'todos') {
                $consultas_filtradas = array_filter($consultas_filtradas, function($c) use ($status_filtro) {
                    return $c['status'] === $status_filtro;
                });
            }
        }
    }
}

// Contar por status
$total_agendado = count(array_filter($consultas_medico, fn($c) => $c['status'] === 'agendado'));
$total_confirmado = count(array_filter($consultas_medico, fn($c) => $c['status'] === 'confirmado'));
$total_concluido = count(array_filter($consultas_medico, fn($c) => $c['status'] === 'concluído'));
$total_cancelado = count(array_filter($consultas_medico, fn($c) => $c['status'] === 'cancelado'));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - Conecta Saúde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0077b6;
            --secondary: #00b4d8;
        }
        
        .agenda-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            border-radius: 15px;
        }
        
        .status-badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-agendado {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-confirmado {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-concluido {
            background-color: #cfe2ff;
            color: #084298;
        }
        
        .status-cancelado {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .consulta-card {
            border: none;
            border-left: 5px solid var(--primary);
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .consulta-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .stat-box {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .filter-btn {
            border: 2px solid #dee2e6;
            background: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            margin: 5px;
        }
        
        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .filter-btn:hover {
            border-color: var(--primary);
        }
        
        .consulta-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-icon {
            color: var(--primary);
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <?php include_once 'navbar.php'; ?>

    <div class="container py-4">
        <!-- Cabeçalho -->
        <div class="agenda-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2"><i class="bi bi-calendar2-check"></i> Minha Agenda</h1>
                    <p class="mb-0">Gerencie suas consultas agendadas</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="dashboard.php" class="btn btn-light">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <?php if($sucesso): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo $sucesso; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $total_agendado; ?></div>
                    <div class="stat-label">Agendados</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $total_confirmado; ?></div>
                    <div class="stat-label">Confirmados</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $total_concluido; ?></div>
                    <div class="stat-label">Concluídos</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $total_cancelado; ?></div>
                    <div class="stat-label">Cancelados</div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-4">
            <h5 class="mb-3">Filtrar por Status:</h5>
            <div>
                <a href="?status=todos" class="filter-btn <?php echo $status_filtro === 'todos' ? 'active' : ''; ?>">
                    Todos
                </a>
                <a href="?status=agendado" class="filter-btn <?php echo $status_filtro === 'agendado' ? 'active' : ''; ?>">
                    Agendados
                </a>
                <a href="?status=confirmado" class="filter-btn <?php echo $status_filtro === 'confirmado' ? 'active' : ''; ?>">
                    Confirmados
                </a>
                <a href="?status=concluído" class="filter-btn <?php echo $status_filtro === 'concluído' ? 'active' : ''; ?>">
                    Concluídos
                </a>
                <a href="?status=cancelado" class="filter-btn <?php echo $status_filtro === 'cancelado' ? 'active' : ''; ?>">
                    Cancelados
                </a>
            </div>
        </div>

        <!-- Lista de Consultas -->
        <div class="row">
            <div class="col-lg-12">
                <?php if(empty($consultas_filtradas)): ?>
                    <div class="card border-0 text-center p-5 bg-light">
                        <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma consulta encontrada</h5>
                        <p class="text-muted small">Não há consultas com este filtro</p>
                    </div>
                <?php else: ?>
                    <?php foreach($consultas_filtradas as $consulta): ?>
                        <div class="card consulta-card">
                            <div class="card-body p-4">
                                <div class="row align-items-start">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <span class="status-badge status-<?php echo str_replace('ó', 'ã', $consulta['status']); ?>">
                                                <?php echo ucfirst($consulta['status']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="consulta-info">
                                            <div class="info-item">
                                                <i class="bi bi-calendar-event info-icon"></i>
                                                <div>
                                                    <small class="text-muted">Data e Hora</small>
                                                    <div class="fw-bold">
                                                        <?php echo date('d/m/Y H:i', strtotime($consulta['data'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="info-item">
                                                <i class="bi bi-stethoscope info-icon"></i>
                                                <div>
                                                    <small class="text-muted">Especialidade</small>
                                                    <div class="fw-bold"><?php echo $consulta['especialidade']; ?></div>
                                                </div>
                                            </div>
                                            
                                            <div class="info-item">
                                                <i class="bi bi-geo-alt info-icon"></i>
                                                <div>
                                                    <small class="text-muted">Tipo</small>
                                                    <div class="fw-bold">
                                                        <?php echo ucfirst($consulta['tipo']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="info-item">
                                                <i class="bi bi-chat-left-text info-icon"></i>
                                                <div>
                                                    <small class="text-muted">Motivo</small>
                                                    <div class="fw-bold" style="max-width: 200px;">
                                                        <?php echo substr($consulta['motivo'], 0, 50); ?>...
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="d-flex flex-column gap-2">
                                            <?php if($consulta['status'] === 'agendado'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="acao" value="confirmar">
                                                    <input type="hidden" name="consulta_id" value="<?php echo $consulta['id']; ?>">
                                                    <input type="hidden" name="paciente_id" value="<?php echo $consulta['paciente_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i> Confirmar
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="acao" value="cancelar">
                                                    <input type="hidden" name="consulta_id" value="<?php echo $consulta['id']; ?>">
                                                    <input type="hidden" name="paciente_id" value="<?php echo $consulta['paciente_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja cancelar?');">
                                                        <i class="bi bi-x-circle"></i> Cancelar
                                                    </button>
                                                </form>
                                            <?php elseif($consulta['status'] === 'confirmado'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="acao" value="concluir">
                                                    <input type="hidden" name="consulta_id" value="<?php echo $consulta['id']; ?>">
                                                    <input type="hidden" name="paciente_id" value="<?php echo $consulta['paciente_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-info">
                                                        <i class="bi bi-check-all"></i> Marcar como Concluída
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="acao" value="cancelar">
                                                    <input type="hidden" name="consulta_id" value="<?php echo $consulta['id']; ?>">
                                                    <input type="hidden" name="paciente_id" value="<?php echo $consulta['paciente_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja cancelar?');">
                                                        <i class="bi bi-x-circle"></i> Cancelar
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                    <i class="bi bi-lock"></i> Sem ações
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
