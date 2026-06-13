# Como Executar o Projeto ConectaSaúde

## Requisitos

Antes de iniciar, certifique-se de ter instalado:

* XAMPP
* PHP 8.2 ou superior
* MariaDB/MySQL
* Navegador Web

## 1. Instalar o XAMPP

Baixe e instale o XAMPP através do site oficial:

https://www.apachefriends.org/

Após a instalação, abra o Painel de Controle do XAMPP.

## 2. Iniciar os Serviços

No painel do XAMPP, inicie os seguintes serviços:

* Apache
* MySQL

Ambos devem ficar com o status em verde.

## 3. Copiar os Arquivos do Projeto

Copie a pasta do projeto ConectaSaúde para o diretório:

C:\xampp\htdocs\

Exemplo:

C:\xampp\htdocs\conectasaude

## 4. Criar o Banco de Dados

1. Abra o navegador.
2. Acesse:

http://localhost/phpmyadmin

3. Clique em "Novo".
4. Crie um banco de dados chamado:

conecta_saude

5. Selecione o banco criado.
6. Clique na aba "Importar".
7. Selecione o arquivo SQL fornecido com o projeto.
8. Clique em "Executar".

Após a importação, todas as tabelas e registros iniciais serão criados automaticamente.

## 5. Configurar a Conexão com o Banco

Abra o arquivo de configuração do banco de dados e verifique as credenciais:

Host: localhost
Banco: conecta_saude
Usuário: root
Senha: (vazia por padrão no XAMPP)

Exemplo:

```php
$host = "localhost";
$dbname = "conecta_saude";
$user = "root";
$password = "";
```

## 6. Executar o Projeto

Com Apache e MySQL em execução, acesse:

http://localhost/conectasaude

A aplicação será carregada automaticamente.

## 7. Usuário Administrador

O sistema possui um usuário administrador pré-cadastrado:

E-mail: admin@conectasaude.com
Senha: admin123

Tipo: Administrador

Esse usuário pode:
* Autorizar cadastros de agentes comunitários.
* Visualizar todos os usuários.
* Editar dados de usuários.
* Excluir contas de usuários.

Caso necessário, a senha poderá ser redefinida diretamente no banco de dados.

## Solução de Problemas

### Erro de conexão com o banco

Verifique se:

* O MySQL está iniciado no XAMPP.
* O banco conecta_saude foi criado corretamente.
* As credenciais do arquivo de configuração estão corretas. 

### Página não encontrada

Verifique se a pasta do projeto está localizada em:

C:\xampp\htdocs\conectasaude

### Porta 80 ocupada

Caso o Apache não inicie, altere a porta do Apache ou encerre programas que estejam utilizando a porta 80 (IIS, Skype, etc.).

## Licença

Projeto desenvolvido para fins acadêmicos e demonstrativos.
