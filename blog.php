<?php
// blog.php - CORRIGIDO para estrutura CONECTASAUDE
session_start();

// Incluir configuração
if (file_exists('config.php')) {
    require_once 'config.php';
}

// Incluir conexão se existir
if (file_exists('conexao.php')) {
    require_once 'conexao.php';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Conecta Saúde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Incluir navbar -->
    <?php include_once 'navbar.php'; ?>
    
    <main class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Blog Conecta Saúde</h1>
                <p class="lead">Artigos sobre saúde, bem-estar e dicas médicas</p>
            </div>
        </div>
        
        <div class="row">
            <?php
            // Verificar se há conexão com banco
            $has_db = isset($conn) && $conn;
            
            if ($has_db) {
                // Buscar artigos do banco
                try {
                    $sql = "SELECT * FROM artigos WHERE status = 'publicado' ORDER BY data_publicacao DESC LIMIT 6";
                    $result = $conn->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <?php if(!empty($row['imagem'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($row['imagem']); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($row['titulo']); ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($row['titulo']); ?></h5>
                                        <p class="card-text small">
                                            <?php 
                                            $resumo = strip_tags($row['conteudo']);
                                            echo strlen($resumo) > 100 ? substr($resumo, 0, 100) . '...' : $resumo;
                                            ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($row['data_publicacao'])); ?>
                                        </small>
                                    </div>
                                    
                                    <div class="card-footer">
                                        <a href="artigo.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            Ler artigo
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="col-12"><div class="alert alert-info">Nenhum artigo publicado ainda.</div></div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="col-12"><div class="alert alert-warning">Erro ao carregar artigos.</div></div>';
                }
            } else {
                // Dados de exemplo se não houver banco
                $artigos = [
                    ['id' => 1, 'titulo' => 'Importância do Check-up Anual', 'data' => '31/12/2025', 'resumo' => 'Saiba por que fazer check-up anual é essencial para prevenção.'],
                    ['id' => 2, 'titulo' => 'Alimentação Saudável no Inverno', 'data' => '31/12/2025', 'resumo' => 'Dicas para manter uma alimentação balanceada nos dias frios.'],
                    ['id' => 3, 'titulo' => 'Cuidados com a Saúde Mental', 'data' => '05/12/2025', 'resumo' => 'Como cuidar da saúde mental no dia a dia.'],
                ];
                
                foreach($artigos as $artigo) {
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $artigo['titulo']; ?></h5>
                                <p class="card-text small"><?php echo $artigo['resumo']; ?></p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> <?php echo $artigo['data']; ?>
                                </small>
                            </div>
                            <div class="card-footer">
                                <a href="artigo.php?id=<?php echo $artigo['id']; ?>" 
                                   class="btn btn-primary btn-sm">
                                    Ler artigo
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </main>
    
    <!-- Incluir footer -->
    <?php include_once 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>