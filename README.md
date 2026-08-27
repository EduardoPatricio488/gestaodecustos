# Finance Pro

> **Premium Personal Finance & Business Management SaaS**

Finance Pro é uma plataforma SaaS desenvolvida em Laravel para gestão financeira pessoal e empresarial. O projeto combina controlo de despesas, análise financeira, gestão empresarial, subscrições e funcionalidades baseadas em Inteligência Artificial numa única aplicação web.

O sistema foi desenvolvido com uma arquitetura preparada para funcionar como **SaaS multi-utilizador**, com diferentes planos de subscrição e suporte para utilização em desktop e dispositivos móveis através de PWA.

---

## ✨ Principais Funcionalidades

### 👤 Gestão Financeira Pessoal

* Gestão de receitas e despesas
* Categorias personalizadas
* Dashboard financeiro
* Análise de hábitos de consumo
* Gestão de despesas recorrentes
* Gestão de subscrições
* Objetivos financeiros
* Sistema de XP e níveis
* Área privada para dados financeiros
* Organização de documentos
* Insights financeiros através de IA

### 💼 Gestão Empresarial

* Dashboard empresarial
* Análise de receitas e despesas
* Profit & Loss (P&L)
* Gestão de colaboradores
* Gestão de clientes
* Gestão de projetos
* Análise de rentabilidade
* Gestão de inventário
* Controlo de stock
* Controlo de despesas empresariais
* Ferramentas de gestão para administradores

### 🤖 Inteligência Artificial

O Finance Pro inclui integração com serviços de IA para funcionalidades como:

* Análise de despesas
* Geração de insights financeiros
* Interpretação de dados
* Assistência financeira
* Análise de documentos/recibos, quando configurada

A integração de IA utiliza uma camada de API compatível com fornecedores como **OpenRouter**, permitindo utilizar diferentes modelos de linguagem.

> As funcionalidades de IA requerem configuração das respetivas API keys.

### 💳 Subscrições e Pagamentos

Integração com **Stripe** através do Laravel Cashier.

O sistema suporta diferentes níveis de subscrição, permitindo configurar funcionalidades e acesso de acordo com o plano do utilizador.

Exemplo de estrutura:

* Free
* Plus
* Pro

A configuração dos preços e produtos Stripe é feita através das variáveis de ambiente da aplicação.

### 📱 Progressive Web App

O Finance Pro pode ser instalado como uma **Progressive Web App (PWA)** em dispositivos compatíveis.

A interface foi desenvolvida para proporcionar uma experiência responsiva em:

* Desktop
* Tablet
* Android
* iOS

---

# 🛠️ Stack Tecnológica

| Tecnologia      | Utilização                   |
| --------------- | ---------------------------- |
| PHP 8.3+        | Backend                      |
| Laravel 11      | Framework                    |
| Livewire 3      | Interfaces dinâmicas         |
| Alpine.js       | Interações frontend          |
| Tailwind CSS    | Estilos e UI                 |
| Flux UI         | Componentes de interface     |
| MySQL           | Base de dados                |
| PostgreSQL      | Base de dados                |
| SQLite          | Desenvolvimento/testes       |
| Laravel Cashier | Integração Stripe            |
| Stripe          | Pagamentos e subscrições     |
| OpenRouter      | Integração com modelos de IA |
| Vite            | Build frontend               |
| PWA             | Experiência mobile           |

---

# 🏗️ Arquitetura

O projeto segue a arquitetura padrão do Laravel e está organizado de forma a facilitar manutenção e extensão.

Principais diretórios:

```text
app/
├── Livewire/
├── Models/
├── Services/
├── Providers/
└── ...

database/
├── migrations/
├── seeders/
└── factories/

resources/
├── views/
├── css/
└── js/

routes/
├── web.php
└── ...

public/
└── ...

tests/
└── ...
```

A aplicação utiliza migrations, models, seeders e componentes Livewire para estruturar a lógica e as interfaces do sistema.

