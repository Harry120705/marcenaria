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

## Como rodar o projeto

1. **Pré-requisitos:**
   - PHP instalado (recomendado PHP 7.4+)
   - Servidor web (ex: XAMPP, WAMP, Laragon)
   - MySQL/MariaDB

2. **Clone ou copie os arquivos do projeto para a pasta do seu servidor web.**

3. **Configure o banco de dados:**
   - Crie um banco de dados no MySQL (ex: `marcenaria`).
   - Importe o arquivo de estrutura/tabelas (caso exista, ou crie as tabelas manualmente conforme necessário).
   - Atualize as credenciais de acesso ao banco em `db.php` se necessário.

4. **Inicie o servidor web e acesse o sistema:**
   - Exemplo: `http://localhost/marcenaria/marcenaria/index.php`

5. **Cadastre um novo usuário e faça login para acessar o sistema.**

## Observações
- Certifique-se de que as permissões de arquivos estejam corretas para leitura e escrita.
- Para ambiente de produção, altere as configurações de segurança conforme necessário.

---

Desenvolvido por Harry120705