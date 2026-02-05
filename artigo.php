<?php
session_start();
include 'includes/config.php';

if (!isset($_GET['id'])) {
    header('Location: blog.php');
    exit;
}

$artigo_id = intval($_GET['id']);

// Buscar artigo
$sql = "SELECT a.*, u.nome as autor_nome, u.especialidade as autor_especialidade 
        FROM artigos a 
        JOIN usuarios u ON a.autor_id = u.id 
        WHERE a.id = ? AND a.status = 'publicado'";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $artigo_id);
$stmt->execute();
$result = $stmt->get_result();
$artigo = $result->fetch_assoc();

if (!$artigo) {
    header('Location: blog.php');
    exit;
}

// Incrementar visualizações
$sql_update = "UPDATE artigos SET visualizacoes = visualizacoes + 1 WHERE id = ?";
$stmt_update = $mysqli->prepare($sql_update);
$stmt_update->bind_param("i", $artigo_id);
$stmt_update->execute();

$titulo = $artigo['titulo'] . " - Conecta Saúde Blog";

// Buscar artigos relacionados
$sql_relacionados = "SELECT id, titulo, imagem, data_publicacao 
                     FROM artigos 
                     WHERE categoria = ? AND id != ? AND status = 'publicado' 
                     ORDER BY data_publicacao DESC LIMIT 3";
$stmt = $mysqli->prepare($sql_relacionados);
$stmt->bind_param("si", $artigo['categoria'], $artigo_id);
$stmt->execute();
$relacionados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .article-header {
            background: linear-gradient(rgba(0, 119, 182, 0.8), rgba(0, 180, 216, 0.8)), 
                        url('<?php echo $artigo['imagem'] ?: "https://images.unsplash.com/photo-1505751172876-fa1923c5c528?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80"; ?>') center/cover;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
        }
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 20px 0;
        }
        .article-content h2, .article-content h3 {
            color: #0077b6;
            margin-top: 30px;
        }
        .author-card {
            border-left: 4px solid #0077b6;
            padding-left: 20px;
        }
        .share-buttons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border-radius: 50%;
            margin: 5px;
            color: white;
            text-decoration: none;
        }
        .facebook { background: #3b5998; }
        .twitter { background: #1da1f2; }
        .linkedin { background: #0077b5; }
        .whatsapp { background: #25d366; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Cabeçalho do Artigo -->
    <section class="article-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <?php if($artigo['categoria']): ?>
                        <span class="badge bg-light text-primary mb-3 p-2"><?php echo $artigo['categoria']; ?></span>
                    <?php endif; ?>
                    
                    <h1 class="display-5 fw-bold mb-4"><?php echo htmlspecialchars($artigo['titulo']); ?></h1>
                    
                    <div class="d-flex justify-content-center align-items-center flex-wrap gap-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle fs-4 me-2"></i>
                            <div>
                                <strong><?php echo $artigo['autor_nome']; ?></strong>
                                <?php if($artigo['autor_especialidade']): ?>
                                    <br><small><?php echo $artigo['autor_especialidade']; ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar me-2"></i>
                            <span><?php echo date('d/m/Y', strtotime($artigo['data_publicacao'])); ?></span>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock me-2"></i>
                            <?php
                            $palavras = str_word_count(strip_tags($artigo['conteudo']));
                            $tempo_leitura = ceil($palavras / 200);
                            echo $tempo_leitura > 1 ? "$tempo_leitura min de leitura" : "1 min de leitura";
                            ?>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <i class="bi bi-eye me-2"></i>
                            <span><?php echo $artigo['visualizacoes']; ?> visualizações</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Conteúdo do Artigo -->
    <div class="container">
        <div class="row justify-content-center">
            <!-- Conteúdo Principal -->
            <div class="col-lg-8">
                <article class="article-content mb-5">
                    <?php echo nl2br(htmlspecialchars_decode($artigo['conteudo'])); ?>
                </article>
                
                <!-- Compartilhar -->
                <div class="card mb-5">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Compartilhe este artigo</h5>
                        <div class="share-buttons mt-3">
                            <a href="#" class="facebook" title="Compartilhar no Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="twitter" title="Compartilhar no Twitter">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="#" class="linkedin" title="Compartilhar no LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="#" class="whatsapp" title="Compartilhar no WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
                