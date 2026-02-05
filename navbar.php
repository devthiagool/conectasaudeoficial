<?php

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <span class="ms-2 fw-bold">Conecta Saúde</span>
        </a>

        <!-- Botão Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                       href="index.php">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sobre.php' ? 'active' : ''; ?>" 
                       href="sobre.php">
                        <i class="bi bi-info-circle"></i> Sobre
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'blog.php' ? 'active' : ''; ?>" 
                       href="blog.php">
                        <i class="bi bi-newspaper"></i> Blog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'servicos.php' ? 'active' : ''; ?>" 
                       href="servicos.php">
                        <i class="bi bi-heart"></i> Serviços
                    </a>
                </li>
            </ul>

            <!-- Menu do Usuário -->
            <ul class="navbar-nav">
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <!-- Usuário Logado -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" 
                           id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <?php
                            $foto = 'assets/default-avatar.png';
                            if (isset($_SESSION['usuario_foto']) && !empty($_SESSION['usuario_foto'])) {
                                $foto_path = 'uploads/' . $_SESSION['usuario_foto'];
                                if (file_exists($foto_path)) {
                                    $foto = $foto_path;
                                }
                            }
                            ?>
                            <img src="<?php echo $foto; ?>?t=<?php echo time(); ?>" 
                                 class="rounded-circle" 
                                 alt="<?php echo $_SESSION['usuario_nome']; ?>"
                                 style="width: 40px; height: 40px; object-fit: cover; border: 2px solid white; flex-shrink: 0;">
                            <span class="d-none d-md-inline">
                                <?php 
                                $nome = $_SESSION['usuario_nome'];
                                echo strlen($nome) > 15 ? substr($nome, 0, 15) . '...' : $nome;
                                ?>
                            </span>
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <div class="dropdown-header">
                                    <strong><?php echo $_SESSION['usuario_nome']; ?></strong>
                                    <p class="small text-muted mb-0"><?php echo $_SESSION['usuario_email']; ?></p>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="dashboard.php">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="perfil.php">
                                    <i class="bi bi-person"></i> Meu Perfil
                                </a>
                            </li>
                            <?php if($_SESSION['usuario_tipo'] == 'paciente'): ?>
                                <li>
                                    <a class="dropdown-item" href="agendar.php">
                                        <i class="bi bi-calendar-plus"></i> Agendar Consulta
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Sair
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                <?php else: ?>
                    <!-- Usuário Não Logado -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" 
                           href="login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light ms-2" href="cadastro.php">
                            <i class="bi bi-person-plus"></i> Cadastre-se
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
.avatar-container {
    position: relative;
    display: inline-block;
}

.avatar-img {
    transition: transform 0.3s;
}

.avatar-img:hover {
    transform: scale(1.1);
}

.dropdown-menu {
    min-width: 250px;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-radius: 10px;
    margin-top: 10px;
}

.dropdown-header {
    padding: 10px 15px;
}

.dropdown-item {
    padding: 10px 15px;
    border-radius: 5px;
    margin: 2px 5px;
    transition: all 0.3s;
}

.dropdown-item:hover {
    background-color: #e9ecef;
}

.nav-link.active {
    font-weight: 600;
    background-color: rgba(255,255,255,0.1);
    border-radius: 5px;
}

@media (max-width: 768px) {
    .dropdown-menu {
        position: static !important;
        transform: none !important;
        margin: 10px 0;
    }
}
</style>