---

# 📋 Requisitos

Antes de instalar o projeto, certifique-se de ter:

* PHP 8.3 ou superior
* Composer
* Node.js
* NPM
* Uma base de dados MySQL, PostgreSQL ou SQLite
* Git

Para funcionalidades específicas:

* Conta Stripe para pagamentos
* Stripe CLI para desenvolvimento de webhooks
* API key de um fornecedor de IA, caso pretenda utilizar as funcionalidades de IA

---

# 🚀 Instalação

## 1. Clonar o projeto

```bash
git clone https://github.com/EduardoPatricio488/gestaodecustos.git

cd gestaodecustos
```

## 2. Instalar dependências

```bash
composer install
```

```bash
npm install
```

## 3. Configurar o ambiente

Copie o ficheiro `.env.example`:

### Linux / macOS

```bash
cp .env.example .env
```

### Windows

```bash
copy .env.example .env
```

Depois gere a chave da aplicação:

```bash
php artisan key:generate
```

---

# 🗄️ Base de Dados

Configure as variáveis da base de dados no ficheiro `.env`.

Exemplo com SQLite:

```env
DB_CONNECTION=sqlite
```

Depois execute:

```bash
php artisan migrate
```

Para criar os dados de demonstração:

```bash
php artisan db:seed --class=DemoSeeder
```

> Recomenda-se utilizar dados de demonstração apenas em ambientes de desenvolvimento ou demonstração.

---

# 🎨 Compilar os Assets

Durante o desenvolvimento:

```bash
npm run dev
```

Para produção:

```bash
npm run build
```

---

# ▶️ Executar Localmente

Pode iniciar o servidor Laravel com:

```bash
php artisan serve
```

A aplicação ficará disponível em:

```text
http://127.0.0.1:8000
```

---

# 💳 Configuração Stripe

Para utilizar pagamentos e subscrições, é necessário configurar uma conta Stripe.

No `.env`, configure as credenciais correspondentes:

```env
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
```

Durante o desenvolvimento local, pode utilizar o Stripe CLI para encaminhar os webhooks:

