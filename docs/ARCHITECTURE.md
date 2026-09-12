# Finance Pro AI — Arquitectura

## Visão geral

Finance Pro AI é uma aplicação Laravel monolítica modular. A interface é construída principalmente com Livewire/Volt e a aplicação usa middleware para autenticação, verificação, plano, workspace e administração.

## Camadas

### HTTP / routing

`routes/web.php` organiza:

- páginas públicas;
- autenticação e verificação;
- área pessoal;
- módulos premium;
- área Business;
- administração;
- portais externos;
- exports e endpoints de integração.

### Middleware

A fronteira de acesso é feita antes da execução da funcionalidade. Os principais conceitos são:

- `auth`;
- `verified`;
- `plan`;
- `business.workspace`;
- `admin`;
- rate limiting e protecções específicas de verificação/webhooks.

### UI

Livewire/Volt trata a maioria das interacções sem exigir uma SPA separada. Tailwind/Flux fornecem a camada visual e Vite compila os assets.

### Domínio

Models, Actions, Services, Policies e componentes Livewire representam os fluxos de negócio. Dados financeiros críticos são persistidos na base de dados e devem permanecer workspace-scoped.

### Persistência

A aplicação suporta MySQL e PostgreSQL. Sessões, cache e queue usam base de dados por defeito, com Redis disponível para cenários de maior escala.

### Integrações

- Stripe/Cashier: subscrições e pagamentos;
- Resend: email transaccional;
- OpenRouter: IA;
- Strava: integração de fitness;
- WhatsApp: webhook/integração configurável;
- APIs de mercado: dados auxiliares;
- VAPID/web push: notificações.

## Multi-tenancy / workspaces

O conceito de workspace é central para separar dados pessoais e empresariais. A aplicação deve validar o workspace actual no servidor e não confiar apenas num ID enviado pelo browser.

## IA

O fluxo conceptual é:

```text
Pedido do utilizador
      |
      v
Autenticação + workspace + plano + permissões
      |
      v
Contexto determinístico da aplicação
      |
      v
AI Brain / provider externo
      |
      v
Resposta / insight / proposta de acção
      |
      v
Backend valida novamente antes de qualquer escrita
```

A IA não substitui Policies, Middleware ou regras de negócio.

## Assinaturas

O catálogo interno contém os planos Free, Pro e Business. Os Price IDs Stripe são configurados pelo ambiente através de `STRIPE_PRICE_PRO` e `STRIPE_PRICE_BUSINESS`. O webhook de produção deve validar a assinatura com `STRIPE_WEBHOOK_SECRET`.

## Jobs e scheduler

Workloads assíncronos usam a queue configurada. O scheduler executa tarefas agendadas, incluindo rotinas de análise quando configuradas.

Num único container, Supervisor consegue executar web, worker e scheduler. Em escala, estes papéis devem ser separados para evitar duplicação do scheduler e permitir escalabilidade independente.

## Storage

O filesystem local serve para instalações simples. Em infra-estrutura efémera, ficheiros persistentes devem ser armazenados num object storage compatível com S3 ou num persistent disk.

## Segurança arquitectural

- secrets vêm do ambiente;
- CSRF permanece activo excepto para webhooks específicos;
- webhooks usam validação própria;
- rate limits protegem operações sensíveis;
- workspaces limitam o acesso aos dados;
- operações AI de escrita exigem validação server-side;
- Admin e impersonation têm controlos próprios.

## Pontos de escala

Para crescer sem alterar o domínio principal:

1. BD gerida com backups e índices;
2. Redis para cache/queue;
3. object storage;
4. web/worker/scheduler separados;
5. observabilidade e métricas;
6. E2E browser testing.
