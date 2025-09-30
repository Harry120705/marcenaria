
# Marcenaria

## Descrição

Sistema web moderno para gerenciamento de marcenaria, desenvolvido em PHP, MySQL, HTML, CSS e JavaScript. O sistema é responsivo, seguro e modular, com controle de acesso por tipo de usuário (admin/usuário comum) via JWT.

## Funcionalidades Principais
- Cadastro, login e autenticação de usuários (JWT)
- Controle de acesso: telas administrativas só para admin
- Cadastro, edição, exclusão e destaque de produtos
- Listagem e edição de usuários
- Carrossel horizontal de produtos por categoria
- Card de produtos em destaque (dashboard e loja)
- Card dinâmico via AJAX ao clicar em "Ver mais"
- Menu hamburguer responsivo em todas as telas principais
- Botões de login/cadastro dinâmicos (somem após login)
- Mensagem de saudação personalizada após login
- Alteração de senha
- Logout
- CSS modular por tela, visual limpo e moderno

## Tecnologias Utilizadas
- PHP
- MySQL
- HTML, CSS (modular, responsivo)
- JavaScript (AJAX, carrossel, modais)

## Estrutura de Arquivos
- `index.php`: Tela de login
- `register.php`: Cadastro de usuário
- `dashboard.php`: Painel principal (admin)
- `usuarios.php`: Listagem de usuários (admin)
- `usuario.php`: Detalhes/edição de usuário (admin)
- `produtos.php`: Listagem de produtos (admin)
- `produto.php`: Detalhes/edição de produto (admin)
- `alterar_senha.php`: Alteração de senha
- `logout.php`: Logout do sistema
- `db.php`: Conexão com o banco de dados
- `loja.php`: Tela principal da loja para usuários comuns
- `loja.css`, `dashboard.css`, `produtos.css`, etc: CSS modular por tela
- `loja.js`, `dashboard.js`, etc: JS modular por tela
- `uploads/`, `icons/`: Imagens e ícones

## Segurança
- Autenticação por JWT
- Controle de acesso: apenas admin acessa dashboard e telas administrativas
- Usuário comum só acessa loja, orçamento, configurações e dados próprios

## Como rodar o projeto

### 1. Pré-requisitos
- PHP 7.4+
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
- **Sobre o campo de destaque:**
  - A tabela de produtos possui o campo `destaque` (TINYINT, padrão 0).
  - Até 12 produtos podem ser destacados.
  - O dashboard exibe até 12 produtos em destaque, ou os 12 de menor estoque caso não haja suficientes destacados.

### 5. Configure a conexão com o banco
- Edite o arquivo `db.php` e ajuste usuário, senha e nome do banco conforme seu ambiente local.

### 6. Rode o sistema
- Acesse no navegador: `http://localhost/marcenaria/marcenaria/index.php`
- Faça login com um dos usuários já cadastrados ou cadastre um novo.

### 7. Dicas e observações
- Sempre importe as categorias antes dos produtos para evitar erros de chave estrangeira.
- Se aparecer erro de foreign key ao truncar tabelas, desabilite `FOREIGN_KEY_CHECKS`.
- Para ambiente de produção, reforce as configurações de segurança.
- Certifique-se de que a pasta `uploads/` tem permissão de escrita, caso use upload de imagens.
- O menu hamburguer e os botões de login/cadastro são responsivos e dinâmicos.
- O sistema é modular, fácil de manter e expandir.

---

Desenvolvido por Harry120705

---