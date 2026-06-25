# NURA – Plataforma Digital para Alimentação Saudável

## Sumário

- [Descrição](#descrição)
- [Integrantes](#integrantes)
- [Tema Central do TCC](#tema-central-do-tcc)
- [Propósito Principal](#propósito-principal)
- [Objetivos Específicos](#objetivos-específicos)
- [Justificativa](#justificativa)
- [Status do Projeto](#status-do-projeto)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Como Rodar o Projeto](#como-rodar-o-projeto)
- [Screenshots](#screenshots)
- [Como Contribuir](#como-contribuir)
- [Contato](#contato)
- [Roadmap](#roadmap)
- [Licença](#licença)
- [Link de Acesso ao Sistema](#link-de-access-ao-sistema)

---

## Descrição

NURA é uma plataforma digital responsiva voltada à comercialização de produtos alimentares adaptados a diferentes necessidades nutricionais, como intolerância à lactose, intolerância ao glúten, vegetarianismo, veganismo e controle de diabetes. O sistema contará com um e-commerce integrado a serviços de entrega (delivery), permitindo que os usuários recebam em domicílio alimentos saudáveis compatíveis com seu perfil alimentar. Além disso, será desenvolvida uma área administrativa robusta para gestão de produtos, estoques, pedidos, clientes e relatórios, tudo em uma interface intuiva e acessível.

## Integrantes

* **Wellington Pereira Cavalcanti** – RM 26530 – <engwellingtoncavalcanti@gmail.com>
* **Matheus Chagas Colasso Ferreira** – RM 23280 – <matheuscolasso2006@gmail.com>
* **Jairo Machado Alves do Carmo** – RM 26529 – <jairomahadoalves35@gmail.com>
* **Kauan Araujo Santos** – RM 26670 – <kauan.santos.andre@gmail.com>

## Tema Central do TCC

Desenvolvimento da plataforma digital NURA para alimentação saudável, com e-commerce e gestão personalizada de produtos para dietas especiais.

## Propósito Principal

Oferecer uma solução digital inovadora que facilite o acesso a produtos alimentares específicos por meio de uma loja virtual moderna, personalizada e com serviço de entregas, atendendo pessoas com restrições ou escolhas alimentares específicas e disponibilizando ferramentas administrativas completas.

## Objetivos Específicos

* Desenvolver site responsivo de e-commerce para venda e entrega de alimentos saudáveis;
* Implementar filtros por perfil alimentar (sem glúten, sem lactose, vegano, vegetariano, etc.);
* Incluir carrinho de compras, lista de favoritos, simulação de pagamento, cálculo de frete e sistema de avaliações;
* Criar área administrativa com dashboard, gráficos e controle de estoque, pedidos e usuários;
* Adicionar acessibilidade, modo escuro, notificações por e-mail e chatbot FAQ;
* Garantir experiência prática, segura e acessível para entrega domiciliar.

## Justificativa

A crescente busca por alimentação saudável e personalizada exige plataformas digitais que concentrem produtos adequados com entrega eficiente, área ainda pouco atendida no mercado.

## Status do Projeto

Em desenvolvimento. Atualizações serão feitas conforme novas funcionalidades forem implementadas.

## Tecnologias Utilizadas

* PHP
* HTML5 / CSS3 / JavaScript
* Docker
* Azure
* Firebase
* SQL

---

## Como Rodar o Projeto

Este projeto utiliza o **Docker** para gerenciar e isolar o ambiente de desenvolvimento local (servidor web PHP e banco de dados SQL), dispensando a necessidade de instalações manuais na sua máquina física.

### Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:
* [Git](https://git-scm.com/)
* [Docker Desktop](https://www.docker.com/products/docker-desktop/) (deve estar aberto e rodando durante a execução do projeto)

### Passo a Passo

Abra o seu terminal (Prompt de Comando, PowerShell ou Terminal do Linux/Mac) e execute a sequência de comandos abaixo:

```bash
# Clone o repositório
git clone [https://github.com/Etec-da-Zona-Leste-TCCs-DS-Noite/nura-tcc.git](https://github.com/Etec-da-Zona-Leste-TCCs-DS-Noite/nura-tcc.git)

# Acesse a pasta do projeto
cd nura-tcc

# Acesse a pasta do código-fonte
cd src

# Inicie os containers do Docker
docker compose up -d --build
