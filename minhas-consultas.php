<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'paciente') {
    header('Location: login.php');
    exit;
}

// Arquivo para armazenar consultas
$arquivo_consultas = 'dados/consultas_' . $_SESSION['usuario_id'] . '.json';

// Criar diretório se não existir
if (!is_dir('dados')) {
    mkdir('dados', 0755, true);
}

// Inicializar arquivo se não existir
if (!file_exists($arquivo_consultas)) {
    file_put_contents($arquivo_consultas, json_encode([], JSON_PRETTY_PRINT));
}

// Carregar consultas
$consultas = json_decode(file_get_contents($arquivo_consultas), true) ?? [];

// Processar ações (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'cancelar_consulta') {
        $id = intval($_POST['id']);
        foreach ($consultas as &$consulta) {
            if ($consulta['id'] === $id) {
                $consulta['status'] = 'cancelado';
                break;
            }
        }
        file_put_contents($arquivo_consultas, json_encode($consultas, JSON_PRETTY_PRINT));
        echo json_encode(['sucesso' => true]);
        exit;
    }
    elseif ($acao === 'remarcar_consulta') {
        $id = intval($_POST['id']);
        $nova_data = $_POST['nova_data'] ?? '';
        $nova_hora = $_POST['nova_hora'] ?? '';
        
        if (empty($nova_data) || empty($nova_hora)) {
            echo json_encode(['sucesso' => false, 'erro' => 'Data e hora são obrigatórias']);
            exit;
        }
        
        foreach ($consultas as &$consulta) {
            if ($consulta['id'] === $id) {
                $consulta['data'] = "$nova_data $nova_hora";
                $consulta['status'] = 'remarcado';
                break;
            }
        }
        file_put_contents($arquivo_consultas, json_encode($consultas, JSON_PRETTY_PRINT));
        echo json_encode(['sucesso' => true]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Consultas - Conecta Saúde</title>
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
        .status-pendente { background-color: #e2e3e5; color: #383d41; }
        .status-remarcado { background-color: #cce5ff; color: #004085; }
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
    </style>
</head>
<body>
    <?php include_once 'navbar.php'; ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">
                <i class="bi bi-calendar-check"></i> Minhas Consultas
                <span class="badge bg-primary"><?php echo count($consultas); ?></span>
            </h1>
            <a href="agendar.php" class="btn btn-primary">
                <i class="bi bi-calendar-plus"></i> Nova Consulta
            </a>
        </div>
        
        <!-- Filtros -->
        <?php if(count($consultas) > 0): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="mb-3">Filtrar por:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-dark filter-badge active" data-filter="all">Todas</span>
                    <span class="badge bg-light text-dark filter-badge" data-filter="agendado">Agendadas</span>
                    <span class="badge bg-light text-dark filter-badge" data-filter="confirmado">Confirmadas</span>
                    <span class="badge bg-light text-dark filter-badge" data-filter="remarcado">Remarcadas</span>
                    <span class="badge bg-light text-dark filter-badge" data-filter="realizado">Realizadas</span>
                    <span class="badge bg-light text-dark filter-badge" data-filter="cancelado">Canceladas</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Lista de Consultas -->
        <div class="row" id="consultasContainer">
            <?php foreach($consultas as $consulta): ?>
                <div class="col-lg-6 mb-4 consulta-item" data-status="<?php echo $consulta['status']; ?>">
                    <div class="card consulta-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1"><?php echo $consulta['medico']; ?></h5>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-star"></i> <?php echo $consulta['especialidade']; ?>
                                    </p>
                                </div>
                                <span class="status-badge status-<?php echo $consulta['status']; ?>">
                                    <?php 
                                    $status_pt = [
                                        'agendado' => 'Agendada',
                                        'confirmado' => 'Confirmada',
                                        'realizado' => 'Realizada',
                                        'cancelado' => 'Cancelada',
                                        'pendente' => 'Pendente',
                                        'remarcado' => 'Remarcada'
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
                                
                                <?php if($consulta['status'] == 'agendado' || $consulta['status'] == 'confirmado' || $consulta['status'] == 'remarcado'): ?>
                                    <button class="btn btn-outline-warning btn-sm" 
                                            onclick="remarcarConsulta(<?php echo $consulta['id']; ?>)">
                                        <i class="bi bi-calendar-x"></i> Remarcar
                                    </button>
                                <?php endif; ?>
                                
                                <?php if($consulta['status'] == 'agendado' || $consulta['status'] == 'confirmado'): ?>
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="cancelarConsulta(<?php echo $consulta['id']; ?>)">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if(count($consultas) == 0): ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Nenhuma consulta agendada</h4>
                <p class="text-muted">Agende sua primeira consulta agora mesmo!</p>
                <a href="agendar.php" class="btn btn-primary btn-lg mt-2">
                    <i class="bi bi-calendar-plus"></i> Agendar Consulta
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

    <!-- Modal de Remarcar -->
    <div class="modal fade" id="modalRemarcar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-x"></i> Remarcar Consulta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formRemarcar" onsubmit="confirmarRemarcar(event)">
                    <div class="modal-body">
                        <input type="hidden" id="remarcarId">
                        <div class="mb-3">
                            <label for="novaData" class="form-label">Nova Data *</label>
                            <input type="date" class="form-control" id="novaData" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="novaHora" class="form-label">Novo Horário *</label>
                            <select class="form-select" id="novaHora" required>
                                <option value="">Selecione um horário</option>
                                <?php
                                $horarios = ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];
                                foreach ($horarios as $horario): ?>
                                    <option value="<?php echo $horario; ?>">
                                        <?php echo $horario; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Confirmar
                        </button>
                    </div>
                </form>
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
        const html = `
            <div class="d-flex align-items-center mb-4">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                     style="width: 60px; height: 60px; color: white; margin-right: 15px;">
                    <i class="bi bi-person"></i>
                </div>
                <div>
                    <h6 class="mb-0">${consulta.medico}</h6>
                    <small class="text-muted">
                        <i class="bi bi-star"></i> ${consulta.especialidade}
                    </small>
                </div>
            </div>
            
            <div class="border-top pt-3">
                <div class="mb-3">
                    <strong>Data e Hora:</strong>
                    <p>${new Date(consulta.data).toLocaleDateString('pt-BR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}).replace(/(\d{2})\/(\d{2})\/(\d{4})/, '$1/$2/$3').split('T')[0]} ${consulta.data.split(' ')[1]}</p>
                </div>
                
                <div class="mb-3">
                    <strong>Tipo:</strong>
                    <p><span class="badge bg-light text-dark">${consulta.tipo === 'presencial' ? 'Presencial' : 'Online'}</span></p>
                </div>
                
                <div class="mb-3">
                    <strong>Status:</strong>
                    <p>
                        <span class="badge status-${consulta.status}" style="padding: 8px 12px;">
                            ${{'agendado': 'Agendada', 'confirmado': 'Confirmada', 'realizado': 'Realizada', 'cancelado': 'Cancelada', 'remarcado': 'Remarcada'}[consulta.status] || consulta.status}
                        </span>
                    </p>
                </div>
                
                <div>
                    <strong>Motivo:</strong>
                    <p>${consulta.motivo}</p>
                </div>
            </div>
        `;
        
        document.getElementById('conteudoDetalhes').innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('modalDetalhes'));
        modal.show();
    }
    
    function cancelarConsulta(id) {
        if (confirm('Tem certeza que deseja cancelar esta consulta?')) {
            const formData = new FormData();
            formData.append('acao', 'cancelar_consulta');
            formData.append('id', id);
            
            fetch('minhas-consultas.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    mostrarNotificacao('✓ Consulta cancelada com sucesso!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                mostrarNotificacao('Erro ao cancelar consulta', 'danger');
                console.error('Erro:', error);
            });
        }
    }
    
    function remarcarConsulta(id) {
        document.getElementById('remarcarId').value = id;
        document.getElementById('novaData').value = '';
        document.getElementById('novaHora').value = '';
        const modal = new bootstrap.Modal(document.getElementById('modalRemarcar'));
        modal.show();
    }
    
    function confirmarRemarcar(event) {
        event.preventDefault();
        
        const id = document.getElementById('remarcarId').value;
        const novaData = document.getElementById('novaData').value;
        const novaHora = document.getElementById('novaHora').value;
        
        const formData = new FormData();
        formData.append('acao', 'remarcar_consulta');
        formData.append('id', id);
        formData.append('nova_data', novaData);
        formData.append('nova_hora', novaHora);
        
        fetch('minhas-consultas.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                mostrarNotificacao('✓ Consulta remarcada com sucesso!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalRemarcar')).hide();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                mostrarNotificacao(data.erro || 'Erro ao remarcar consulta', 'danger');
            }
        })
        .catch(error => {
            mostrarNotificacao('Erro ao remarcar consulta', 'danger');
            console.error('Erro:', error);
        });
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
