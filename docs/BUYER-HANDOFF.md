# Finance Pro AI — Buyer Handoff

Este documento foi escrito para que um novo proprietário consiga assumir o projecto sem depender do proprietário original.

## 1. O produto

Finance Pro AI é um SaaS de gestão financeira pessoal e empresarial, construído em Laravel + Livewire. Centraliza receitas, despesas, orçamentos, objectivos, dívidas, investimentos, subscrições, contas bancárias, relatórios, operações empresariais e funcionalidades de IA.

A área Business acrescenta clientes, fornecedores, facturação, propostas, despesas, pagamentos, notas de crédito, contas bancárias, reconciliação, fluxo de caixa, resultados, projectos, equipa, tarefas, documentos e análise empresarial.

A IA é uma camada de apoio à análise e produtividade. As permissões e os valores financeiros críticos continuam a ser validados/calculados no backend.

## 2. Stack

- PHP 8.3+
- Laravel 13
- Livewire 4 / Volt
- Flux UI
- Tailwind CSS 4
- Vite / Node.js
- MySQL ou PostgreSQL
- Laravel Cashier + Stripe
- Resend para email
- OpenRouter para IA
- Docker + Nginx + PHP-FPM + Supervisor
- Pest + Pint + GitHub Actions

## 3. Instalação local

Requisitos:

- PHP 8.3+
- Composer 2
- Node.js 22+ e npm
- MySQL ou PostgreSQL
- Git

Passos:

```bash
git clone https://github.com/EduardoPatricio488/gestaodecustos.git
cd gestaodecustos
composer install
copy .env.example .env
php artisan key:generate
```

Configure a BD no `.env` e depois:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Para desenvolvimento com Vite/queue:

```bash
composer run dev
```

O projecto usa `database` para sessões/cache/queue por defeito, o que simplifica uma instalação inicial. Em escala, Redis é preferível.

## 4. Dados de demonstração

A seed normal não cria contas administrativas ou palavras-passe conhecidas.

O `DemoSeeder` existe apenas para uma demonstração controlada. Deve ser activado explicitamente com:

```env
DEMO_SEED_ENABLED=true
DEMO_ADMIN_PASSWORD=<password-forte>
DEMO_CEO_PASSWORD=<password-forte>
DEMO_MEMBER_PASSWORD=<password-forte>
```

Depois:

```bash
php artisan db:seed --class=DemoSeeder
```

Nunca executar este seeder numa produção com dados reais sem rever os dados de demonstração e as credenciais.

## 5. Configuração obrigatória

### Aplicação

- `APP_KEY`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://dominio-real`
- `APP_TIMEZONE=Europe/Lisbon`

### Base de dados

Configure `DB_CONNECTION`, host, porta, nome, utilizador e password. PostgreSQL e MySQL são suportados pela aplicação.

### Email

Recomendado em produção:

```env
MAIL_MAILER=resend
RESEND_KEY=...
MAIL_FROM_ADDRESS=um-endereco@dominio-verificado.pt
MAIL_FROM_NAME="Finance Pro AI"
```

O domínio remetente deve estar verificado no fornecedor de email.

### Stripe

Configure chaves do ambiente correcto e os Price IDs:

```env
STRIPE_KEY=...
STRIPE_SECRET=...
STRIPE_WEBHOOK_SECRET=...
STRIPE_PRICE_PRO=...
STRIPE_PRICE_BUSINESS=...
```

Não reutilizar secrets de desenvolvimento. O webhook de produção deve ser HTTPS e usar o secret desse endpoint.

### IA

```env
OPENROUTER_API_KEY=...
AI_MODEL=...
```

A aplicação deve continuar funcional quando serviços externos de IA não estiverem disponíveis; funcionalidades dependentes de IA podem degradar sem substituir os cálculos determinísticos.

### Storage

O disco `public` é local por defeito. Em Render ou noutro ambiente efémero, usar persistent disk ou object storage S3-compatible para ficheiros que devam sobreviver a redeploys.

## 6. Deploy Docker / Render

O `Dockerfile` já faz instalação das dependências PHP, instalação Node e build dos assets. O container usa Nginx + PHP-FPM + Supervisor.

Antes de colocar em produção:

1. Criar a base de dados.
2. Configurar todas as environment variables no provider.
3. Executar migrations como release/pre-deploy command.
4. Criar `storage:link`.
5. Garantir storage persistente/object storage.
6. Fazer `npm run build` através do build Docker.
7. Activar HTTPS e domínio.
8. Configurar worker e scheduler.
9. Testar login, email, Stripe, IA e uploads.

Não colocar secrets no Git.

## 7. Queue e scheduler

O container actual consegue executar worker e scheduler através do Supervisor. Isto é adequado para uma instalação simples.

Em escala horizontal, separar:

- web service;
- queue worker;
- scheduler/cron.

Deve existir apenas uma instância efectiva do scheduler.

## 8. Planos

Os planos actualmente definidos no catálogo são:

| Plano | Preço configurado no catálogo | Escopo |
|---|---:|---|
| Free | 0 € | Funcionalidades base pessoais |
| Pro | 5 € | Funcionalidades pessoais avançadas, IA e funcionalidades premium configuradas |
| Business | 10 € | Funcionalidades empresariais e equipa |

Os preços são valores actualmente configurados no código/seeder e devem ser tratados como configuração comercial, não como promessa contratual. Os Price IDs Stripe são fornecidos por environment variables.

## 9. Permissões

O acesso é composto por autenticação, verificação de email, plano, workspace e papel.

- utilizador autenticado: área pessoal permitida pelo plano;
- Pro/premium: funcionalidades premium protegidas pelo middleware de plano;
- Business: área empresarial protegida pelo plano e pelo workspace;
- funções empresariais: operações adicionais dependem do papel/permissões;
- Admin: área `/admin` protegida pelo middleware administrativo;
- portais: acesso através de fluxos/token próprios, sem assumir que um utilizador normal tem acesso ao portal de terceiros.

A autorização é validada server-side; IDs recebidos do cliente não devem ser considerados prova de propriedade.

## 10. Arquitectura

```text
Browser / PWA
   |
   v
Laravel Routes
   |
   +--> Middleware (auth / verified / plan / workspace / admin)
   |
   +--> Livewire / Volt components
   |
   +--> Services / Actions / Models
   |        |
   |        +--> MySQL / PostgreSQL
   |        +--> Cache / Queue
   |
   +--> Integrations
            +--> Stripe
            +--> Resend
            +--> OpenRouter
            +--> Strava
            +--> WhatsApp
            +--> Market data APIs
```

## 11. Manutenção

Rotina recomendada:

- monitorizar logs e failed jobs;
- aplicar migrations apenas através do processo de release;
- manter backups da BD e testar restauração;
- actualizar dependências com revisão e CI;
- confirmar `APP_DEBUG=false` em produção;
- verificar domínio/email/Stripe após alterações de infraestrutura;
- executar Pint e Pest antes de releases;
- manter o build frontend verde.

## 12. Troubleshooting

### App abre mas sessão falha
Verificar `SESSION_DRIVER`, migrations da BD e tabela de sessões.

### Email não chega
Verificar `MAIL_MAILER=resend`, `RESEND_KEY`, domínio remetente e logs do provider.

### Stripe não activa uma subscrição
Verificar chaves, Price ID, webhook HTTPS, `STRIPE_WEBHOOK_SECRET` e eventos recebidos.

### IA não responde
Verificar `OPENROUTER_API_KEY`, `AI_MODEL`, limites/créditos do provider e logs da aplicação. A falha da IA não deve ser confundida com falha da lógica financeira do backend.

### Uploads desaparecem após deploy
Verificar se o ambiente usa filesystem efémero. Migrar ficheiros importantes para object storage ou persistent disk.

### Jobs não executam
Verificar `QUEUE_CONNECTION`, migrations de jobs e worker activo. Consultar failed jobs.

### Scheduler não executa
Verificar `schedule:work`/cron e garantir que existe apenas um scheduler em produção escalada.

## 13. Limites do produto

Não apresentar o sistema como software de contabilidade de dupla entrada, substituto de contabilista certificado, payroll legal completo ou software oficialmente certificado pela Autoridade Tributária sem a certificação/licenciamento aplicável. As funcionalidades fiscais são de apoio à gestão.