```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

O comando irá fornecer um `whsec_...` que deve ser colocado em:

```env
STRIPE_WEBHOOK_SECRET=
```

> Nunca publique chaves Stripe ou outras credenciais no repositório.

---

# 🤖 Configuração de IA

Para ativar as funcionalidades de Inteligência Artificial, configure a API utilizada pelo projeto no `.env`.

Exemplo:

```env
OPENROUTER_API_KEY=
```

Dependendo da configuração utilizada, podem ser necessários outros parâmetros relacionados com o fornecedor ou modelo de IA.

> As API keys são fornecidas pelo utilizador final e não devem ser incluídas no código-fonte.

---

# 🧪 Testes

Os testes podem ser executados através do PHPUnit/Pest configurado no projeto:

```bash
php artisan test
```

Antes de colocar a aplicação em produção, recomenda-se executar toda a suite de testes e validar manualmente os principais fluxos da aplicação.

---

# 🔐 Segurança

Antes de realizar um deployment em produção:

1. Defina `APP_ENV=production`
2. Defina `APP_DEBUG=false`
3. Utilize HTTPS
4. Configure corretamente a base de dados
5. Configure os webhooks Stripe
6. Proteja todas as API keys
7. Nunca publique o ficheiro `.env`
8. Utilize credenciais diferentes para desenvolvimento e produção
9. Configure corretamente os mecanismos de autenticação e autorização
10. Execute `php artisan optimize`

Exemplo:

```env
APP_ENV=production
APP_DEBUG=false
```

---

# 📦 Deployment

O projeto pode ser adaptado para diferentes ambientes de alojamento compatíveis com aplicações Laravel.

Para um ambiente de produção, devem ser configurados:

* PHP
* Composer
* Base de dados
* Node.js/build dos assets
* Variáveis de ambiente
* HTTPS
* Queue workers, quando aplicável
* Cron scheduler, quando aplicável
* Stripe webhooks
* Serviço de IA, quando utilizado

O processo exato de deployment depende do fornecedor de hosting escolhido.

---

# 📊 Estado Atual do Projeto

O Finance Pro encontra-se numa fase de **MVP avançado / SaaS em desenvolvimento**, com uma base funcional preparada para evolução e comercialização.

### Implementado

* [x] Gestão financeira pessoal
* [x] Gestão de receitas e despesas
* [x] Categorias
* [x] Dashboard financeiro
* [x] Gestão empresarial
* [x] Dashboard empresarial
* [x] Gestão de projetos
* [x] Inventário
* [x] Gestão de colaboradores
* [x] Sistema de subscrições
* [x] Integração Stripe
* [x] Integração com IA
* [x] PWA
* [x] Sistema de planos
* [x] Dados de demonstração

### Em desenvolvimento / Roadmap

* [ ] Análise preditiva avançada
* [ ] Integrações bancárias
* [ ] Aplicação mobile nativa
* [ ] Relatórios PDF avançados
* [ ] Relatórios white-label
* [ ] Mais integrações externas
* [ ] Expansão das funcionalidades de automação

---

# 💰 Modelo de Negócio

O Finance Pro foi concebido para funcionar como um produto **SaaS por subscrição**.

Uma possível estrutura comercial:

| Plano | Modelo                     |
| ----- | -------------------------- |
| Free  | Utilização limitada        |
| Plus  | Funcionalidades adicionais |
| Pro   | Funcionalidades avançadas  |

Os preços, limites e funcionalidades podem ser configurados de acordo com a estratégia comercial do proprietário do produto.

---

# 📸 Demo

> Adicionar aqui o endereço da demonstração online quando o projeto estiver publicado num domínio próprio.

**Live Demo:** `https://your-domain.com`

**Demo Account:** disponibilizada separadamente.

> Não colocar credenciais reais de administrador ou contas pessoais neste README público.

---

# 🧑‍💻 Desenvolvimento

Para contribuir ou continuar o desenvolvimento:

```bash
git checkout -b feature/nova-funcionalidade
```

Faça as alterações necessárias e execute os testes:

```bash
php artisan test
```

Depois compile os assets:

```bash
npm run build
```

---

# 📁 Configuração

As principais configurações da aplicação encontram-se no ficheiro:

```text
.env
```

O projeto inclui:

```text
.env.example
```

que deve servir como base para a configuração do ambiente.

**Nunca faça commit do ficheiro ****`.env`****.**

---

# 📄 Licença

Este projeto é disponibilizado sob uma licença comercial.

Todos os direitos relativos ao código-fonte pertencem ao respetivo proprietário, salvo componentes, bibliotecas ou dependências de terceiros que estejam sujeitos às suas próprias licenças.

As dependências utilizadas pelo projeto permanecem sujeitas às respetivas licenças.

---

# 🛒 Disponibilidade

O **Finance Pro** está disponível para aquisição como projeto SaaS.

A aquisição pode incluir, dependendo do acordo:

* Código-fonte
* Base de dados/migrations
* Documentação
* Configuração de deployment
* Integrações existentes
* Sistema de subscrições
* Configuração inicial
* Transferência do repositório
* Apoio à instalação

Os termos exatos da transferência devem ser definidos entre comprador e vendedor.

---

## 📬 Contacto

Para informações sobre o projeto, aquisição ou colaboração, contactar o proprietário através do canal definido para o efeito.

---

## ⭐ Sobre o Projeto

O Finance Pro foi desenvolvido com foco em criar uma plataforma moderna de gestão financeira capaz de servir tanto utilizadores individuais como pequenas empresas.

A arquitetura Laravel + Livewire permite continuar a expandir o produto com novas integrações, funcionalidades e modelos de negócio.

**Built with Laravel, Livewire and modern web technologies.**
