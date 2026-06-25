# NURA – Plataforma Digital para Alimentação Saudável

<p align="center">
  <img src="https://github.com/user-attachments/assets/7dea795b-888b-47e4-b2ca-8e10fa88212e" alt="Preview da plataforma Nura" width="100%">
</p>

## 📌 Sumário

- [Sobre o Projeto](#-sobre-o-projeto)
- [Integrantes](#-integrantes)
- [Tema Central do TCC](#-tema-central-do-tcc)
- [Propósito Principal](#-propósito-principal)
- [Objetivos Específicos](#-objetivos-específicos)
- [Justificativa](#-justificativa)
- [Status do Projeto](#-status-do-projeto)
- [Tecnologias Utilizadas](#-tecnologias-utilizadas)
- [Ambiente Local (Como Rodar)](#-ambiente-local-como-rodar)
- [Como Contribuir](#-como-contribuir)
- [Contato](#-contato)
- [Roadmap de Desenvolvimento](#-roadmap-de-desenvolvimento)
- [Link de Acesso ao Sistema](#-link-de-acesso-ao-sistema)
- [Licença (MIT License)](#-licença-mit-license)

---

## 📝 Sobre o Projeto

O **NURA** é uma plataforma digital responsiva voltada à comercialização de produtos alimentares adaptados a diferentes necessidades nutricionais. O ecossistema atende públicos com restrições ou preferências específicas, como **intolerância à lactose, intolerância ao glúten, vegetarianismo, veganismo e controle de diabetes**. 

A solução conta com um e-commerce integrado a serviços de entrega (delivery), permitindo que os usuários recebam alimentos saudáveis compatíveis com seu perfil em domicílio. Além disso, possui uma área administrativa robusta para gestão de produtos, estoques, pedidos, clientes e relatórios gerenciais estruturada em uma interface altamente intuitiva.

---

## 👥 Integrantes

* **Wellington Pereira Cavalcanti** – RM 26530 – <engwellingtoncavalcanti@gmail.com>
* **Matheus Chagas Colasso Ferreira** – RM 23280 – <matheuscolasso2006@gmail.com>
* **Jairo Machado Alves do Carmo** – RM 26529 – <jairomahadoalves35@gmail.com>
* **Kauan Araujo Santos** – RM 26670 – <kauan.santos.andre@gmail.com>

---

## 🎯 Tema Central do TCC

Desenvolvimento da plataforma digital NURA para alimentação saudável, com e-commerce e gestão personalizada de produtos para dietas especiais.

---

## 🚀 Propósito Principal

Oferecer uma solução digital inovadora que facilite o acesso a produtos alimentares específicos por meio de uma loja virtual moderna, personalizada e com serviço de entregas, atendendo pessoas com restrições ou escolhas alimentares específicas e disponibilizando ferramentas administrativas completas.

---

## 📋 Objetivos Específicos

- [x] **E-commerce Responsivo:** Desenvolver uma interface adaptável para computadores, tablets e smartphones.
- [x] **Filtros Personalizados:** Implementar segmentação avançada por perfil alimentar (sem glúten, sem lactose, vegano, vegetariano, etc.).
- [x] **Fazendo Compras:** Incluir recursos essenciais como carrinho de compras, lista de favoritos, simulação de pagamento, cálculo de frete e sistema de avaliações de produtos.
- [x] **Painel Administrativo:** Criar um dashboard gerencial com gráficos analíticos, controle rigoroso de estoque, gerenciamento de pedidos e usuários.
- [x] **Acessibilidade e UX:** Adicionar suporte a múltiplos perfis de acessibilidade, modo escuro (Dark Mode), notificações automáticas por e-mail e um chatbot estruturado de FAQ.

---

## 💡 Justificativa

A crescente busca por uma rotina de alimentação saudável e personalizada exige plataformas que centralizem produtos adequados com uma logística de entrega eficiente — uma fatia de mercado latente que ainda carece de soluções integradas de ponta a ponta.

---

## ⚡ Status do Projeto

⏳ **Em desenvolvimento ativo.** Atualizações constantes estão sendo aplicadas à medida que novos módulos e otimizações são validados pela equipe.

---

## 🛠 Tecnologias Utilizadas

O projeto foi construído utilizando tecnologias modernas e escaláveis para o desenvolvimento web:

* **Front-end:** HTML5, CSS3, JavaScript (ES6+)
* **Back-end:** PHP
* **Banco de Dados:** SQL
* **Infraestrutura e Cloud:** Docker, Microsoft Azure, Firebase

---

## 💻 Ambiente Local (Como Rodar)

O projeto utiliza o **Docker** para empacotar, gerenciar e isolar o ambiente local (servidor Apache/PHP e o banco de dados SQL). Isso elimina a necessidade de instalar dependências diretamente no sistema operacional da sua máquina.

### Pré-requisitos
Antes de iniciar, garanta que você possui os seguintes softwares instalados:
* [Git](https://git-scm.com/)
* [Docker Desktop](https://www.docker.com/products/docker-desktop/) *(certifique-se de que o Docker está em execução)*

### Passo a Passo para Execução

Abra o terminal de sua preferência (Prompt de Comando, PowerShell ou Terminal Linux/Mac) e execute os comandos abaixo na ordem indicada:

```bash
# 1. Clone o repositório oficial
git clone [https://github.com/Etec-da-Zona-Leste-TCCs-DS-Noite/nura-tcc.git](https://github.com/Etec-da-Zona-Leste-TCCs-DS-Noite/nura-tcc.git)

# 2. Acesse o diretório do projeto
cd nura-tcc

# 3. Entre na pasta raiz do código-fonte
cd src

# 4. Construa e inicialize os containers em segundo plano
docker compose up -d --build
