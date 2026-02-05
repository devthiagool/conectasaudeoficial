<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conecta Saúde - Página Inicial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0077b6;
            --secondary: #00b4d8;
            --light: #e0f7fa;
            --dark: #023e8a;
        }
        .hero {
            background: linear-gradient(135deg, var(--light), #ffffff);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80') center/cover;
            opacity: 0.1;
            z-index: 0;
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            overflow: hidden;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--primary);
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: var(--dark);
            border-color: var(--dark);
            transform: translateY(-2px);
        }
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
        }
        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .welcome-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Inclui a navbar -->
    <?php include_once 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-primary mb-4">Bem-vindo ao Conecta Saúde!</h1>
                    <p class="lead mb-4">
                        Conecte-se com profissionais de saúde, agende consultas e cuide da sua saúde 
                        de forma simples e acessível.
                    </p>
                    
                    <?php if(isset($_SESSION['usuario_id'])): ?>
                        <!-- Mensagem de boas-vindas para usuário logado -->
                        <div class="welcome-card">
                            <div class="d-flex align-items-center mb-3">
                                <?php
                                $foto = isset($_SESSION['usuario_foto']) ? 'uploads/' . $_SESSION['usuario_foto'] : 'assets/default-avatar.png';
                                if (!file_exists($foto)) {
                                    $foto = 'assets/default-avatar.png';
                                }
                                ?>
                                <img src="<?php echo $foto; ?>?t=<?php echo time(); ?>" 
                                     class="rounded-circle me-3" 
                                     alt="<?php echo $_SESSION['usuario_nome']; ?>"
                                     style="width: 60px; height: 60px; object-fit: cover; border: 3px solid white;">
                                <div>
                                    <h4 class="mb-0">Olá, <?php echo $_SESSION['usuario_nome']; ?>!</h4>
                                    <small class="opacity-75">Bem-vindo de volta</small>
                                </div>
                            </div>
                            <p>Você está logado. Aproveite todos os recursos disponíveis.</p>
                            <div class="mt-3">
                                <a href="dashboard.php" class="btn btn-light me-2">
                                    <i class="bi bi-speedometer2"></i> Meu Dashboard
                                </a>
                                <a href="perfil.php" class="btn btn-outline-light">
                                    <i class="bi bi-person"></i> Meu Perfil
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Botões para usuário não logado -->
                        <p class="mb-4">Faça login ou cadastre-se para acessar todos os recursos.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="login.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                            <a href="cadastro.php" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-person-plus"></i> Cadastre-se
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         class="img-fluid rounded-3 shadow-lg" 
                         alt="Médico atendendo paciente">
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center text-primary mb-5">Nossos Serviços</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card p-4 text-center">
                        <div class="feature-icon">📅</div>
                        <h3 class="h4 mb-3">Agendamento Online</h3>
                        <p class="text-muted">
                            Agende consultas com especialistas de forma rápida e prática.
                        </p>
                        <?php if(isset($_SESSION['usuario_id'])): ?>
                            <a href="agendar.php" class="btn btn-primary mt-3">Agendar Consulta</a>
                        <?php else: ?>
                            <a href="cadastro.php" class="btn btn-outline-primary mt-3">Começar Agora</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card p-4 text-center">
                        <div class="feature-icon">📚</div>
                        <h3 class="h4 mb-3">Informações de Saúde</h3>
                        <p class="text-muted">
                            Acesse artigos e dicas sobre bem-estar e cuidados com a saúde.
                        </p>
                        <a href="blog.php" class="btn btn-outline-primary mt-3">Ler Artigos</a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card feature-card p-4 text-center">
                        <div class="feature-icon">👥</div>
                        <h3 class="h4 mb-3">Conecte-se</h3>
                        <p class="text-muted">
                            Encontre médicos e pacientes na sua região.
                        </p>
                        <a href="sobre.php" class="btn btn-outline-primary mt-3">Saiba Mais</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Conecta Saúde</h5>
                    <p class="small">Sua saúde em primeiro lugar.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="small mb-0">&copy; 2023 Conecta Saúde. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>