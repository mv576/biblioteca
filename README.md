# 📚 Sistema de Gerenciamento de Biblioteca (SGB)

> Este projeto é um sistema de gerenciamento de biblioteca completo, desenvolvido para praticar conceitos de **Back-end com PHP** e **Front-end com Bootstrap**. O arquivo principal (`index.html`) atua como o *dashboard* ou menu principal do sistema, direcionando o usuário para as diferentes funcionalidades.

**Status do Projeto:** 🟢 **Concluído**

---

## 🛠️ Tecnologias Utilizadas

Este é um projeto full-stack que utiliza:

* **Front-End (Este Menu):**
    * **HTML5:** Estruturação da página.
    * **Bootstrap 5:** Framework CSS principal para layout, grid responsivo e componentes (cards, botões).
    * **Bootstrap Icons:** Para a iconografia dos cards.
    * **CSS3 Customizado:** Efeitos de *hover* (`transform`, `box-shadow`) para melhorar a interatividade da UI.

* **Back-End (Páginas de Ação):**
    * **PHP:** Linguagem principal para processar todas as requisições, como cadastros, listagens e empréstimos.
    * **Banco de Dados (ex: MySQL):** Para armazenar e gerenciar os dados de livros, usuários e empréstimos.

---

## ✨ Funcionalidades Principais

A partir deste menu, o usuário pode acessar os seguintes módulos:

* **Cadastrar Livro:** Um formulário HTML (`cadastrar.html`) que envia os dados para um script PHP (não mostrado) para inserir um novo livro no banco de dados.
* **Listar Livros:** Uma página PHP (`listar.php`) que executa uma consulta no banco de dados e exibe todos os livros cadastrados em uma tabela.
* **Empréstimo e Devolução:** Módulos PHP (`emprestimo.php`, `devolucao.php`) que controlam a lógica de registrar a saída e a entrada de livros, atualizando o status no banco.
* **Relatórios:** Página PHP (`relatorios.php`) que gera estatísticas sobre o acervo (ex: livros mais emprestados, livros em atraso).

---

## 📖 Como Rodar o Projeto

Como este projeto depende de PHP e um banco de dados, ele **não funciona** apenas abrindo o `index.html` no navegador.

**Pré-requisitos:** Ter um servidor local instalado (XAMPP, WAMP, MAMP).

1.  Clone este repositório:
    ```bash
    git clone [https://github.com/seu-usuario/nome-do-repositorio.git](https://github.com/seu-usuario/nome-do-repositorio.git)
    ```
2.  Mova a pasta do projeto para dentro da pasta `htdocs` (no XAMPP) ou `www` (no WAMP/MAMP).
3.  Inicie os serviços do Apache e MySQL no seu servidor local.
4.  Crie um banco de dados (ex: `biblioteca`) no seu phpMyAdmin.
5.  Importe o arquivo `.sql` (você precisa adicionar este arquivo ao repositório) para o banco de dados criado.
6.  Acesse o projeto pelo navegador: `http://localhost/nome-do-repositorio/`

