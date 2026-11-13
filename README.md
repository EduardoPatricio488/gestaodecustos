# Finance Pro AI

**Finance Pro AI** é uma plataforma SaaS de gestão financeira pessoal e empresarial com uma camada de inteligência artificial para análise, explicação e produtividade.

O projecto foi estruturado para suportar utilizadores individuais, workspaces empresariais, equipas, permissões, subscrições e integrações externas.

> **Importante:** Finance Pro AI é uma plataforma de gestão financeira e operações empresariais. Não deve ser apresentado como software de contabilidade de dupla entrada, substituto de contabilista certificado, payroll legal completo ou software oficialmente certificado pela Autoridade Tributária sem a certificação/licenciamento aplicável.

## O que o produto faz

### Personal

- receitas e despesas;
- categorias e campos personalizados;
- orçamentos;
- objectivos de poupança;
- dívidas;
- investimentos e património;
- subscrições;
- contas bancárias e posição de caixa;
- importação de movimentos/extractos;
- calendários e lembretes;
- relatórios e exportação PDF;
- previsões e análise financeira;
- modo privacidade;
- PWA e fluxos offline suportados pela aplicação;
- funcionalidades de família/gamificação;
- Finance Connect / área social.

Na atualização de investimentos, os Certificados de Aforro Série E importados do AforroNet são capitalizados apenas nos trimestres vencidos com taxas disponíveis num histórico comunitário recente. Sem capitalização vencida, mantêm o valor guardado; taxas em falta ou indisponíveis impedem a atualização completa.

### Business

- onboarding e configuração empresarial;
- dashboard empresarial;
- clientes e fornecedores;
- facturação e propostas;
- despesas e aprovações;
- pagamentos/recebimentos e notas de crédito;
- contas bancárias e reconciliação;
- fluxo de caixa e resultados/P&L;
- impostos e e-fatura como ferramentas de apoio à gestão;
- centros de custo e projectos;
- stock/inventário;
- equipa, tarefas, calendário, férias e recrutamento;
- documentos;
- messenger e operações internas;
- portais de clientes, fornecedores e banco;
- análise empresarial com IA.

### AI

A camada de IA inclui Copilot, insights, memória, contexto de workspace, ferramentas autorizadas, confirmações para acções de escrita, registo de acções AI, Health Score e análises proactivas.

A IA **não é a autoridade de autorização** e os valores financeiros críticos devem ser calculados/validados no backend. Quando os dados são insuficientes, a aplicação deve indicar a limitação em vez de inventar informação.

## Stack tecnológica

- PHP 8.3+
- Laravel 13
- Livewire 4 / Volt
- Flux UI
- Tailwind CSS 4
- Vite / Node.js
- MySQL ou PostgreSQL
- Laravel Cashier / Stripe
- Resend
- OpenRouter
- Docker / Nginx / PHP-FPM / Supervisor
- Pest / Pint / GitHub Actions

As versões e dependências exactas estão em `composer.json` e `package.json`.

## Arquitectura

```text
Browser / PWA
    |
    v
Laravel Routes
    |
    +--> Middleware: auth / verified / plan / workspace / admin
    |
    +--> Livewire / Volt UI
    |
    +--> Actions / Services / Models
    |        |
    |        +--> MySQL / PostgreSQL
    |        +--> Cache / Queue
    |
    +--> Integrações externas
             +--> Stripe
             +--> Resend
             +--> OpenRouter
             +--> Strava
             +--> WhatsApp
             +--> APIs de mercado
```

A autorização é server-side e os dados financeiros são isolados por workspace.

## Planos actualmente configurados

| Plano | Preço no catálogo | Âmbito |
|---|---:|---|
| Free | 0 € | Funcionalidades base pessoais |
| Pro | 5 € | Funcionalidades pessoais avançadas, IA e funcionalidades premium configuradas |
| Business | 10 € | Gestão empresarial e equipa |

Os preços são configuração actual do produto e podem ser alterados pelo proprietário. Os Price IDs Stripe **não estão hardcoded** no seeder: são fornecidos através de `STRIPE_PRICE_PRO` e `STRIPE_PRICE_BUSINESS`.

## Instalação rápida

### Requisitos

- PHP 8.3+
- Composer 2
- Node.js 22+ / npm
- MySQL ou PostgreSQL
- Git

### Local

```bash
git clone https://github.com/EduardoPatricio488/gestaodecustos.git
cd gestaodecustos
composer install
copy .env.example .env
php artisan key:generate
```

Configure a BD no `.env` e execute:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Para desenvolvimento com servidor, queue, scheduler e Vite (inclui pagamentos salariais automáticos):

```bash
composer run dev
```

Se iniciares apenas `php artisan serve`, inicia também `php artisan schedule:work` noutro terminal para executar as tarefas agendadas.

Para receber emails de teste no MailHog local, inicia o MailHog e configura no `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=
MAIL_PASSWORD=
```

Consulta os emails em http://localhost:8025. Em ambiente `local`, os códigos de verificação são enviados imediatamente, sem depender de um worker; o MailHog não entrega emails a destinatários reais. Em produção mantém-se o envio pela fila configurada.

## Produção

