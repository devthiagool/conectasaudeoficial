<?php
// Inicia sessão no TOPO do arquivo
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para login
    header('Location: login.php');
    exit;
}

// Dados do usuário
$usuario = [
    'id' => $_SESSION['usuario_id'],
    'nome' => $_SESSION['usuario_nome'] ?? 'Usuário Teste',
    'email' => $_SESSION['usuario_email'] ?? 'teste@email.com',
    'tipo' => $_SESSION['usuario_tipo'] ?? 'paciente'
];

// Carregar foto de perfil
$foto = 'assets/default-avatar.png';
if (isset($_SESSION['usuario_foto']) && !empty($_SESSION['usuario_foto'])) {
    $foto_path = 'uploads/' . $_SESSION['usuario_foto'];
    if (file_exists($foto_path)) {
        $foto = $foto_path;
    }
}

// Carregar consultas do arquivo JSON
$arquivo_consultas = 'dados/consultas_' . $_SESSION['usuario_id'] . '.json';
if (!is_dir('dados')) {
    mkdir('dados', 0755, true);
}
if (!file_exists($arquivo_consultas)) {
    $agendamentos = [];
} else {
    $agendamentos = json_decode(file_get_contents($arquivo_consultas), true) ?? [];
}

// Se não houver consultas, usar dados padrão para demonstração
if (empty($agendamentos)) {
    $agendamentos = [];
}

$total_consultas = count($agendamentos);
$consultas_realizadas = count(array_filter($agendamentos, function($a) { return $a['status'] === 'realizado'; }));
$consultas_agendadas = count(array_filter($agendamentos, function($a) { return $a['status'] === 'agendado' || $a['status'] === 'confirmado'; }));

