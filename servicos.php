<?php
// servicos.php - Página de Serviços CONECTASAUDE
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços - CONECTASAUDE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .service-card {
            transition: transform 0.3s;
            border: 1px solid #e0e0e0;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .service-icon {
            font-size: 2.5rem;
            color: #198754; /* Verde do CONECTASAUDE */
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Incluir navbar -->
    <?php include_once 'navbar.php'; ?>
    
    <main class="container mt-4">
        <!-- Cabeçalho -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-6 fw-bold">Nossos Serviços de Saúde</h1>
                <p class="lead text-muted">Cuidado integral para você e sua família</p>
                <div class="border-bottom mx-auto" style="width: 100px; border-color: #198754!important; border-width: 3px;"></div>
            </div>
        </div>
        
        <!-- Serviços Principais -->
        <div class="row g-4 mb-5">
            <!-- Telemedicina -->
            <div class="col-md-4">
                <div class="card service-card h-100 border-0">
                    <div class="card-body text-center p-4">
                        <div class="service-icon">
                            <i class="bi bi-camera-video-fill"></i>
                        </div>
                        <h3 class="card-title h5 fw-bold mb-3">Consultas Online</h3>
                        <p class="card-text text-muted small">
                            Atendimento médico por videoconferência com especialistas de diversas áreas.
                            Agende sua consulta sem sair de casa.
                        </p>
                        <ul class="list-unstyled text-start mt-3 small">
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Mais de 50 especialidades</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Atendimento 24/7</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Receita digital</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center pb-4">
                        <?php if(isset($_SESSION['usuario_id'])): ?>
                            <a href="agendar.php?servico=online" class="btn btn-success">
                                <i class="bi bi-calendar-plus me-2"></i>Agendar Consulta
                            </a>
                        <?php else: ?>
                            <a href="cadastro.php" class="btn btn-outline-success">
                                <i class="bi bi-person-plus me-2"></i>Criar Conta
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Agendamento Presencial -->
            <div class="col-md-4">
                <div class="card service-card h-100 border-0">
                    <div class="card-body text-center p-4">
                        <div class="service-icon">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <h3 class="card-title h5 fw-bold mb-3">Consultas Presenciais</h3>
                        <p class="card-text text-muted small">
                            Agendamento de consultas em clínicas e hospitais parceiros em toda a cidade.
                            Encontre o especialista mais próximo de você.
                        </p>
                        <ul class="list-unstyled text-start mt-3 small">
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Rede credenciada</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Resultados online</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Descontos especiais</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center pb-4">
                        <?php if(isset($_SESSION['usuario_id'])): ?>
                            <a href="agendar.php?servico=presencial" class="btn btn-success">
                                <i class="bi bi-geo-alt me-2"></i>Encontrar Clínica
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-success">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Fazer Login
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Exames -->
            <div class="col-md-4">
                <div class="card service-card h-100 border-0">
                    <div class="card-body text-center p-4">
                        <div class="service-icon">
                            <i class="bi bi-clipboard2-pulse-fill"></i>
                        </div>
                        <h3 class="card-title h5 fw-bold mb-3">Agendamento de Exames</h3>
                        <p class="card-text text-muted small">
                            Marque seus exames laboratoriais e de imagem com facilidade.
                            Resultados disponíveis na plataforma.
                        </p>
                        <ul class="list-unstyled text-start mt-3 small">
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Laboratórios parceiros</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Laudos online</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Acesso ao histórico</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center pb-4">
                        <a href="agendar.php?servico=exames" class="btn btn-success">
                            <i class="bi bi-search-heart me-2"></i>Ver Exames
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Outros Serviços -->
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="text-center mb-4">Mais Serviços</h4>
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="p-3 border rounded bg-light text-center">
                            <i class="bi bi-capsule-pill text-success d-block mb-2 fs-4"></i>
                            <span class="small fw-medium">Farmácia Digital</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 border rounded bg-light text-center">
                            <i class="bi bi-heart-pulse text-success d-block mb-2 fs-4"></i>
                            <span class="small fw-medium">Monitoramento</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 border rounded bg-light text-center">
                            <i class="bi bi-file-earmark-text text-success d-block mb-2 fs-4"></i>
                            <span class="small fw-medium">Prontuário Digital</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 border rounded bg-light text-center">
                            <i class="bi bi-shield-check text-success d-block mb-2 fs-4"></i>
                            <span class="small fw-medium">Segurança de Dados</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Call to Action -->
        <div class="row">
            <div class="col-12">
                <div class="bg-success text-white rounded-3 p-5 text-center">
                    <h3 class="mb-3">Comece a Cuidar da Sua Saúde Hoje!</h3>
                    <p class="mb-4">Cadastre-se gratuitamente e tenha acesso a todos os nossos serviços.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="cadastro.php" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-person-plus me-2"></i>Criar Minha Conta
                        </a>
                        <a href="sobre.php" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-info-circle me-2"></i>Conhecer Mais
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Incluir footer -->
    <?php include_once 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>