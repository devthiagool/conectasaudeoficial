<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'medico') {
    header('Location: login.php');
    exit;
}

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

// Processar ações (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'confirmar_consulta' || $acao === 'cancelar_consulta' || $acao === 'concluir_consulta') {
        $consulta_id = intval($_POST['consulta_id'] ?? 0);
        $paciente_id = $_POST['paciente_id'] ?? '';
        
        if ($consulta_id && $paciente_id) {
            $arquivo_consultas = "dados/consultas_$paciente_id.json";
            
            if (file_exists($arquivo_consultas)) {
                $consultas = json_decode(file_get_contents($arquivo_consultas), true) ?? [];
                
                foreach ($consultas as &$consulta) {
                    if ($consulta['id'] == $consulta_id) {
                        if ($acao === 'confirmar_consulta') {
                            $consulta['status'] = 'confirmado';
                        } elseif ($acao === 'cancelar_consulta') {
                            $consulta['status'] = 'cancelado';
                        } elseif ($acao === 'concluir_consulta') {
                            $consulta['status'] = 'realizado';
                        }
                        break;
                    }
                }
                
                file_put_contents($arquivo_consultas, json_encode($consultas, JSON_PRETTY_PRINT));
                echo json_encode(['sucesso' => true]);
                exit;
            }
        }
        
        echo json_encode(['sucesso' => false]);
        exit;
    }
}

