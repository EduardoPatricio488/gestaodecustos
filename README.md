# Finance Pro

Finance Pro é uma solução SaaS de gestão financeira pessoal e empresarial, desenvolvida em Laravel com Livewire, Tailwind e tecnologias modernas de web app. O produto centraliza gestão de receitas, despesas, categorias, orçamentos, subscrições, relatórios e análise financeira em um único ambiente para utilizadores individuais e equipas.

A plataforma foi concebida para funcionar como produto escalável, com suporte a múltiplos workspaces, planos de subscrição e integrações de pagamento e IA. O objetivo é entregar uma experiência moderna, funcional e pronta para continuar a evoluir como software comercial.

---

## Visão geral

O sistema foi pensado para servir como ferramenta de controlo financeiro para:

- utilizadores pessoais
- famílias e grupos
- pequenas e médias empresas
- equipas com múltiplos workspaces
- clientes que procuram uma solução SaaS de gestão financeira

A aplicação inclui dashboard financeiro, gestão de categorias, relatórios, objetivos, subscrições, trabalho em equipa e módulos de análise com IA.

---

## Funcionalidades principais

### Gestão financeira pessoal

- registo de receitas e despesas
- categorização por hubs e tipos de gasto
- dashboard financeiro com visão geral mensal
- controlo de orçamento por categoria
- objetivos financeiros
- gestão de despesas recorrentes
- histórico e análise de padrões de consumo
- ambiente privado e personalizado por utilizador

### Gestão empresarial

- workspaces empresariais
- gestão de colaboradores e permissões
- dashboard empresarial
- análise de receitas, despesas e rentabilidade
- gestão de projetos
- gestão de clientes e fornecedores
- controlo de stock e inventário
- relatórios e acompanhamento financeiro

### Inteligência artificial

- análise de gastos e insights
- sugestões de melhoria financeira
- apoio em categorização e identificação de padrões
- integração com provedores de IA para suporte analítico

### Pagamentos e subscrições

- suporte a Stripe
- planos por nível de utilização
- gestão de acesso por plano
- flow de upgrade e downgrade
- arquitetura pronta para monetização SaaS

### PWA e UX

- experiência responsiva em desktop e mobile
- estrutura de app web progressiva
- interface moderna com Flux UI e Tailwind
- suporte a instalação em dispositivos móveis

---

## Stack tecnológica

| Tecnologia | Utilização |
| --- | --- |
| PHP 8.3+ | backend |
| Laravel 11 | framework principal |
| Livewire 3 | interfaces reativas |
| Alpine.js | interatividade do frontend |
| Tailwind CSS | styling e design system |
| Flux UI | componentes visuais |
| MySQL / PostgreSQL / SQLite | base de dados |
| Vite | build frontend |
| Stripe | pagamentos e subscrições |
| Laravel Cashier | integração de billing |
| OpenRouter / IA | geração de insights e análise |
| PWA | experiência mobile |

---

## Arquitetura do projeto

A estrutura segue o padrão de aplicações Laravel modernas, dividida por camadas lógicas:

```text
app/
├── Actions/
├── Console/
├── Http/
├── Listeners/
├── Livewire/
├── Mail/
├── Models/
├── Providers/
├── Services/
├── Traits/
└── ...

database/
├── factories/
├── migrations/
├── seeders/
└── ...

resources/
├── css/
├── js/
├── views/
└── ...

routes/
├── web.php
├── auth.php
├── settings.php
└── ...

public/
├── build/
├── pwa/
├── manifest.json
└── ...

tests/
├── Feature/
├── Unit/
└── ...
```

A aplicação combina modelos Eloquent, Livewire components, serviços de domínio e migrations para organizar a lógica de negócio e a interface do utilizador.

---

## Requisitos do sistema

Antes de instalar e executar o projeto, certifique-se de que tem instalado:

- PHP 8.3 ou superior
- Composer
- Node.js
- npm
- Git
- Base de dados MySQL, PostgreSQL ou SQLite

Para funcionalidades específicas:

- conta Stripe para pagamentos e subscrições
- Stripe CLI para webhooks locais
- chave de API de IA para módulos de insights

---

## Instalação local

### 1. Clonar o repositório

```bash
git clone https://github.com/SEU-USUARIO/gestao-de-custos.git
cd gestao-de-custos
```

### 2. Instalar dependências

```bash
composer install
npm install
```

### 3. Configurar o ambiente

Crie o ficheiro de ambiente com base no exemplo:

```bash
cp .env.example .env
```

Se estiver a usar Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

---

## Configuração da base de dados

