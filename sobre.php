<?php
session_start(); // Só isso no topo, sem includes
$titulo = "Sobre Nós - Conecta Saúde";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0077b6;
            --secondary: #00b4d8;
            --light: #e0f7fa;
            --dark: #023e8a;
        }
        .hero-sobre {
            background: linear-gradient(rgba(0, 119, 182, 0.9), rgba(0, 180, 216, 0.9));
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        .icon-feature {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        .team-card {
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
        }
        .team-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .counter {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary);
        }
        .card-hover {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Inclui navbar (não tem session_start dentro dele) -->
    <?php include_once 'navbar.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-sobre text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Sobre o Conecta Saúde</h1>
            <p class="lead">Revolucionando o acesso à saúde no Brasil desde 2025</p>
        </div>
    </section>
    
    <!-- Missão, Visão, Valores -->
    <section class="container py-5">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm card-hover">
                    <div class="card-body text-center p-4">
                        <div class="icon-feature">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <h3 class="card-title text-primary">Nossa Missão</h3>
                        <p class="card-text">
                            Conectar pacientes e profissionais de saúde de forma simples, rápida e acessível, 
                            promovendo o bem-estar e facilitando o acesso a serviços médicos de qualidade.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm card-hover">
                    <div class="card-body text-center p-4">
                        <div class="icon-feature">
                            <i class="bi bi-eye"></i>
                        </div>
                        <h3 class="card-title text-primary">Nossa Visão</h3>
                        <p class="card-text">
                            Ser a principal plataforma de saúde digital do Brasil, reconhecida pela excelência 
                            no atendimento e inovação tecnológica na área da saúde.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm card-hover">
                    <div class="card-body text-center p-4">
                        <div class="icon-feature">
                            <i class="bi bi-heart"></i>
                        </div>
                        <h3 class="card-title text-primary">Nossos Valores</h3>
                        <ul class="list-unstyled text-start">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Ética e transparência</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Inovação constante</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Compromisso com a saúde</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i> Acessibilidade para todos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- História -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="text-primary mb-4">Nossa História</h2>
                    <p class="lead">
                        O Conecta Saúde nasceu em 2025 da necessidade de simplificar o acesso à saúde no Brasil.
                    </p>
                    <p>
                        Fundada por um grupo de alunos, nossa plataforma foi criada para 
                        reduzir as barreiras entre pacientes e profissionais de saúde. Percebemos que muitas 
                        pessoas enfrentavam dificuldades para marcar consultas, encontrar especialistas 
                        próximos e acessar informações confiáveis sobre saúde.
                    </p>
                    <p>
                        Hoje, somos uma equipe multidisciplinar comprometida em transformar a experiência 
                        em saúde através da tecnologia, sempre mantendo o foco no cuidado humano e na 
                        qualidade do atendimento.
                    </p>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&q=80" 
                         class="img-fluid rounded shadow" 
                         alt="Equipe Conecta Saúde">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Estatísticas -->
    <section class="container py-5">
        <h2 class="text-center text-primary mb-5">Nossos Números</h2>
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="counter" data-count="1500">0</div>
                <p class="text-muted">Pacientes Atendidos</p>
            </div>
            <div class="col-md-3 mb-4">
                <div class="counter" data-count="200">0</div>
                <p class="text-muted">Profissionais Cadastrados</p>
            </div>
            <div class="col-md-3 mb-4">
                <div class="counter" data-count="5000">0</div>
                <p class="text-muted">Consultas Realizadas</p>
            </div>
            <div class="col-md-3 mb-4">
                <div class="counter" data-count="25">0</div>
                <p class="text-muted">Cidades Atendidas</p>
            </div>
        </div>
    </section>
    
    <!-- Equipe -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center text-primary mb-5">Conheça Nossa Equipe</h2>
            <div class="row justify-content-center">
                <!-- Thiago Oliveira -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card team-card border-0 shadow-sm">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             class="card-img-top team-img" 
                             alt="Thiago Oliveira">
                        <div class="card-body text-center">
                            <h5 class="card-title">Thiago Oliveira</h5>
                            <p class="text-muted">Dev Back-End</p>
                            <p class="small">Junior</p>
                        </div>
                    </div>
                </div>
                
                <!-- Yago Miguel -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card team-card border-0 shadow-sm">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             class="card-img-top team-img" 
                             alt="Yago Miguel">
                        <div class="card-body text-center">
                            <h5 class="card-title">Yago Miguel</h5>
                            <p class="text-muted">Projetista</p>
                            <p class="small">CEO</p>
                        </div>
                    </div>
                </div>
                
                <!-- Brenno Ventura -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card team-card border-0 shadow-sm">
                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             class="card-img-top team-img" 
                             alt="Brenno Ventura">
                        <div class="card-body text-center">
                            <h5 class="card-title">Brenno Ventura</h5>
                            <p class="text-muted">Dev Front-End</p>
                            <p class="small">Junior</p>
                        </div>
                    </div>
                </div>
                
                <!-- Allan Felipe -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card team-card border-0 shadow-sm">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             class="card-img-top team-img" 
                             alt="Allan Felipe">
                        <div class="card-body text-center">
                            <h5 class="card-title">Allan Felipe</h5>
                            <p class="text-muted">Multifuncional</p>
                            <p class="small">Versátil</p>
                        </div>
                    </div>
                </div>

                <!-- Lilian Isadora -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card team-card border-0 shadow-sm">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             class="card-img-top team-img" 
                             alt="Lilian Isadora">
                        <div class="card-body text-center">
                            <h5 class="card-title">Lilian Isadora</h5>
                            <p class="text-muted">Designer</p>
                            <p class="small">UI/UX</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA -->
    <section class="bg-primary text-white py-5">
        <div class="container text-center">
            <h2 class="mb-4">Faça Parte Dessa Transformação!</h2>
            <p class="lead mb-4">
                Seja um paciente ou profissional de saúde, junte-se a nós para construir um futuro mais saudável.
            </p>
            <div class="d-flex justify-content-center flex-wrap gap-3">
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <a href="dashboard.php" class="btn btn-light btn-lg">
                        <i class="bi bi-speedometer2"></i> Acessar Dashboard
                    </a>
                    <a href="agendar.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-calendar-plus"></i> Agendar Consulta
                    </a>
                <?php else: ?>
                    <a href="cadastro.php?tipo=paciente" class="btn btn-light btn-lg">Cadastre-se como Paciente</a>
                    <a href="cadastro.php?tipo=medico" class="btn btn-outline-light btn-lg">Cadastre-se como Profissional</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Footer Simples (no próprio arquivo, sem include) -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0"> Conecta Saúde</h5>
                    <p class="small mb-0 mt-2">Sua saúde em primeiro lugar.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="small mb-0">
                        &copy; 2025 Conecta Saúde. Todos os direitos reservados.<br>
                        <a href="index.php" class="text-white text-decoration-none">Home</a> | 
                        <a href="sobre.php" class="text-white text-decoration-none">Sobre</a> | 
                        <a href="contato.php" class="text-white text-decoration-none">Contato</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animação dos contadores
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.counter');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const increment = target / 200;
                let current = 0;
                
                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        counter.textContent = Math.ceil(current);
                        setTimeout(updateCounter, 10);
                    } else {
                        counter.textContent = target;
                    }
                };
                
                // Inicia animação quando visível
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCounter();
                            observer.unobserve(entry.target);
                        }
                    });
                });
                
                observer.observe(counter);
            });
        });
    </script>
</body>
</html>