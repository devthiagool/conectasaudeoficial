<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Arquivo para armazenar mensagens
$arquivo_mensagens = 'dados/mensagens_' . $_SESSION['usuario_id'] . '.json';

// Criar diretório se não existir
if (!is_dir('dados')) {
    mkdir('dados', 0755, true);
}

// Inicializar mensagens
if (!file_exists($arquivo_mensagens)) {
    $mensagens_padrao = [
        [
            'id' => 1,
            'remetente' => 'Dr. Carlos Silva',
            'assunto' => 'Confirmação de consulta',
            'mensagem' => 'Sua consulta foi confirmada para 15/01/2024 às 14:00.',
            'data' => '2024-01-10 09:30',
            'lida' => true
        ],
        [
            'id' => 2,
            'remetente' => 'Dra. Ana Souza',
            'assunto' => 'Resultados de exames',
            'mensagem' => 'Seus exames já estão disponíveis no portal.',
            'data' => '2024-01-09 14:15',
            'lida' => false
        ],
        [
            'id' => 3,
            'remetente' => 'Sistema Conecta Saúde',
            'assunto' => 'Bem-vindo ao sistema',
            'mensagem' => 'Obrigado por se cadastrar! Esperamos que tenha uma ótima experiência.',
            'data' => '2024-01-08 10:00',
            'lida' => true
        ]
    ];
    file_put_contents($arquivo_mensagens, json_encode($mensagens_padrao, JSON_PRETTY_PRINT));
}

// Carregar mensagens
$mensagens = json_decode(file_get_contents($arquivo_mensagens), true);

// Processar ações (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'adicionar_mensagem') {
        $nova_mensagem = [
            'id' => !empty($mensagens) ? max(array_column($mensagens, 'id')) + 1 : 1,
            'remetente' => htmlspecialchars($_POST['remetente']),
            'assunto' => htmlspecialchars($_POST['assunto']),
            'mensagem' => htmlspecialchars($_POST['mensagem']),
            'data' => date('Y-m-d H:i'),
            'lida' => false
        ];
        $mensagens[] = $nova_mensagem;
        file_put_contents($arquivo_mensagens, json_encode($mensagens, JSON_PRETTY_PRINT));
        echo json_encode(['sucesso' => true, 'mensagem' => 'Mensagem adicionada com sucesso']);
        exit;
    } 
    elseif ($acao === 'marcar_como_lida') {
        $id = intval($_POST['id']);
        foreach ($mensagens as &$msg) {
            if ($msg['id'] === $id) {
                $msg['lida'] = true;
                break;
            }
        }
        file_put_contents($arquivo_mensagens, json_encode($mensagens, JSON_PRETTY_PRINT));
        echo json_encode(['sucesso' => true]);
        exit;
    } 
    elseif ($acao === 'marcar_todas_como_lidas') {
        foreach ($mensagens as &$msg) {
            $msg['lida'] = true;
        }
        file_put_contents($arquivo_mensagens, json_encode($mensagens, JSON_PRETTY_PRINT));
        echo json_encode(['sucesso' => true]);
        exit;
    } 
    elseif ($acao === 'limpar_todas') {
        $mensagens = [];
        file_put_contents($arquivo_mensagens, json_encode($mensagens, JSON_PRETTY_PRINT));
        echo json_encode(['sucesso' => true]);
        exit;
    }
    elseif ($acao === 'deletar_mensagem') {
        $id = intval($_POST['id']);
        $mensagens = array_filter($mensagens, function($msg) use ($id) {
            return $msg['id'] !== $id;
        });
        $mensagens = array_values($mensagens);
        file_put_contents($arquivo_mensagens, json_encode($mensagens, JSON_PRETTY_PRINT));
        echo json_encode(['sucesso' => true]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - Conecta Saúde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0077b6;
            --secondary: #00b4d8;
        }
        .mensagem-nao-lida {
            background-color: #f0f9ff;
            border-left: 4px solid var(--primary);
        }
        .mensagem-lida {
            background-color: #f8f9fa;
        }
        .mensagem-item {
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }
        .mensagem-item:hover {
            transform: translateX(5px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .mensagem-item .delete-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .mensagem-item:hover .delete-btn {
            opacity: 1;
        }
        .badge-nao-lido {
            background-color: var(--primary);
            color: white;
            font-size: 0.6rem;
            padding: 3px 6px;
        }
        .btn-nova-mensagem {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 119, 182, 0.3);
            z-index: 1000;
            transition: all 0.3s;
        }
        .btn-nova-mensagem:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 119, 182, 0.4);
        }
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
        }
    </style>