Edite o ficheiro .env com as configurações da base de dados, por exemplo:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finance_pro
DB_USERNAME=root
DB_PASSWORD=
```

Ou em SQLite:

```env
DB_CONNECTION=sqlite
```

Depois execute as migrations:

```bash
php artisan migrate
```

Se quiser carregar dados de demonstração:

```bash
php artisan db:seed --class=DemoSeeder
```

> Os dados de demonstração são recomendados apenas para ambiente de desenvolvimento ou apresentação.

---

## Compilar assets

Durante o desenvolvimento:

```bash
npm run dev
```

Para produção:

```bash
npm run build
```

---

## Executar a aplicação localmente

```bash
php artisan serve
```

A aplicação fica disponível em:

```text
http://127.0.0.1:8000
```

---

## Stripe e planos

Para habilitar os pagamentos e planos, configure no .env:

```env
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
CASHIER_CURRENCY=eur
```

Em desenvolvimento, é possível usar o Stripe CLI para encaminhar webhooks locais:

```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

O valor retornado em `whsec_...` deve ser guardado em:

```env
STRIPE_WEBHOOK_SECRET=
```

> Nunca submeta chaves reais para controlo de versões.

---

## IA e integração de insights

Para ativar funções de IA e geração de insights, configure a chave do fornecedor no .env:

```env
OPENROUTER_API_KEY=
```

As integrações externas devem estar protegidas e a sua ativação depende do ambiente em que a aplicação está a ser usada.

---

## Segurança e produção

Antes de colocar a aplicação em produção, recomenda-se:

- definir `APP_ENV=production`
- definir `APP_DEBUG=false`
- ativar HTTPS
- proteger o ficheiro .env
- configurar webhooks do Stripe corretamente
- utilizar filas de jobs em produção
- configurar backups da base de dados
- ativar logs e monitorização
- controlar permissões de utilizadores e workspaces

Exemplo:

```env
APP_ENV=production
APP_DEBUG=false
```

---

## Testes

A aplicação inclui suporte a testes com Pest/PHPUnit:

```bash
php artisan test
```

Recomenda-se executar a suite antes de cada release ou entrega comercial.

---

## Deployment

O projeto pode ser implantado em ambientes Laravel compatíveis, como VPS, servidores Linux, Docker e plataformas cloud.

Para produção, deve garantir:

- PHP e Composer instalados
- base de dados configurada
- SSL ativo
- assets compilados
- fila de jobs em execução
- cache e otimização ativados
- Stripe e IA prontas para produção
- monitorização e logs configurados

Exemplo de otimização em produção:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
php artisan migrate --force
```

---

## Estado atual do produto

O projeto encontra-se numa fase de MVP avançado / SaaS funcional com base sólida para continuar a evolução e comercialização.

### Funcionalidades já presentes

- [x] gestão financeira pessoal
- [x] gestão de receitas e despesas
- [x] categorias e hubs
- [x] dashboard financeiro
- [x] gestão empresarial
- [x] workspaces e multi-utilizador
- [x] gestão de projetos e colaboradores
- [x] controlo de subscrições e recorrentes
- [x] integração Stripe
- [x] módulos de IA
- [x] PWA e interface responsiva
- [x] estrutura de planos e monetização

### Roadmap potencial

- [ ] análise preditiva mais avançada
- [ ] integrações bancárias
- [ ] relatórios PDF e exportação mais avançada
- [ ] white-label para clientes
- [ ] app mobile nativa
- [ ] automações e integrações externas adicionais

---

## Modelo de negócio

A aplicação foi concebida para funcionar em modelo SaaS por subscrição, com estrutura típica de planos:

| Plano | Objetivo |
| --- | --- |
| Free | acesso básico |
| Plus | recursos extra e gestão mais completa |
| Pro | gestão avançada, business e IA |

A estrutura de preços e limites pode ser ajustada conforme a estratégia comercial do proprietário.

---

## Demo e demonstração

O produto pode ser apresentado em ambiente de demonstração com dados de exemplo e workspaces preparados para mostrar funcionalidades em funcionamento.

Para uma demonstração profissional, recomenda-se:

- criar um utilizador demo
- preparar um workspace pessoal
- preparar um workspace empresarial
- carregar dados fictícios mas realistas
- validar dashboard, categorias e subscrições
- confirmar que o fluxo de planos funciona corretamente

---

## Licença

Este projeto está disponibilizado sob uma licença comercial.

Todos os direitos sobre o código-fonte pertencem ao proprietário, salvo componentes de terceiros com as respetivas licenças.

---

## Disponibilidade para aquisição

O Finance Pro está preparado para ser apresentado como projeto SaaS funcional, com base tecnológica sólida e boa capacidade de extensão.

A venda pode incluir, conforme acordo:

- código-fonte completo
- documentação técnica
- estrutura de deployment
- integrações existentes
- setup inicial
- transfer do repositório
- apoio de instalação e configuração

---

## Contacto

Para informações sobre o produto, aquisição ou colaboração, utilizar o canal de contacto definido pelo proprietário.

---

## Sobre o projeto

Finance Pro foi desenvolvido para criar uma solução moderna de gestão financeira, com foco em simplicidade, clareza e controlo. A arquitetura em Laravel + Livewire facilita a expansão do produto e a criação de novas funcionalidades sem perder a qualidade de manutenção e escalabilidade.

Built with Laravel, Livewire, Tailwind and modern SaaS architecture.
