# Marcenaria

## Descrição

Sistema web simples para gerenciamento de marcenaria, desenvolvido em PHP. Permite o cadastro, edição e exclusão de usuários e produtos, além de controle de login e alteração de senha.

## Funcionalidades
- Cadastro e login de usuários
- Cadastro, edição e exclusão de produtos
- Listagem de usuários e produtos
- Alteração de senha
- Logout

## Tecnologias Utilizadas
- PHP
- MySQL (requer banco de dados configurado)
- HTML, CSS, JavaScript

## Estrutura de Arquivos
- `index.php`: Tela de login
- `register.php`: Cadastro de usuário
- `dashboard.php`: Painel principal
- `usuarios.php`: Listagem de usuários
- `usuario.php`: Detalhes/edição de usuário
- `produtos.php`: Listagem de produtos
- `produto.php`: Detalhes/edição de produto
- `alterar_senha.php`: Alteração de senha
- `logout.php`: Logout do sistema
- `db.php`: Conexão com o banco de dados
- `script.js`: Scripts JavaScript
- `style.css`: Estilos CSS


## Como rodar o projeto (Passo a Passo Completo)

### 1. Pré-requisitos
   - PHP instalado (recomendado PHP 7.4+)
   - Servidor web local (XAMPP, WAMP, Laragon, etc.)
   - MySQL/MariaDB

### 2. Instale e configure o ambiente
   - Instale o XAMPP ou similar e inicie o Apache e o MySQL.
   - Coloque a pasta do projeto dentro do diretório `htdocs` (XAMPP) ou equivalente.

### 3. Crie o banco de dados
   - Acesse o phpMyAdmin ou use o terminal MySQL.
   - Crie o banco de dados:
      ```sql
      CREATE DATABASE marcenaria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
      ```

### 4. Importe as tabelas e dados
   - Importe o arquivo de estrutura do banco (ex: `banco_marcenaria.sql`).
   - Importe o arquivo de povoamento (ex: `povoar_marcenaria.sql`).
   - **Ordem recomendada:**
      1. Primeiro, importe o arquivo de estrutura (tabelas).
      2. Depois, importe o arquivo de povoamento (dados).

   - **Se precisar limpar o banco para reinserir os dados:**
      1. Desabilite as restrições de foreign key:
          ```sql
          SET FOREIGN_KEY_CHECKS = 0;
          ```
      2. Trunque as tabelas na ordem correta:
          ```sql
          TRUNCATE TABLE produtos;
          TRUNCATE TABLE categorias;
          TRUNCATE TABLE usuarios;
          ```
      3. Reabilite as restrições:
          ```sql
          SET FOREIGN_KEY_CHECKS = 1;
          ```

### 5. Configure a conexão com o banco
   - Edite o arquivo `db.php` e ajuste usuário, senha e nome do banco conforme seu ambiente local.

### 6. Rode o sistema
   - Acesse no navegador: `http://localhost/marcenaria/marcenaria/index.php`
   - Faça login com um dos usuários já cadastrados ou cadastre um novo.

### 7. Dicas e observações
   - Sempre importe as categorias antes dos produtos para evitar erros de chave estrangeira.
   - Se aparecer erro de foreign key ao truncar tabelas, siga o passo de desabilitar `FOREIGN_KEY_CHECKS`.
   - Para ambiente de produção, reforce as configurações de segurança.
   - Certifique-se de que a pasta `uploads/` tem permissão de escrita, caso use upload de imagens.

---

Desenvolvido por Harry120705

---