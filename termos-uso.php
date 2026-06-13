<?php
session_start();
$titulo = 'Termos de Uso - Conecta Saúde';
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
        .terms-hero {
            background: linear-gradient(135deg, rgba(0,119,182,0.95), rgba(0,180,216,0.95));
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }
        .terms-card { border-radius: 10px; }
    </style>
</head>
<body>
    <?php include_once 'navbar.php'; ?>

    <section class="terms-hero text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Termos de Uso</h1>
            <p class="lead mb-0">ConectaSaúde — Última atualização: 11 de junho de 2026</p>
        </div>
    </section>

    <main class="container mb-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm terms-card mb-4">
                    <div class="card-body">
                        <h2 id="aceitacao" class="h5 text-primary">1. Aceitação dos Termos</h2>
                        <p>
                            Ao acessar ou utilizar a API, plataforma, website ou quaisquer serviços fornecidos pela ConectaSaúde,
                            o usuário declara ter lido, compreendido e concordado integralmente com estes Termos de Uso.
                            Caso não concorde com qualquer disposição destes termos, o usuário não deverá utilizar os serviços.
                        </p>

                        <h2 class="h5 text-primary mt-4">2. Sobre a ConectaSaúde</h2>
                        <p>
                            A ConectaSaúde é uma plataforma tecnológica que oferece recursos de integração, gerenciamento e acesso
                            a informações relacionadas à área da saúde por meio de APIs e sistemas próprios. A ConectaSaúde atua como
                            fornecedora de tecnologia e não substitui profissionais da saúde, hospitais, clínicas ou serviços médicos.
                        </p>

                        <h2 id="cadastro" class="h5 text-primary mt-4">3. Cadastro e Conta</h2>
                        <p>Para utilizar determinados serviços, poderá ser necessário criar uma conta. O usuário compromete-se a:</p>
                        <ul>
                            <li>Fornecer informações verdadeiras e atualizadas;</li>
                            <li>Manter seus dados atualizados;</li>
                            <li>Preservar a confidencialidade de suas credenciais;</li>
                            <li>Não compartilhar sua conta com terceiros;</li>
                            <li>Comunicar imediatamente qualquer uso não autorizado da conta.</li>
                        </ul>
                        <p>O usuário é responsável por todas as atividades realizadas por meio de sua conta.</p>

                        <h2 class="h5 text-primary mt-4">4. Uso Permitido</h2>
                        <p>O usuário poderá utilizar os serviços exclusivamente para finalidades legais e autorizadas.</p>
                        <p>É proibido:</p>
                        <ul>
                            <li>Utilizar os serviços para atividades ilegais;</li>
                            <li>Tentar obter acesso não autorizado a sistemas ou dados;</li>
                            <li>Realizar engenharia reversa da plataforma;</li>
                            <li>Interferir no funcionamento dos serviços;</li>
                            <li>Distribuir vírus, malware ou códigos maliciosos;</li>
                            <li>Utilizar a plataforma para envio de spam;</li>
                            <li>Coletar informações de terceiros sem autorização.</li>
                        </ul>

                        <h2 id="uso-api" class="h5 text-primary mt-4">5. Uso da API</h2>
                        <p>Ao utilizar a API da ConectaSaúde, o usuário concorda em:</p>
                        <ul>
                            <li>Utilizar as credenciais de acesso de forma segura;</li>
                            <li>Respeitar limites de requisições estabelecidos;</li>
                            <li>Não realizar tentativas de exploração ou sobrecarga dos sistemas;</li>
                            <li>Não compartilhar chaves de API com terceiros não autorizados;</li>
                            <li>Utilizar os dados obtidos em conformidade com a legislação aplicável.</li>
                        </ul>
                        <p>A ConectaSaúde poderá suspender ou revogar credenciais que estejam sendo utilizadas de forma inadequada.</p>

                        <h2 class="h5 text-primary mt-4">6. Dados e Privacidade</h2>
                        <p>O tratamento de dados pessoais ocorre de acordo com a Política de Privacidade da ConectaSaúde.
                        O usuário é responsável por garantir que possui autorização legal para processar e enviar dados pessoais
                        por meio dos serviços quando atuar como controlador ou operador desses dados.</p>

                        <h2 class="h5 text-primary mt-4">7. Disponibilidade dos Serviços</h2>
                        <p>A ConectaSaúde busca manter seus serviços disponíveis continuamente. Entretanto, não garante disponibilidade
                        ininterrupta e poderá realizar atualizações, correções e manutenções que resultem em interrupções temporárias.</p>

                        <h2 class="h5 text-primary mt-4">8. Propriedade Intelectual</h2>
                        <p>Todos os direitos relacionados à plataforma, software, APIs, documentação, marcas, logotipos e conteúdos da
                        ConectaSaúde pertencem à ConectaSaúde ou aos seus respectivos titulares. Nenhuma disposição destes Termos concede
                        ao usuário qualquer direito de propriedade intelectual além da autorização limitada de uso dos serviços.</p>

                        <h2 class="h5 text-primary mt-4">9. Limitação de Responsabilidade</h2>
                        <p>Na máxima extensão permitida pela legislação aplicável, a ConectaSaúde não será responsável por danos indiretos,
                        lucros cessantes, perda de receitas, decisões tomadas com base em informações fornecidas pelos usuários, falhas de terceiros
                        ou eventos fora de seu controle razoável. O uso dos serviços ocorre por conta e risco do usuário.</p>

                        <h2 class="h5 text-primary mt-4">10. Suspensão e Encerramento</h2>
                        <p>A ConectaSaúde poderá suspender ou encerrar contas, acessos ou credenciais em caso de violação destes Termos,
                        atividades suspeitas, uso abusivo da plataforma ou determinação legal. O usuário também poderá encerrar sua conta a qualquer momento.</p>

                        <h2 class="h5 text-primary mt-4">11. Modificações dos Serviços</h2>
                        <p>A ConectaSaúde poderá adicionar, modificar, suspender ou descontinuar funcionalidades, APIs ou serviços a qualquer momento.</p>

                        <h2 class="h5 text-primary mt-4">12. Isenção Médica</h2>
                        <p>A ConectaSaúde não fornece diagnósticos médicos, prescrições ou orientações clínicas. Decisões médicas devem ser tomadas por profissionais habilitados.</p>

                        <h2 class="h5 text-primary mt-4">13. Conformidade com a LGPD</h2>
                        <p>Os usuários comprometem-se a cumprir todas as obrigações previstas na Lei Geral de Proteção de Dados (Lei nº 13.709/2018) e normas aplicáveis.</p>

                        <h2 class="h5 text-primary mt-4">14. Alterações dos Termos</h2>
                        <p>Estes Termos de Uso poderão ser atualizados periodicamente. A continuidade da utilização dos serviços após a publicação das alterações constituirá aceitação da nova versão.</p>

                        <h2 class="h5 text-primary mt-4">15. Legislação Aplicável e Foro</h2>
                        <p>Estes Termos serão regidos pelas leis da República Federativa do Brasil. Fica eleito o foro da comarca do responsável legal pela ConectaSaúde para dirimir controvérsias.</p>

                        <h2 class="h5 text-primary mt-4">16. Contato</h2>
                        <p><strong>ConectaSaúde</strong><br>
                        E-mail: contato@conectasaude.com<br>
                        Suporte: suporte@conectasaude.com</p>

                        <p class="mt-4"><em>Ao utilizar os serviços da ConectaSaúde, o usuário declara ter lido, compreendido e aceitado integralmente estes Termos de Uso.</em></p>
                    </div>
                </div>
            </div>

            <aside class="col-lg-4">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Índice</h5>
                        <ul class="list-unstyled">
                            <li><a href="#aceitacao">1. Aceitação dos Termos</a></li>
                            <li><a href="#cadastro">3. Cadastro e Conta</a></li>
                            <li><a href="#uso-api">5. Uso da API</a></li>
                            <li><a href="politica-privacidade.php">Política de Privacidade</a></li>
                            <li><a href="contato.php">Contato</a></li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <?php include_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
