# 📖 Documentação de Manutenção - Conecta Saúde

## 🎯 Status Atual

**Todos os problemas foram resolvidos e testados:**
- ✅ Foto de perfil atualiza corretamente
- ✅ Imagens aparecem adequadamente  
- ✅ Navbar mostra layout correto
- ✅ Login funciona com usuários cadastrados

---

## 📚 Arquivos Principais

### Autenticação & Login
- **login.php** (8KB)
  - Autentica usuários contra `usuarios.json`
  - Usa `password_verify()` para validação
  - Define variáveis de sessão completas
  
- **cadastro.php** (16KB)
  - Registra novos usuários
  - Faz upload de foto (opcional)
  - Salva em `usuarios.json` com senha hashada

### Perfil & Configurações
- **perfil.php** (24KB) ⭐ RECRIADO
  - Edição completa do perfil
  - Upload de foto com validação
  - Persistência em `dados/perfil_[id].json`
  - CSS integrado para layout correto

### Dashboard & Navegação
- **dashboard.php** (28KB)
  - Página principal após login
  - Carrega dados reais de `dados/consultas_[id].json`
  - Exibe estatísticas

- **navbar.php** (8KB)
  - Barra de navegação incluída em todas as páginas
  - Exibe avatar e menu do usuário
  - Acesso a perfil, consultas, mensagens

### Configuração
- **conexao.php** (4KB) ⭐ ATUALIZADO
  - Configurações gerais (timezone, diretórios)
  - **NOTA: Removida dependência de MySQL**
  - Apenas configurações estáticas

---

## 📁 Estrutura de Dados

### usuarios.json
```json
[
  {
    "id": "string único",
    "nome": "Nome do usuário",
    "email": "email@example.com",
    "senha": "$2y$10$hashbcrypt...",
    "tipo": "paciente|profissional",
    "foto": "nome_arquivo.jpg",
    "data_cadastro": "2025-02-05 13:30:00"
  }
]
```

### dados/perfil_[id].json
```json
{
  "id": "usuario_id",
  "nome": "Nome Completo",
  "email": "email@example.com",
  "tipo": "paciente",
  "telefone": "(11) 98765-4321",
  "data_nascimento": "1990-01-15",
  "cpf": "123.456.789-00",
  "endereco": "Rua das Flores, 123",
  "cidade": "São Paulo",
  "estado": "SP",
  "cep": "01234-567",
  "foto": "6984c142d1b90_1649856000.jpg"
}
```

### dados/consultas_[id].json
```json
[
  {
    "id": "unique_id",
    "especialidade": "Cardiologia",
    "profissional": "Dr. João",
    "data": "2025-02-10",
    "hora": "14:30",
    "status": "agendado|confirmado|realizado|cancelado",
    "observacoes": "Texto..."
  }
]
```

### dados/mensagens_[id].json
```json
[
  {
    "id": "unique_id",
    "remetente_id": "usuario_id",
    "remetente_nome": "Nome",
    "titulo": "Assunto",
    "conteudo": "Mensagem...",
    "data": "2025-02-05 13:30:00",
    "lido": true|false
  }
]
```

---

## 🔐 Sistema de Segurança

### Senhas
- ✅ Hashadas com bcrypt (`PASSWORD_DEFAULT`)
- ✅ Verificadas com `password_verify()`
- ✅ Nunca armazenadas em texto plano

### Upload de Arquivos
- ✅ Validação de extensão (JPG, PNG, GIF)
- ✅ Validação de tamanho (máximo 5MB)
- ✅ Nomeação com timestamp único
- ✅ Salvos em `uploads/`

### Input Validation
- ✅ Sanitização com `htmlspecialchars()`
- ✅ Validação de CPF
- ✅ Validação de email
- ✅ Verificação de sessão

---

## 🛠️ Manutenção Rotineira

### Limpeza de Uploads Antigos
```bash
# Remover arquivos com mais de 90 dias
find /var/www/html/conectasaude/uploads -type f -mtime +90 -delete
```

### Backup de Dados
```bash
# Backup manual dos dados
tar -czf conectasaude_backup_$(date +%Y%m%d).tar.gz \
  /var/www/html/conectasaude/dados/ \
  /var/www/html/conectasaude/usuarios.json
```

### Verificar Permissões
```bash
# Garantir permissões corretas
chmod 777 /var/www/html/conectasaude/dados
chmod 777 /var/www/html/conectasaude/uploads
chmod 644 /var/www/html/conectasaude/usuarios.json
```

---

## 🔧 Troubleshooting

### Erro: "Erro ao fazer upload da foto"
**Causa:** Pasta `uploads/` não tem permissão de escrita
**Solução:**
```bash
sudo chmod 777 /var/www/html/conectasaude/uploads
sudo chown www-data:www-data /var/www/html/conectasaude/uploads
```

### Erro: "Erro ao salvar perfil"
**Causa:** Pasta `dados/` não tem permissão de escrita
**Solução:**
```bash
sudo chmod 777 /var/www/html/conectasaude/dados
sudo chown www-data:www-data /var/www/html/conectasaude/dados
```

### Avatar não aparece
**Causa:** Arquivo default-avatar.png está faltando
**Solução:** Verificar se `assets/default-avatar.png` existe (648 bytes)

### Login falha
**Causa:** `usuarios.json` corrompido
**Solução:** Verificar JSON com: `php -r "json_decode(file_get_contents('usuarios.json'), true);"`

---

## 📊 Monitoramento

### Verificar Saúde do Sistema
Execute regularmente:
```bash
cd /var/www/html/conectasaude && bash validacao.sh
```

Deve exibir:
- ✅ Todos os arquivos presentes
- ✅ Todas as pastas existentes e escritáveis
- ✅ Pelo menos 1 usuário cadastrado

### Logs
Atualmente não há sistema de logs implementado. Para adicionar:
```php
// Adicionar ao topo de cada arquivo crítico
error_log("[" . date('Y-m-d H:i:s') . "] Action por " . $_SESSION['usuario_id']);
```

---

## 🔄 Fluxo de Autenticação

```
1. Usuário acessa login.php
2. Insere email e senha
3. login.php lê usuarios.json
4. Busca usuário por email
5. Verifica senha com password_verify()
6. Define variáveis de sessão
7. Redireciona para dashboard.php
8. navbar.php carrega usando $_SESSION
9. Usuário pode editar perfil em perfil.php
```

---

## 📋 Checklist de Manutenção Mensal

- [ ] Executar `validacao.sh` e verificar status
- [ ] Fazer backup de `dados/` e `usuarios.json`
- [ ] Limpar uploads antigos (90+ dias)
- [ ] Verificar permissões de pastas
- [ ] Verificar se há usuários inativos para arquivar
- [ ] Testar login com usuário real
- [ ] Testar upload de foto
- [ ] Verificar espaço em disco

---

## 📞 Suporte

Para problemas:
1. Verifique `validacao.sh` para status do sistema
2. Verifique logs do Apache/PHP
3. Verifique permissões de arquivos/pastas
4. Verifique se `usuarios.json` está válido
5. Verifique se diretórios `dados/` e `uploads/` existem

---

**Última atualização:** 2025-02-05  
**Versão:** 1.0  
**Status:** ✅ OPERACIONAL
