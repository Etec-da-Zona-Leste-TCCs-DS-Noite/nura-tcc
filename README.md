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

* **Wellington Pereira Cavalcanti** – RM 26530 – engwellingtoncavalcanti@gmail.com
* **Matheus Chagas Colasso Ferreira** – RM 23280 – matheuscolasso2006@gmail.com
* **Jairo Machado Alves do Carmo** – RM 26529 – jairomahadoalves35@gmail.com
* **Kauan Araujo Santos** – RM 26670 – kauan.santos.andre@gmail.com

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
git clone https://github.com/Etec-da-Zona-Leste-TCCs-DS-Noite/nura-tcc.git

# 2. Acesse o diretório do projeto
cd nura-tcc

# 3. Entre na pasta raiz do código-fonte
cd src

# 4. Construa e inicialize os containers em segundo plano
docker compose up -d --build
```

Após o processo de compilação terminar, abra o seu navegador de preferência e acesse a aplicação pelo endereço:

👉 **http://localhost:80**

---

## 🤝 Como Contribuir

Contribuições são o que tornam a comunidade de desenvolvedores um lugar incrível para aprender, inspirar e criar. Toda contribuição que você fizer será **muito apreciada**.

Para contribuir com o projeto **NURA**, siga o fluxo de trabalho detalhado abaixo:

### 1. Preparando o Ambiente e Fork

1. Faça um **Fork** deste projeto clicando no botão *Fork* no canto superior direito desta página.
2. Clone o **seu** fork localmente na sua máquina (substitua `SEU_USUARIO` pelo seu username do GitHub):

```bash
git clone https://github.com/SEU_USUARIO/nura-tcc.git
```

3. Acesse a pasta do repositório clonado:

```bash
cd nura-tcc
```

### 2. Criando uma Branch de Recurso

Sempre crie uma nova branch para trabalhar em uma modificação específica. Evite fazer alterações diretamente na branch `main`.

```bash
git checkout -b feature/nome-da-sua-feature
# Exemplo: git checkout -b feature/ajuste-carrinho
```

### 3. Commitando Alterações com Padrão

Após escrever e testar o código, adicione os arquivos e salve suas alterações usando mensagens claras baseadas em **Conventional Commits**:

```bash
# Adicione os arquivos alterados
git add .

# Registre o commit com o prefixo correto
git commit -m "feat: adiciona componente de notificações por e-mail"
```

*Tipos comuns:* `feat:` para novos recursos, `fix:` para correções de bugs, `docs:` para documentação.

### 4. Enviando e Solicitando Integração

1. Envie a sua branch criada para o seu repositório fork no GitHub:

```bash
git push origin feature/nome-da-sua-feature
```

2. Volte para a página do repositório principal `Etec-da-Zona-Leste-TCCs-DS-Noite/nura-tcc`.
3. Uma barra amarela aparecerá exibindo o botão **"Compare & pull request"**. Clique nele.
4. Escreva uma breve descrição das modificações introduzidas e envie o seu **Pull Request (PR)**.

---

## 📞 Contato

Para dúvidas, feedbacks ou troca de informações sobre a plataforma NURA, sinta-se à vontade para entrar em contato com os membros desenvolvedores:

| Integrante | E-mail de Contato | GitHub |
| --- | --- | --- |
| **Wellington Pereira Cavalcanti** | engwellingtoncavalcanti@gmail.com | [@wellingtonpc17](https://github.com/wellingtonpc17) |
| **Matheus Chagas C. Ferreira** | matheuscolasso2006@gmail.com | [@mcolasso](https://github.com/mcolasso) |
| **Jairo Machado Alves do Carmo** | jairomahadoalves35@gmail.com | [@jairoalves2741](https://github.com/jairoalves2741) |
| **Kauan Araujo Santos** | kauan.santos.andre@gmail.com | [@kauansantos9](https://github.com/kauansantos9) |

---

## 🗺 Roadmap de Desenvolvimento

Acompanhe as etapas de evolução planejadas e executadas para o desenvolvimento do ecossistema:

* [x] Planejamento estratégico e Engenharia de Requisitos do sistema
* [x] Definição clara de escopo, objetivos do TCC e documentação teórica
* [x] Modelagem física do banco de dados SQL e arquitetura estrutural do Docker
* [x] Desenvolvimento de layouts responsivos para a experiência do usuário (Front-end)
* [x] Implementação de algoritmos de filtragem alimentar e regras do carrinho de compras
* [x] Construção do painel analítico da área administrativa com relatórios integrados
* [x] Integração lógica com módulos simulados de delivery e cálculo de frete
* [x] Execução de rotinas de testes de software e ajustes visuais finos
* [x] Configuração da infraestrutura de deploy contínuo em Cloud Computing

---

## 🌐 Link de Acesso ao Sistema

A plataforma encontra-se publicada de forma oficial na nuvem (Microsoft Azure) e está acessível publicamente para simulações e homologação por meio do link de produção abaixo:

> 🖥️ **Acesse a Aplicação Online:** http://57.156.66.64