// Carregar mensagens do arquivo JSON
$arquivo_mensagens = 'dados/mensagens_' . $_SESSION['usuario_id'] . '.json';
$mensagens = [];
$total_mensagens = 0;
if (file_exists($arquivo_mensagens)) {
    $mensagens = json_decode(file_get_contents($arquivo_mensagens), true) ?? [];
    $total_mensagens = count($mensagens);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Conecta Saúde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css">
    <style>
        :root {
            --primary: #0077b6;
            --secondary: #00b4d8;
            --light: #e0f7fa;
            --dark: #023e8a;
            --sidebar-width: 250px;
        }
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: fixed;
            width: var(--sidebar-width);
            left: 0;
            top: 0;
            z-index: 1000;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
        }
        .nav-link {
            color: #333;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(0, 119, 182, 0.1);
            color: var(--primary);
        }
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 15px;
        }
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.bg-primary { background: linear-gradient(135deg, var(--primary), #005a87) !important; }
        .stat-card.bg-success { background: linear-gradient(135deg, #28a745, #1e7e34) !important; }
        .stat-card.bg-warning { background: linear-gradient(135deg, #ffc107, #e0a800) !important; }
        .stat-card.bg-info { background: linear-gradient(135deg, var(--secondary), #0093b8) !important; }
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .badge-agendado { background-color: #ffc107; color: #000; }
        .badge-confirmado { background-color: #17a2b8; color: white; }
        .badge-realizado { background-color: #28a745; color: white; }
        .badge-pendente { background-color: #6c757d; color: white; }
        .badge-cancelado { background-color: #dc3545; color: white; }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar d-none d-lg-block">
        <div class="p-4">
            <!-- Avatar do Usuário -->
            <div class="text-center mb-3">
                <img src="<?php echo $foto; ?>?t=<?php echo time(); ?>" alt="Avatar" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #0077b6;">
            </div>
            
            <h5 class="text-center mb-1"><?php echo $usuario['nome']; ?></h5>
            <p class="text-center text-muted small mb-4">
                <span class="badge bg-<?php echo $usuario['tipo'] == 'medico' ? 'info' : 'success'; ?>">
                    <?php echo ucfirst($usuario['tipo']); ?>
                </span>
            </p>
            
            <!-- Menu -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="perfil.php">
                        <i class="bi bi-person"></i> Meu Perfil
                    </a>
                </li>
                
                <?php if($usuario['tipo'] == 'paciente'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="agendar.php">
                            <i class="bi bi-calendar-plus"></i> Agendar Consulta
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="minhas-consultas.php">
                            <i class="bi bi-calendar-check"></i> Minhas Consultas
                        </a>
                    </li>
                <?php elseif($usuario['tipo'] == 'medico'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="agenda-medico.php">
                            <i class="bi bi-calendar-week"></i> Minha Agenda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="meus-pacientes.php">
                            <i class="bi bi-people"></i> Meus Pacientes
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link" href="mensagens.php">
                        <i class="bi bi-chat"></i> Mensagens
                        <?php if($total_mensagens > 0): ?>
                            <span class="badge bg-danger float-end"><?php echo $total_mensagens; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Mobile Header -->
    <nav class="navbar navbar-dark bg-primary d-lg-none">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <strong>🏥 Conecta Saúde</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="text-center mb-3">
                <img src="<?php echo $foto; ?>?t=<?php echo time(); ?>" alt="Avatar" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #0077b6;">
            </div>
            <h6 class="text-center"><?php echo $usuario['nome']; ?></h6>
            <p class="text-center text-muted small mb-4"><?php echo ucfirst($usuario['tipo']); ?></p>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="perfil.php">
                        <i class="bi bi-person"></i> Meu Perfil
                    </a>
                </li>
                <!-- Adicione os outros itens do menu aqui -->
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">Dashboard</h1>
                <div class="btn-group">
                    <button class="btn btn-outline-primary">Hoje</button>
                    <button class="btn btn-outline-primary">Semana</button>
                    <button class="btn btn-outline-primary active">Mês</button>
                </div>
            </div>

            <!-- Cards de Estatísticas -->
            <div class="row mb-4">
                <?php if($usuario['tipo'] == 'paciente'): ?>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white stat-card bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Consultas Agendadas</h6>
                                        <h2 class="mb-0"><?php echo $total_consultas; ?></h2>
                                    </div>
                                    <i class="bi bi-calendar-check fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white stat-card bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Consultas Realizadas</h6>
                                        <h2 class="mb-0"><?php echo $consultas_realizadas; ?></h2>
                                    </div>
                                    <i class="bi bi-check-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white stat-card bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Próxima Consulta</h6>
                                        <h6 class="mb-0">15/01/2024</h6>
                                    </div>
                                    <i class="bi bi-clock fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white stat-card bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Mensagens</h6>
                                        <h2 class="mb-0"><?php echo $total_mensagens; ?></h2>
                                    </div>
                                    <i class="bi bi-chat fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif($usuario['tipo'] == 'medico'): ?>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white stat-card bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Consultas</h6>
                                        <h2 class="mb-0">42</h2>
                                    </div>
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white stat-card bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Agendadas</h6>
                                        <h2 class="mb-0">12</h2>
                                    </div>
                                    <i class="bi bi-calendar fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white stat-card bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Realizadas</h6>
                                        <h2 class="mb-0">30</h2>
                                    </div>
                                    <i class="bi bi-check-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white stat-card bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Pacientes</h6>
                                        <h2 class="mb-0">25</h2>
                                    </div>
                                    <i class="bi bi-person-plus fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Próximas Consultas -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-event"></i>
                                <?php echo $usuario['tipo'] == 'paciente' ? 'Minhas Consultas' : 'Próximas Consultas'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if(count($agendamentos) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Profissional</th>
                                                <th>Data/Hora</th>
                                                <th>Tipo</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($agendamentos as $consulta): ?>
                                                <tr>
                                                    <td><?php echo $consulta['medico']; ?></td>
                                                    <td>
                                                        <?php echo date('d/m/Y H:i', strtotime($consulta['data'])); ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark">
                                                            <?php echo ucfirst($consulta['tipo']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge-status badge-<?php echo $consulta['status']; ?>">
                                                            <?php echo ucfirst($consulta['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary">Ver</button>
                                                        <?php if($consulta['status'] == 'agendado'): ?>
                                                            <button class="btn btn-sm btn-outline-warning">Remarcar</button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Nenhuma consulta agendada</p>
                                    <?php if($usuario['tipo'] == 'paciente'): ?>
                                        <a href="agendar.php" class="btn btn-primary">Agendar Consulta</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Calendário -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-calendar"></i> Calendário</h5>
                        </div>
                        <div class="card-body">
                            <div id="calendar" style="min-height: 300px;"></div>
                        </div>
                    </div>

                    <!-- Atalhos Rápidos -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-lightning"></i> Atalhos Rápidos</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <?php if($usuario['tipo'] == 'paciente'): ?>
                                    <a href="agendar.php" class="btn btn-outline-primary">
                                        <i class="bi bi-calendar-plus"></i> Nova Consulta
                                    </a>
                                    <a href="mensagens.php" class="btn btn-outline-success">
                                        <i class="bi bi-chat"></i> Ver Mensagens
                                    </a>
                                    <a href="perfil.php" class="btn btn-outline-info">
                                        <i class="bi bi-pencil"></i> Editar Perfil
                                    </a>
                                <?php else: ?>
                                    <a href="agenda-medico.php" class="btn btn-outline-primary">
                                        <i class="bi bi-calendar-week"></i> Minha Agenda
                                    </a>
                                    <a href="meus-pacientes.php" class="btn btn-outline-success">
                                        <i class="bi bi-people"></i> Meus Pacientes
                                    </a>
                                    <a href="mensagens.php" class="btn btn-outline-info">
                                        <i class="bi bi-chat"></i> Mensagens
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/pt-br.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calendário
        var calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: [
                    {
                        title: 'Consulta com Dr. Carlos',
                        start: '2024-01-15T14:00:00',
                        color: '#0077b6'
                    },
                    {
                        title: 'Consulta com Dra. Ana',
                        start: '2024-01-20T10:30:00',
                        color: '#28a745'
                    }
                ]
            });
            calendar.render();
        }
        
        // Menu mobile
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenu) {
            mobileMenu.addEventListener('hidden.bs.offcanvas', function () {
                document.body.classList.remove('offcanvas-open');
            });
        }
    });
    </script>
</body>
</html>