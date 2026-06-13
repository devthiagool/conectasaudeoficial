<?php
session_start();
$titulo = 'Política de Privacidade - Conecta Saúde';
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
        .policy-hero {
            background: linear-gradient(135deg, rgba(0,119,182,0.95), rgba(0,180,216,0.95));
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }
        .policy-card { border-radius: 10px; }
        .toc a { text-decoration: none; }
    </style>
</head>
<body>
    <?php include_once 'navbar.php'; ?>

    <section class="policy-hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Política de Privacidade</h1>
            <p class="lead mb-0">ConectaSaúde — Última atualização: 11 de junho de 2026</p>
        </div>
    </section>

    <main class="container mb-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm policy-card mb-4">
                    <div class="card-body">
                        <h2 id="introducao" class="h5 text-primary">1. Introdução</h2>
                        <p>
                            A ConectaSaúde valoriza a privacidade e a proteção dos dados pessoais de seus usuários.
                            Esta Política de Privacidade descreve como coletamos, utilizamos, armazenamos e protegemos
                            as informações fornecidas por meio de nossa API, plataforma e serviços relacionados.
                        </p>

                        <h2 id="dados-coletados" class="h5 text-primary mt-4">2. Dados coletados</h2>
                        <p>Podemos coletar e processar os seguintes dados, quando aplicável:</p>
                        <ul>
                            <li><strong>Dados de identificação:</strong> nome completo, CPF, data de nascimento, endereço, telefone e e-mail.</li>
                            <li><strong>Dados de acesso:</strong> endereço IP, data/hora de acesso, informações do dispositivo, tokens de autenticação e logs de utilização da API.</li>
                            <li><strong>Dados de saúde (sensíveis):</strong> prontuários, histórico clínico, exames, prescrições e demais informações médicas enviadas pelos usuários autorizados.</li>
                            <li><strong>Dados de autenticação:</strong> credenciais armazenadas de forma segura e criptografada.</li>
                        </ul>

                        <h2 id="finalidade" class="h5 text-primary mt-4">3. Finalidade do tratamento</h2>
                        <ul>
                            <li>Fornecer, operar e manter os serviços da plataforma e da API.</li>
                            <li>Autenticar e gerenciar contas e permissões.</li>
                            <li>Garantir segurança, detectar e prevenir fraudes e acessos não autorizados.</li>
                            <li>Atender solicitações e prestar suporte técnico.</li>
                            <li>Cumprir obrigações legais e regulatórias.</li>
                            <li>Gerar métricas e relatórios para melhoria do serviço, sempre que anonimizados quando possível.</li>
                        </ul>

                        <h2 class="h5 text-primary mt-4">4. Base legal</h2>
                        <p>O tratamento de dados é realizado conforme a Lei Geral de Proteção de Dados (Lei nº 13.709/2018 - LGPD), com base em:</p>
                        <ul>
                            <li>Consentimento do titular;</li>
                            <li>Execução de contrato;</li>
                            <li>Cumprimento de obrigação legal ou regulatória;</li>
                            <li>Proteção da vida e da saúde;</li>
                            <li>Legítimo interesse, quando aplicável e justificado.</li>
                        </ul>

                        <h2 class="h5 text-primary mt-4">5. Compartilhamento de dados</h2>
                        <p>
                            A ConectaSaúde não vende dados pessoais. Podemos compartilhar informações apenas quando necessário para:
                        </p>
                        <ul>
                            <li>Operação da infraestrutura (provedores de hospedagem, serviços de banco de dados e backups);</li>
                            <li>Serviços de monitoramento, segurança e análise, mediante contratos que garantam confidencialidade;</li>
                            <li>Cumprimento de ordens judiciais ou requisições de autoridades competentes;</li>
                            <li>Parceiros autorizados, quando indispensável para a execução dos serviços e mediante contratos.</li>
                        </ul>

                        <h2 class="h5 text-primary mt-4">6. Armazenamento e segurança</h2>
                        <p>
                            Empregamos medidas técnicas e administrativas para proteger os dados contra acesso não autorizado, alteração, divulgação ou destruição, incluindo:
                        </p>
                        <ul>
                            <li>Criptografia de senhas e credenciais;</li>
                            <li>Comunicação protegida por HTTPS/TLS;</li>
                            <li>Controle de acesso e privilégios (princípio do menor privilégio);</li>
                            <li>Monitoramento e registro de atividades (logs de auditoria);</li>
                            <li>Backups regulares e políticas de retenção;</li>
                            <li>Adoção de práticas de segurança pela equipe e contratos com fornecedores que exigem proteção de dados.</li>
                        </ul>
                        <p class="small text-muted">Observação: nenhum sistema é totalmente imune a riscos; adotamos medidas para mitigar e responder a incidentes.</p>

                        <h2 id="dados-sensiveis" class="h5 text-primary mt-4">7. Dados sensíveis e prontuários</h2>
                        <p>
                            Dados de saúde são considerados dados pessoais sensíveis e recebem tratamento especial. O acesso a prontuários e informações clínicas é restrito apenas a usuários autorizados e profissionais com consentimento explícito ou respaldo legal.
                        </p>

                        <h2 class="h5 text-primary mt-4">8. Retenção de dados</h2>
                        <p>
                            Reteremos os dados pelo tempo necessário para a prestação dos serviços, cumprimento de obrigações legais e segurança da plataforma. Quando não houver necessidade, arquivos serão excluídos ou anonimizados.
                        </p>

                        <h2 id="direitos" class="h5 text-primary mt-4">9. Direitos dos titulares (LGPD)</h2>
                        <p>O titular dos dados pode solicitar:</p>
                        <ul>
                            <li>Confirmação da existência de tratamento;</li>
                            <li>Acesso aos dados pessoais;</li>
                            <li>Correção de dados incompletos, inexatos ou desatualizados;</li>
                            <li>Anonimização, bloqueio ou eliminação de dados desnecessários;</li>
                            <li>Portabilidade dos dados;</li>
                            <li>Revogação do consentimento, quando aplicável;</li>
                            <li>Informações sobre compartilhamento e finalidades do tratamento.</li>
                        </ul>
                        <p>Solicitações devem ser enviadas ao e-mail de contato (ver seção 12). Responderemos dentro dos prazos legais.</p>

                        <h2 class="h5 text-primary mt-4">10. Cookies e tecnologias semelhantes</h2>
                        <p>
                            Podemos utilizar cookies para manter sessões autenticadas, melhorar o desempenho e coletar métricas de uso.
                            O usuário pode configurar o navegador para recusar cookies, ciente de que funcionalidades podem ficar limitadas.
                        </p>

                        <h2 class="h5 text-primary mt-4">11. Menores de idade</h2>
                        <p>Os serviços não são destinados a menores de idade sem supervisão ou autorização dos responsáveis legais, quando exigido pela legislação.</p>

                        <h2 class="h5 text-primary mt-4">12. Alterações nesta política</h2>
                        <p>
                            Esta Política poderá ser atualizada periodicamente. A versão mais recente estará disponível nos canais oficiais da ConectaSaúde.
                        </p>

                        <h2 class="h5 text-primary mt-4">13. Contato</h2>
                        <p>Para dúvidas, solicitações ou questões relacionadas à privacidade e proteção de dados:</p>
                        <ul>
                            <li><strong>E-mail:</strong> contato@conectasaude.com</li>
                            <li><strong>Suporte:</strong> suporte@conectasaude.com</li>
                        </ul>

                        <p class="mt-4"><em>Ao utilizar os serviços da ConectaSaúde, o usuário declara estar ciente e concordar com os termos desta Política de Privacidade.</em></p>
                    </div>
                </div>
            </div>

            <aside class="col-lg-4">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Índice</h5>
                        <nav class="toc">
                            <ul class="list-unstyled">
                                <li><a href="#introducao">1. Introdução</a></li>
                                <li><a href="#dados-coletados">2. Dados coletados</a></li>
                                <li><a href="#finalidade">3. Finalidade</a></li>
                                <li><a href="#dados-sensiveis">7. Dados sensíveis</a></li>
                                <li><a href="#direitos">9. Direitos dos titulares</a></li>
                                <li><a href="contato.php">Contato</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="mb-2">Precisa de ajuda?</h6>
                        <p class="small text-muted mb-3">Envie sua solicitação de privacidade</p>
                        <a href="contato.php" class="btn btn-primary btn-sm">Entrar em Contato</a>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <?php include_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