Para produção, configurar no host:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dominio-real
SESSION_SECURE_COOKIE=true
```

Também são necessários, conforme as funcionalidades activadas:

- BD persistente;
- `APP_KEY` único;
- Resend + domínio remetente verificado;
- Stripe LIVE + webhook + Price IDs LIVE;
- OpenRouter;
- storage persistente/object storage para uploads;
- worker de queue;
- scheduler;
- HTTPS;
- backups e monitorização.

O Dockerfile faz o build de produção dos assets. Em Render, as migrations devem ser tratadas como release/pre-deploy operation, e não depender de migrations em background para declarar o serviço pronto.

## Documentação para o comprador

- [`docs/BUYER-HANDOFF.md`](docs/BUYER-HANDOFF.md) — instalação, configuração, deployment, manutenção e troubleshooting.
- [`docs/PRODUCT-SCOPE.md`](docs/PRODUCT-SCOPE.md) — âmbito, limites e evolução.
- [`docs/QA-CHECKLIST.md`](docs/QA-CHECKLIST.md) — checklist de QA e produção.
- [`PRODUCTION-DEPLOYMENT-AUDIT.md`](PRODUCTION-DEPLOYMENT-AUDIT.md) — auditoria de deployment.
- [`SECURITY-AUDIT.md`](SECURITY-AUDIT.md) — auditoria de segurança.
- [`PERFORMANCE-AUDIT.md`](PERFORMANCE-AUDIT.md) — auditoria de performance.
- [`UI-UX-AUDIT.md`](UI-UX-AUDIT.md) — auditoria de UI/UX.

## Seeders

`php artisan db:seed` prepara dados de referência sem criar uma conta administrativa com password conhecida.

Para uma demonstração controlada, existe `DemoSeeder`, mas este deve ser activado explicitamente e nunca usado inadvertidamente em produção. Consulte `docs/BUYER-HANDOFF.md`.

## Email, Stripe e AI

### Email

Produção recomendada:

```env
MAIL_MAILER=resend
RESEND_KEY=...
MAIL_FROM_ADDRESS=um-endereco@dominio-verificado.pt
```

### Stripe

```env
STRIPE_KEY=...
STRIPE_SECRET=...
STRIPE_WEBHOOK_SECRET=...
STRIPE_PRICE_PRO=...
STRIPE_PRICE_BUSINESS=...
```

Nunca colocar secrets no Git.

### AI

```env
OPENROUTER_API_KEY=...
AI_MODEL=...
```

A aplicação deve degradar graciosamente quando uma API externa falha; integrações externas não devem ser tratadas como fonte de verdade dos valores financeiros internos.
O chat do AI Brain usa `openrouter/free`, independentemente de `AI_MODEL`. O diagnóstico CFO tenta primeiro `google/gemma-4-31b-it:free`, com recurso ao encaminhamento gratuito se necessário. Ambos estão sujeitos à disponibilidade e aos limites dos modelos gratuitos.
O diagnóstico pessoal usa um resumo dos registos, não a totalidade da conta. O custo pessoal de subscrições exclui as marcadas como pagas pela empresa. As projeções pelo ritmo diário são apenas cenários, podem incluir despesas pontuais e não acrescentam despesas futuras agendadas. Após alterar registos ou a lógica da análise, é necessário atualizar o relatório para substituir a versão em cache.
O relatório pessoal apresenta resultados, opinião, cenários, pontos fortes, pontos negativos, conselhos e resumo, com pelo menos cinco pontos por tópico, procurando desenvolver a análise em 500 a 700 palavras quando os dados o permitirem. Se faltarem dados, os pontos podem indicar limitações e informações a confirmar.
Se o fornecedor devolver um relatório vazio, interrompido ou claramente inválido, o CFO tenta novamente uma versão mais concisa. Se rejeitar um pedido com HTTP 400, pode ser feita uma tentativa adicional com parâmetros simplificados; no máximo, são três pedidos ao fornecedor por geração, que podem consumir quota. Se não for concluída, o relatório anterior é preservado. As verificações de formato não garantem que a análise ou os valores estejam corretos.
No diagnóstico pessoal, valores monetários e percentagens identificados no texto são comparados com o resumo atual antes de o relatório ser aceite. Se não forem confirmados, a segunda tentativa pode usar referências substituídas por valores do sistema, mantendo a verificação; caso também falhe, o relatório anterior é preservado. Esta verificação é parcial: não valida todos os formatos, relações ou conclusões, pelo que a análise deve ser conferida.
A página CFO apresenta a quota diária de pedidos gratuitos devolvida pelo OpenRouter para a chave configurada, partilhada com outras funcionalidades. Um relatório pode consumir mais de um pedido; outros limites do fornecedor e da aplicação também se aplicam.
No centro de inteligência, os cartões mostram o detalhe da saúde financeira, os alertas por ler e as operações recentes. Os contadores abrangem os últimos 30 alertas ativos e as últimas 20 operações; a poupança do Finance Score pessoal usa as receitas registadas no mês.
Os destaques do diagnóstico reutilizam trechos do relatório pessoal guardado, sem novos pedidos de IA. Os dados persistentes da memória continuam consultáveis e elimináveis nas definições do perfil.

## CI / qualidade

A pipeline GitHub Actions valida Composer, sintaxe PHP, build frontend, `npm audit`, Pint e a suite Laravel/Pest. A validação final deve estar verde antes de uma entrega.

## Estado de auditoria

As correcções desta auditoria reforçaram segurança, isolamento por workspace, autenticação de portais, verificação de contas, integridade de pagamentos, performance de agregações e preparação de produção. A validação final é feita pela pipeline `business-integrity` no GitHub Actions.

## Limites e conformidade de apresentação

Não fazer claims de certificação fiscal, contabilidade oficial, payroll legal completo ou cobertura bancária universal que não estejam efectivamente implementados e certificados. Funcionalidades fiscais e financeiras são ferramentas de apoio à gestão e devem ser validadas profissionalmente antes de obrigações oficiais.

## Licença

MIT. A transferência comercial do projecto deve ser acompanhada pela transferência dos activos, contas externas, domínio, secrets e documentação acordados no contrato de venda.
