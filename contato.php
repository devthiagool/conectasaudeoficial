<?php
session_start();
$titulo = 'Contato - Conecta Saúde';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .contact-hero { background: linear-gradient(135deg, rgba(0,119,182,0.95), rgba(0,180,216,0.95)); color:white; padding:50px 0; margin-bottom:20px; }
        .contact-card { border-radius:12px; }
    </style>
</head>
<body>
    <?php include_once 'navbar.php'; ?>

    <section class="contact-hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Contato</h1>
            <p class="lead mb-0">Fale conosco — ConectaSaúde</p>
        </div>
    </section>

    <main class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm contact-card mb-4">
                    <div class="card-body">
                        <h4 class="card-title text-primary">Como entrar em contato</h4>
                        <p class="mb-4">Se você tem dúvidas sobre nossos serviços, solicitações de privacidade, suporte técnico ou parcerias, utilize os canais abaixo:</p>

                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="bi bi-envelope-fill me-2 text-primary"></i><strong>E-mail geral:</strong> contato@conectasaude.com</li>
                            <li class="mb-3"><i class="bi bi-life-preserver me-2 text-primary"></i><strong>Suporte:</strong> suporte@conectasaude.com</li>
                            <li class="mb-3"><i class="bi bi-telephone-fill me-2 text-primary"></i><strong>Telefone:</strong> (00) 0 0000-0000</li>
                            <li class="mb-3"><i class="bi bi-geo-alt-fill me-2 text-primary"></i><strong>Endereço:</strong> Avenida Exemplo, 123 — Cidade, Estado</li>
                        </ul>

                        <p class="small text-muted mt-3">Para solicitações de acesso, correção ou exclusão de dados pessoais, envie seu pedido para <strong>suporte@conectasaude.com</strong> com o assunto "Solicitação LGPD" e informaremos os próximos passos.</p>

                        <div class="mt-4">
                            <a href="index.php" class="btn btn-secondary">Voltar à Home</a>
                            <a href="politica-privacidade.php" class="btn btn-outline-primary ms-2">Política de Privacidade</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
