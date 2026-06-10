<?php
session_start();

// Pegar ID do artigo
if (!isset($_GET['id'])) {
    header('Location: blog.php');
    exit;
}

$artigo_id = intval($_GET['id']);

$is_sample = false;

// Se existir conexão com DB, tentar buscar; caso contrário, usar artigos fictícios
if (isset($mysqli) && $mysqli) {
    // Buscar artigo no banco
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

} else {
    // Artigos fictícios para demonstração
    $is_sample = true;
    $sample_articles = [
        1 => [
            'id' => 1,
            'titulo' => 'Importância do Check-up Anual',
            'categoria' => 'Prevenção',
            'imagem' => 'assets/hero-doctor.jpg',
            'data_publicacao' => '2026-12-31',
            'autor_nome' => 'Dr. Carlos Henrique',
            'autor_especialidade' => 'Clínico Geral',
            'visualizacoes' => 124,
            'conteudo' => '<p>Realizar um check-up anual é uma das ações mais importantes para a manutenção da saúde ao longo dos anos. O exame de rotina permite identificar sinais precoces de doenças crônicas, avaliar fatores de risco e estabelecer um plano preventivo personalizado.</p>
<h2>Por que fazer o check-up?</h2>
<p>O check-up ajuda a detectar hipertensão, diabetes, alterações de colesterol e problemas renais ou hepáticos ainda em estágio inicial. Com intervenções tempestivas, é possível reduzir complicações e melhorar a qualidade de vida.</p>
<h3>Exames comuns</h3>
<ul>
<li>Hemograma completo</li>
<li>Glicemia de jejum e hemoglobina glicada</li>
<li>Perfil lipídico (colesterol total, HDL, LDL, triglicerídeos)</li>
<li>Função renal (ureia e creatinina)</li>
<li>Exames de imagem quando indicados (ultrassom, mamografia, etc.)</li>
</ul>
<p>Converse com seu médico sobre quais exames são adequados para sua faixa etária e histórico familiar.</p>'
        ],
        2 => [
            'id' => 2,
            'titulo' => 'Alimentação Saudável no Inverno',
            'categoria' => 'Nutrição',
            'imagem' => 'assets/hero-doctor.jpg',
            'data_publicacao' => '2026-12-20',
            'autor_nome' => 'Dra. Ana Ribeiro',
            'autor_especialidade' => 'Nutricionista',
            'visualizacoes' => 89,
            'conteudo' => '<p>Nos meses mais frios, é comum buscarmos refeições mais calóricas e reconfortantes. No entanto, manter uma alimentação equilibrada continua sendo essencial para a imunidade e o bem-estar.</p>
<h2>Dicas práticas</h2>
<p>Priorize alimentos da estação como abóboras, couves e raízes. Inclua fontes de proteína magra e gorduras saudáveis para manter saciedade e suporte imunológico.</p>
<h3>Sugestões de refeições</h3>
<ul>
<li>Sopa de legumes com frango desfiado</li>
<li>Ensopado de legumes e grãos integrais</li>
<li>Salada morna de quinoa com folhas e sementes</li>
</ul>
<p>Evite excessos de frituras e açúcar, que podem prejudicar a resposta inflamatória do organismo.</p>'
        ],
        3 => [
            'id' => 3,
            'titulo' => 'Cuidados com a Saúde Mental',
            'categoria' => 'Bem-estar',
            'imagem' => 'assets/hero-doctor.jpg',
            'data_publicacao' => '2026-12-05',
            'autor_nome' => 'Psic. Mariana Lopes',
            'autor_especialidade' => 'Psicóloga Clínica',
            'visualizacoes' => 203,
            'conteudo' => '<p>Cuidar da saúde mental é tão importante quanto cuidar da saúde física. Pequenas práticas diárias podem reduzir o estresse e melhorar a qualidade de vida.</p>
<h2>Práticas recomendadas</h2>
<ul>
<li>Exercício físico regular</li>
<li>Rotina de sono adequada</li>
<li>Conexão social e suporte</li>
<li>Buscar apoio profissional quando necessário</li>
</ul>
<p>Não hesite em procurar um profissional se sentir sintomas persistentes de ansiedade ou depressão.</p>'
        ]
    ];

    if (!isset($sample_articles[$artigo_id])) {
        header('Location: blog.php');
        exit;
    }

    $artigo = $sample_articles[$artigo_id];
    $titulo = $artigo['titulo'] . " - Conecta Saúde Blog";
    $relacionados = array_values(array_filter($sample_articles, function($a) use ($artigo_id) { return $a['id'] != $artigo_id; }));

}
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
                    <?php echo $artigo['conteudo']; ?>
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
                