</head>
<body>
    <?php include_once 'navbar.php'; ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="text-primary">
                    <i class="bi bi-chat"></i> Mensagens
                    <span class="badge bg-primary"><?php echo count($mensagens); ?></span>
                </h1>
            </div>
            <div class="btn-group" role="group">
                <button class="btn btn-outline-primary" onclick="marcarTodasComoLidas()" title="Marcar todas as mensagens como lidas">
                    <i class="bi bi-check-all"></i> Marcar todas como lidas
                </button>
                <button class="btn btn-outline-danger" onclick="limparTodasMensagens()" title="Deletar todas as mensagens">
                    <i class="bi bi-trash"></i> Limpar todas
                </button>
            </div>
        </div>
        
        <div class="row">
            <!-- Lista de Mensagens -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Buscar mensagens..." 
                                   id="buscarMensagens" onkeyup="filtrarMensagens()">
                        </div>
                    </div>
                    
                    <div class="list-group list-group-flush" id="listaMensagens">
                        <?php foreach($mensagens as $msg): ?>
                            <div class="list-group-item list-group-item-action mensagem-item 
                                <?php echo $msg['lida'] ? 'mensagem-lida' : 'mensagem-nao-lida'; ?>"
                               data-id="<?php echo $msg['id']; ?>"
                               data-assunto="<?php echo htmlspecialchars($msg['assunto']); ?>"
                               data-remetente="<?php echo htmlspecialchars($msg['remetente']); ?>"
                               onclick="abrirMensagem(<?php echo $msg['id']; ?>)">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-start flex-grow-1">
                                        <div class="me-3">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px; color: white;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <?php echo $msg['remetente']; ?>
                                                <?php if(!$msg['lida']): ?>
                                                    <span class="badge-nao-lido badge ms-2">Nova</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1 text-dark"><?php echo $msg['assunto']; ?></p>
                                            <small class="text-muted">
                                                <?php echo substr($msg['mensagem'], 0, 60); ?>...
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <small class="text-muted d-block">
                                            <?php echo date('d/m/Y', strtotime($msg['data'])); ?>
                                        </small>
                                        <small class="text-muted">
                                            <?php echo date('H:i', strtotime($msg['data'])); ?>
                                        </small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger delete-btn" onclick="event.stopPropagation(); deletarMensagem(<?php echo $msg['id']; ?>)" title="Deletar mensagem">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Modal da Mensagem -->
                            <div class="modal fade" id="modalMensagem<?php echo $msg['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><?php echo $msg['assunto']; ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px; color: white; margin-right: 15px;">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo $msg['remetente']; ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y H:i', strtotime($msg['data'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div class="mensagem-conteudo p-3 bg-light rounded">
                                                <?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletarMensagem(<?php echo $msg['id']; ?>); bootstrap.Modal.getInstance(document.getElementById('modalMensagem<?php echo $msg['id']; ?>')).hide();">
                                                <i class="bi bi-trash"></i> Deletar
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if(count($mensagens) == 0): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-envelope display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">Nenhuma mensagem</h4>
                            <p class="text-muted">Suas mensagens aparecerão aqui.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Contatos Frequentes -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-people"></i> Contatos Frequentes</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center" onclick="event.preventDefault(); preencherDestinatario('Dr. Carlos Silva')">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                 style="width: 35px; height: 35px; color: white; margin-right: 10px;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <small class="fw-bold">Dr. Carlos Silva</small>
                                <small class="d-block text-muted">Cardiologista</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center" onclick="event.preventDefault(); preencherDestinatario('Dra. Ana Souza')">
                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" 
                                 style="width: 35px; height: 35px; color: white; margin-right: 10px;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <small class="fw-bold">Dra. Ana Souza</small>
                                <small class="d-block text-muted">Pediatra</small>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informações</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">
                            <strong>Total de mensagens:</strong> <span id="total-msgs"><?php echo count($mensagens); ?></span>
                        </p>
                        <p class="small text-muted mb-0">
                            <strong>Não lidas:</strong> <span id="total-nao-lidas"><?php echo count(array_filter($mensagens, function($m) { return !$m['lida']; })); ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão Nova Mensagem -->
    <button class="btn btn-primary btn-nova-mensagem" data-bs-toggle="modal" data-bs-target="#modalNovaMensagem" title="Adicionar nova mensagem">
        <i class="bi bi-plus-lg"></i>
    </button>

    <!-- Modal Nova Mensagem -->
    <div class="modal fade" id="modalNovaMensagem" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-envelope-plus"></i> Nova Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formNovaMensagem" onsubmit="adicionarMensagem(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="remetente" class="form-label">De (Nome):</label>
                            <input type="text" class="form-control" id="remetente" placeholder="Digite seu nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="assunto" class="form-label">Assunto:</label>
                            <input type="text" class="form-control" id="assunto" placeholder="Digite o assunto" required>
                        </div>
                        <div class="mb-3">
                            <label for="conteudo" class="form-label">Mensagem:</label>
                            <textarea class="form-control" id="conteudo" rows="5" placeholder="Digite sua mensagem" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Enviar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Adicionar nova mensagem
        function adicionarMensagem(event) {
            event.preventDefault();
            
            const formData = new FormData();
            formData.append('acao', 'adicionar_mensagem');
            formData.append('remetente', document.getElementById('remetente').value);
            formData.append('assunto', document.getElementById('assunto').value);
            formData.append('mensagem', document.getElementById('conteudo').value);
            
            fetch('mensagens.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    mostrarNotificacao('✓ Mensagem adicionada com sucesso!', 'success');
                    document.getElementById('formNovaMensagem').reset();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                mostrarNotificacao('Erro ao adicionar mensagem', 'danger');
                console.error('Erro:', error);
            });
            
            bootstrap.Modal.getInstance(document.getElementById('modalNovaMensagem')).hide();
        }

        // Marcar todas como lidas
        function marcarTodasComoLidas() {
            if (confirm('Deseja marcar todas as mensagens como lidas?')) {
                const formData = new FormData();
                formData.append('acao', 'marcar_todas_como_lidas');
                
                fetch('mensagens.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        mostrarNotificacao('✓ Todas as mensagens marcadas como lidas!', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                })
                .catch(error => {
                    mostrarNotificacao('Erro ao marcar mensagens', 'danger');
                    console.error('Erro:', error);
                });
            }
        }

        // Limpar todas as mensagens
        function limparTodasMensagens() {
            if (confirm('⚠️ Tem certeza que deseja deletar TODAS as mensagens? Esta ação não pode ser desfeita!')) {
                if (confirm('Esta é a última confirmação. Deletar todas as mensagens?')) {
                    const formData = new FormData();
                    formData.append('acao', 'limpar_todas');
                    
                    fetch('mensagens.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.sucesso) {
                            mostrarNotificacao('✓ Todas as mensagens foram deletadas!', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    })
                    .catch(error => {
                        mostrarNotificacao('Erro ao limpar mensagens', 'danger');
                        console.error('Erro:', error);
                    });
                }
            }
        }

        // Deletar mensagem individual
        function deletarMensagem(id) {
            if (confirm('Deseja deletar esta mensagem?')) {
                const formData = new FormData();
                formData.append('acao', 'deletar_mensagem');
                formData.append('id', id);
                
                fetch('mensagens.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        mostrarNotificacao('✓ Mensagem deletada!', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                })
                .catch(error => {
                    mostrarNotificacao('Erro ao deletar mensagem', 'danger');
                    console.error('Erro:', error);
                });
            }
        }

        // Abrir mensagem e marcar como lida
        function abrirMensagem(id) {
            // Marcar como lida
            const formData = new FormData();
            formData.append('acao', 'marcar_como_lida');
            formData.append('id', id);
            
            fetch('mensagens.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    const mensagemElement = document.querySelector(`[data-id="${id}"]`);
                    mensagemElement.classList.remove('mensagem-nao-lida');
                    mensagemElement.classList.add('mensagem-lida');
                    
                    // Atualizar contador de não lidas
                    const naoLidas = document.querySelectorAll('.mensagem-nao-lida').length;
                    document.getElementById('total-nao-lidas').textContent = naoLidas;
                }
            });
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById(`modalMensagem${id}`));
            modal.show();
        }

        // Filtrar mensagens
        function filtrarMensagens() {
            const busca = document.getElementById('buscarMensagens').value.toLowerCase();
            const mensagens = document.querySelectorAll('.mensagem-item');
            
            mensagens.forEach(msg => {
                const assunto = msg.getAttribute('data-assunto').toLowerCase();
                const remetente = msg.getAttribute('data-remetente').toLowerCase();
                
                if (assunto.includes(busca) || remetente.includes(busca)) {
                    msg.style.display = 'block';
                } else {
                    msg.style.display = 'none';
                }
            });
        }

        // Preencher campo de remetente com contato
        function preencherDestinatario(nome) {
            document.getElementById('remetente').value = nome;
            const modal = new bootstrap.Modal(document.getElementById('modalNovaMensagem'));
            modal.show();
        }

        // Mostrar notificação
        function mostrarNotificacao(mensagem, tipo = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${tipo} alert-dismissible fade show toast-notification`;
            toast.innerHTML = `
                ${mensagem}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 4000);
        }
    </script>
</body>
</html>