// Contar por status
$total_agendado = count(array_filter($consultas_medico, fn($c) => $c['status'] === 'agendado'));
$total_confirmado = count(array_filter($consultas_medico, fn($c) => $c['status'] === 'confirmado'));
$total_realizado = count(array_filter($consultas_medico, fn($c) => $c['status'] === 'realizado'));
$total_cancelado = count(array_filter($consultas_medico, fn($c) => $c['status'] === 'cancelado'));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pacientes - Conecta Saúde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0077b6;
            --secondary: #00b4d8;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-agendado { background-color: #fff3cd; color: #856404; }
        .status-confirmado { background-color: #d1ecf1; color: #0c5460; }
        .status-realizado { background-color: #d4edda; color: #155724; }
        .status-cancelado { background-color: #f8d7da; color: #721c24; }
        
        .consulta-card {
            border-left: 4px solid var(--primary);
            transition: all 0.3s;
        }
        
        .consulta-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .filter-badge {
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-badge:hover {
            transform: scale(1.05);
        }
        
        .filter-badge.active {
            background-color: var(--primary) !important;
            color: white !important;
        }
        
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: center;
            border-top: 3px solid var(--primary);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            margin: 10px 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include_once 'navbar.php'; ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">
                <i class="bi bi-people-fill"></i> Meus Pacientes
                <span class="badge bg-primary"><?php echo count($consultas_medico); ?></span>
            </h1>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
        
        <!-- Estatísticas -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="bi bi-calendar2-event fs-3 text-warning"></i>
                <div class="stat-number"><?php echo $total_agendado; ?></div>
                <div class="stat-label">Agendadas</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-calendar-check fs-3 text-info"></i>
                <div class="stat-number"><?php echo $total_confirmado; ?></div>
                <div class="stat-label">Confirmadas</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-check-circle fs-3 text-success"></i>
                <div class="stat-number"><?php echo $total_realizado; ?></div>
                <div class="stat-label">Realizadas</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-x-circle fs-3 text-danger"></i>
                <div class="stat-number"><?php echo $total_cancelado; ?></div>
                <div class="stat-label">Canceladas</div>
            </div>
        </div>
        
        <!-- Filtros -->
        <?php if(count($consultas_medico) > 0): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="mb-3">Filtrar por:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark filter-badge active" data-filter="all">Todas</span>
                    <span class="badge bg-light text-dark filter-badge" data-filter="agendado">Agendadas</span>
                    <span class="badge bg-light text-dark filter-badge" data-filter="confirmado">Confirmadas</span>
                    <span class="badge bg-light text-dark filter-badge" data-filter="realizado">Realizadas</span>
                    <span class="badge bg-light text-dark filter-badge" data-filter="cancelado">Canceladas</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Lista de Pacientes/Consultas -->
        <div class="row" id="consultasContainer">
            <?php foreach($consultas_medico as $consulta): ?>
                <div class="col-lg-6 mb-4 consulta-item" data-status="<?php echo $consulta['status']; ?>">
                    <div class="card consulta-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <i class="bi bi-person-circle"></i> Paciente ID: <?php echo substr($consulta['paciente_id'], 0, 8); ?>...
                                    </h5>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-stethoscope"></i> <?php echo $consulta['especialidade']; ?>
                                    </p>
                                </div>
                                <span class="status-badge status-<?php echo $consulta['status']; ?>">
                                    <?php 
                                    $status_pt = [
                                        'agendado' => 'Agendada',
                                        'confirmado' => 'Confirmada',
                                        'realizado' => 'Realizada',
                                        'cancelado' => 'Cancelada'
                                    ];
                                    echo $status_pt[$consulta['status']] ?? ucfirst($consulta['status']); 
                                    ?>
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-calendar"></i> 
                                    <strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($consulta['data'])); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="bi bi-laptop"></i> 
                                    <strong>Tipo:</strong> 
                                    <span class="badge bg-light text-dark">
                                        <?php echo $consulta['tipo'] == 'presencial' ? 'Presencial' : 'Online'; ?>
                                    </span>
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-chat"></i> 
                                    <strong>Motivo:</strong> <?php echo $consulta['motivo']; ?>
                                </p>
                            </div>
                            
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <button class="btn btn-outline-primary btn-sm" 
                                        onclick="verDetalhes(<?php echo htmlspecialchars(json_encode($consulta)); ?>)">
                                    <i class="bi bi-eye"></i> Ver Detalhes
                                </button>
                                
                                <?php if($consulta['status'] == 'agendado'): ?>
                                    <button class="btn btn-outline-success btn-sm" 
                                            onclick="confirmarConsulta(<?php echo $consulta['id']; ?>, '<?php echo $consulta['paciente_id']; ?>')">
                                        <i class="bi bi-check-circle"></i> Confirmar
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="cancelarConsulta(<?php echo $consulta['id']; ?>, '<?php echo $consulta['paciente_id']; ?>')">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                <?php elseif($consulta['status'] == 'confirmado'): ?>
                                    <button class="btn btn-outline-info btn-sm" 
                                            onclick="concluirConsulta(<?php echo $consulta['id']; ?>, '<?php echo $consulta['paciente_id']; ?>')">
                                        <i class="bi bi-check-all"></i> Marcar Realizada
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="cancelarConsulta(<?php echo $consulta['id']; ?>, '<?php echo $consulta['paciente_id']; ?>')">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small">Sem ações disponíveis</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if(count($consultas_medico) == 0): ?>
            <div class="text-center py-5">
                <i class="bi bi-people-fill display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Nenhuma consulta agendada</h4>
                <p class="text-muted">Você ainda não tem pacientes com consultas agendadas</p>
                <a href="dashboard.php" class="btn btn-primary btn-lg mt-2">
                    <i class="bi bi-arrow-left"></i> Voltar ao Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de Detalhes -->
    <div class="modal fade" id="modalDetalhes" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-check"></i> Detalhes da Consulta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="conteudoDetalhes">
                    <!-- Preenchido por JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Filtro de consultas
    document.querySelectorAll('.filter-badge').forEach(badge => {
        badge.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Atualiza badges ativos
            document.querySelectorAll('.filter-badge').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
            
            // Filtra consultas
            document.querySelectorAll('.consulta-item').forEach(item => {
                if (filter === 'all' || item.getAttribute('data-status') === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    function verDetalhes(consulta) {
        const dataFormatada = new Date(consulta.data).toLocaleDateString('pt-BR') + ' ' + consulta.data.split(' ')[1];
        const statusText = {
            'agendado': 'Agendada',
            'confirmado': 'Confirmada',
            'realizado': 'Realizada',
            'cancelado': 'Cancelada'
        }[consulta.status] || consulta.status;
        
        const html = `
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Data e Hora:</strong>
                        <p><i class="bi bi-calendar"></i> ${dataFormatada}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Especialidade:</strong>
                        <p><i class="bi bi-stethoscope"></i> ${consulta.especialidade}</p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <strong>Tipo:</strong>
                        <p><span class="badge bg-light text-dark">${consulta.tipo === 'presencial' ? 'Presencial' : 'Online'}</span></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p><span class="badge status-${consulta.status}" style="padding: 8px 12px;">${statusText}</span></p>
                    </div>
                </div>
            </div>
            
            <div class="border-top pt-3">
                <strong>Motivo da Consulta:</strong>
                <p>${consulta.motivo}</p>
            </div>
            
            <div class="border-top pt-3">
                <strong>Data do Agendamento:</strong>
                <p>${new Date(consulta.data_agendamento).toLocaleDateString('pt-BR')}</p>
            </div>
        `;
        
        document.getElementById('conteudoDetalhes').innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('modalDetalhes'));
        modal.show();
    }
    
    function confirmarConsulta(id, pacienteId) {
        if (confirm('Tem certeza que deseja confirmar esta consulta?')) {
            const formData = new FormData();
            formData.append('acao', 'confirmar_consulta');
            formData.append('consulta_id', id);
            formData.append('paciente_id', pacienteId);
            
            fetch('meuspacientes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    mostrarNotificacao('✓ Consulta confirmada com sucesso!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    mostrarNotificacao('Erro ao confirmar consulta', 'danger');
                }
            })
            .catch(error => {
                mostrarNotificacao('Erro ao confirmar consulta', 'danger');
                console.error('Erro:', error);
            });
        }
    }
    
    function cancelarConsulta(id, pacienteId) {
        if (confirm('Tem certeza que deseja cancelar esta consulta?')) {
            const formData = new FormData();
            formData.append('acao', 'cancelar_consulta');
            formData.append('consulta_id', id);
            formData.append('paciente_id', pacienteId);
            
            fetch('meuspacientes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    mostrarNotificacao('✓ Consulta cancelada com sucesso!', 'danger');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    mostrarNotificacao('Erro ao cancelar consulta', 'danger');
                }
            })
            .catch(error => {
                mostrarNotificacao('Erro ao cancelar consulta', 'danger');
                console.error('Erro:', error);
            });
        }
    }
    
    function concluirConsulta(id, pacienteId) {
        if (confirm('Tem certeza que deseja marcar esta consulta como realizada?')) {
            const formData = new FormData();
            formData.append('acao', 'concluir_consulta');
            formData.append('consulta_id', id);
            formData.append('paciente_id', pacienteId);
            
            fetch('meuspacientes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    mostrarNotificacao('✓ Consulta marcada como realizada!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    mostrarNotificacao('Erro ao concluir consulta', 'danger');
                }
            })
            .catch(error => {
                mostrarNotificacao('Erro ao concluir consulta', 'danger');
                console.error('Erro:', error);
            });
        }
    }
    
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
