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

## Descrição

NURA é uma plataforma digital responsiva voltada à comercialização de produtos alimentares adaptados a diferentes necessidades nutricionais, como intolerância à lactose, intolerância ao glúten, vegetarianismo, veganismo e controle de diabetes. O sistema contará com um e-commerce integrado a serviços de entrega (delivery), permitindo que os usuários recebam em domicílio alimentos saudáveis compatíveis com seu perfil alimentar. Além disso, será desenvolvida uma área administrativa robusta para gestão de produtos, estoques, pedidos, clientes e relatórios, tudo em uma interface intuitiva e acessível.

## Integrantes

- Wellington Pereira Cavalcanti – RM 26530 - engwellingtoncavalcanti@gmail.com
- Matheus Chagas Colasso Ferreira – RM 23280 - matheuscolasso2006@gmail.com
- Jairo Machado Alves do Carmo – RM 26529 - jairomahadoalves35@gmail.com
- Kauan Araujo Santos - RM 26670 - kauan.santos.andre@gmail.com 

## Tema Central do TCC

Desenvolvimento da plataforma digital NURA para alimentação saudável, com e-commerce e gestão personalizada de produtos para dietas especiais.

## Propósito Principal

Oferecer uma solução digital inovadora que facilite o acesso a produtos alimentares específicos por meio de uma loja virtual moderna, personalizada e com serviço de entregas, atendendo pessoas com restrições ou escolhas alimentares específicas e disponibilizando ferramentas administrativas completas.

## Objetivos Específicos

- Desenvolver site responsivo de e-commerce para venda e entrega de alimentos saudáveis;  
- Implementar filtros por perfil alimentar (sem glúten, sem lactose, vegano, vegetariano, etc.);  
- Incluir carrinho de compras, lista de favoritos, simulação de pagamento, cálculo de frete e sistema de avaliações;  
- Criar área administrativa com dashboard, gráficos e controle de estoque, pedidos e usuários;  
- Adicionar acessibilidade, modo escuro, notificações por e-mail e chatbot FAQ;  
- Garantir experiência prática, segura e acessível para entrega domiciliar.

## Justificativa

A crescente busca por alimentação saudável e personalizada exige plataformas digitais que concentrem produtos adequados com entrega eficiente, área ainda pouco atendida no mercado.

## Status do Projeto

Em desenvolvimento. Atualizações serão feitas conforme novas funcionalidades forem implementadas.

## Tecnologias Utilizadas

- PHP
- HTML5
- CSS3
- JavaScript
- Docker
- Azure
- FireBase
- SQL

## Como Rodar o Projeto

Este projeto utiliza **Docker** para simplificar o ambiente de desenvolvimento local, gerenciando os containers do PHP (servidor web) e do Banco de Dados (SQL).

### Pré-requisitos

Antes de começar, você precisará ter instalado em sua máquina:
- [Git](https://git-scm.com/)
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (já inclui o Docker Compose)

### Passo a Passo
#### 1. Clonar o Repositório
 - Abra o seu terminal (Prompt de Comando, PowerShell ou Terminal do Linux/Mac) e clone o projeto:

#### 2.Preparando o ambiente no seu computador
 - git clone https://github.com/Etec-da-Zona-Leste-TCCs-DS-Noite/nura-tcc.git
 - cd nura-tcc

#### 3. Criando a configuração do Docker para a pasta "src"
FROM php:8.2-apache

# Instala extensões necessárias para o PHP se conectar ao banco SQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Aponta o servidor Apache para ler a pasta src/ do seu projeto
ENV APACHE_DOCUMENT_ROOT /var/www/html/src
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilita o módulo de reescrita do Apache (caso usem rotas amigáveis)
RUN a2enmod rewrite
 

#### 4. Instalar as Dependências (Se aplicável)
docker compose exec app composer install

## Screenshots
<img width="1899" height="877" alt="imagem" src="https://github.com/user-attachments/assets/7dea795b-888b-47e4-b2ca-8e10fa88212e" />


## Como Contribuir

1. Faça um fork do repositório  
2. Crie uma branch para sua feature (`git checkout -b feature/nome-da-feature`)  
3. Faça commit das suas alterações (`git commit -m 'Adiciona nova feature'`)  
4. Envie para o seu fork (`git push origin feature/nome-da-feature`)  
5. Abra um Pull Request neste repositório

## Contato

- Wellington Pereira: [github.com/wellingtonpc17](https://github.com/wellingtonpc17)  
- Matheus Chagas: [github.com/mcolasso](https://github.com/mcolasso)  
- Jairo Machado: [github.com/jairoalves2741](https://github.com/jairoalves2741)
- Kauan Araujo: [github.com/kauansantos9](https://github.com/kauansantos9)

## Roadmap

- [x] Planejamento do projeto  
- [x] Definição do tema e objetivos  
- [x] Desenvolvimento do front-end responsivo  
- [x] Implementação do sistema de filtros e carrinho de compras  
- [x] Desenvolvimento da área administrativa  
- [x] Integração com sistema de entrega  
- [x] Testes e ajustes finais  
- [x] Deploy e documentação completa  

## Licença

Este projeto está licenciado sob a Licença MIT. Veja o arquivo LICENSE para mais detalhes.
