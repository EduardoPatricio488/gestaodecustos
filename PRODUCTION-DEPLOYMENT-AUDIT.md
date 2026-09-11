# Finance Pro AI — Production & Deployment Audit

**Data:** 2026-09-12  
**Estado:** auditado no branch `main`

## Resumo executivo

O projeto tem uma base de deployment sólida para Docker/Render, mas **não deve ser considerado 100% buyer-ready sem configuração correcta do ambiente de produção e sem separar responsabilidades de web, queue e scheduler quando houver escala**.

Não foram encontrados secrets reais no repositório através das verificações direccionadas realizadas (`sk_live_`, `whsec_`, `sk-or-v1-`). O `.gitignore` exclui `.env`, `.env.production`, logs, `vendor`, `node_modules` e `public/storage`.

Foi corrigido o template `.env.example`, que tinha defaults excessivamente ligados ao ambiente local e SMTP/SAPO como configuração principal, apesar de o projecto suportar Resend. O template agora documenta explicitamente a diferença entre local e produção e inclui `RESEND_KEY`.

## Findings

### P0 — Bloqueador de produção

- **Configuração de produção tem de ser injectada no host, nunca no Git.** `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`, `APP_KEY` único e secrets de produção são obrigatórios.
- **Não usar chaves Stripe de teste em produção.** O `.env.example` continua com valores `pk_test_`/`sk_test_` apenas como exemplo local.
- **O endpoint Stripe de produção deve ter o seu próprio webhook secret.**

### P1 — Importante

- O Docker actual executa migrations/config cache em background no arranque. Isto permite que o processo web comece antes de as migrations terminarem. Para ambientes com deploy controlado, é preferível executar `php artisan migrate --force` como pre-deploy/release command e deixar o container apenas arrancar a aplicação.
- Queue e scheduler estão no mesmo container através de Supervisor. Funciona num serviço único, mas ao escalar horizontalmente pode criar vários schedulers e múltiplos workers. Separar `web`, `worker` e `scheduler` é a arquitectura recomendada para escala.
- `FILESYSTEM_DISK=public` usa armazenamento local. Em Render, o filesystem local não deve ser tratado como armazenamento persistente de uploads sem persistent disk ou object storage (S3-compatible). Para ficheiros importantes do utilizador, usar object storage.
- `storage:link` é necessário quando o disco `public` é utilizado. Deve fazer parte do release/deploy ou ser criado no arranque se não existir.
- Cache e queue em `database` são funcionais e simples para um serviço único, mas Redis é preferível quando o volume cresce.

### P2 — Hardening / operação

- `SESSION_SECURE_COOKIE=true` em produção HTTPS.
- Manter `SESSION_HTTP_ONLY=true` e `SESSION_SAME_SITE=lax` salvo necessidade funcional específica.
- Não activar HMR em produção.
- `npm run build` deve ser obrigatório antes de qualquer release.
- `php artisan config:cache`, `route:cache` e `view:cache` devem ser executados como parte do release, depois de todas as variáveis de ambiente estarem disponíveis.
- Logs devem ser enviados para stdout/stderr no container e monitorizados pelo provider.
- Backups automáticos da BD devem ser responsabilidade da BD gerida; testar restauração periodicamente.

## Componentes auditados

### Laravel

- `config/app.php`: defaults seguros (`APP_ENV=production`, `APP_DEBUG=false`) quando as variáveis não estão definidas. fileciteturn132file0
- `config/database.php`: suporta MySQL e PostgreSQL, com strict mode activo. fileciteturn128file0
- `config/session.php`: database sessions, HttpOnly e SameSite Lax. fileciteturn135file0
- `config/cache.php`: database cache disponível e Redis configurado. fileciteturn137file0
- `config/queue.php`: database queue, Redis e failed jobs configurados. fileciteturn136file0
- `config/filesystems.php`: local/public/S3 disponíveis e `storage:link` configurado. fileciteturn134file0
- `config/services.php`: Stripe, Resend, OpenRouter, Strava, WhatsApp e market APIs dependem de environment variables. fileciteturn148file0

### Environment

O template foi corrigido para:

- não conter secrets reais;
- documentar explicitamente local vs produção;
- suportar Resend através de `RESEND_KEY`;
- exigir HTTPS/secure cookie em produção;
- manter defaults locais compatíveis com Laragon/MySQL.

### Docker

O Dockerfile:

- usa PHP 8.3 Alpine;
- instala PDO MySQL/PostgreSQL;
- instala Composer sem dev dependencies;
- executa `npm ci` e `npm run build`;
- configura permissões de `storage`/`bootstrap/cache`;
- usa Nginx + PHP-FPM + Supervisor. fileciteturn129file0

O Nginx aplica `X-Frame-Options`, `X-Content-Type-Options`, gzip e cache agressivo para `/build`. fileciteturn142file0

### Queue / Scheduler

Supervisor arranca PHP-FPM, Nginx, queue worker e scheduler. fileciteturn131file0

Isto é aceitável para uma instância única, mas não deve ser duplicado indiscriminadamente em múltiplas réplicas.

### Vite

O build de produção está correctamente integrado no Docker. O `vite.config.js` mantém servidor/HMR apenas como configuração de desenvolvimento; `npm run build` gera os assets finais. fileciteturn141file0

## Checklist de entrega

### Local

- [ ] `.env` criado a partir de `.env.example`
- [ ] `APP_ENV=local`
- [ ] `APP_DEBUG=true`
- [ ] MySQL local funcional
- [ ] migrations executadas
- [ ] `php artisan storage:link`
- [ ] `npm run dev` / `npm run build` funcional
- [ ] testes Laravel/Pest executados
- [ ] Pint executado

### Staging

- [ ] `APP_ENV=staging` ou equivalente
- [ ] `APP_DEBUG=false`
- [ ] HTTPS
- [ ] BD separada da produção
- [ ] Stripe TEST
- [ ] Resend com domínio de staging/teste
- [ ] secrets injectados pelo provider
- [ ] queue worker activo
- [ ] scheduler activo
- [ ] storage persistente/object storage
- [ ] smoke tests após deploy

### Production

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://dominio-real`
- [ ] `APP_KEY` único e guardado fora do Git
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_HTTP_ONLY=true`
- [ ] `SESSION_SAME_SITE=lax`
- [ ] BD de produção gerida
- [ ] backups + restore testado
- [ ] migrations controladas
- [ ] logs/monitorização activos

### Render

- [ ] Docker deploy confirmado
- [ ] `PORT` fornecida pelo Render
- [ ] Environment variables configuradas no serviço
- [ ] DB connection configurada para BD de produção
- [ ] health endpoint `/up` disponível
- [ ] custom domain + HTTPS
- [ ] deploy automático apenas de branch protegida/reviewed
- [ ] não depender do filesystem efémero para uploads importantes
- [ ] considerar serviços separados para worker/scheduler quando houver escala

### Database

- [ ] migrations aplicadas
- [ ] sem `migrate:fresh` em produção
- [ ] backups automáticos
- [ ] SSL/TLS quando suportado pelo provider
- [ ] índices verificados
- [ ] credentials exclusivas de produção

### Email / Resend

- [ ] `MAIL_MAILER=resend`
- [ ] `RESEND_KEY` configurado no host
- [ ] domínio remetente verificado
- [ ] SPF/DKIM/DMARC configurados
- [ ] `MAIL_FROM_ADDRESS` num domínio verificado
- [ ] teste de email após deploy
- [ ] falhas de email não expõem secrets nem stack traces

### Stripe

- [ ] `STRIPE_KEY` LIVE
- [ ] `STRIPE_SECRET` LIVE
- [ ] `STRIPE_WEBHOOK_SECRET` do endpoint LIVE
- [ ] price IDs LIVE
- [ ] webhook HTTPS
- [ ] assinatura do webhook validada
- [ ] idempotência testada
- [ ] checkout/replay/cancelamento testados

### Queue

- [ ] `QUEUE_CONNECTION=database` ou Redis
- [ ] migrations de jobs aplicadas
- [ ] worker activo
- [ ] failed jobs monitorizados
- [ ] retry/backoff definidos
- [ ] não usar `sync` para workloads de produção que dependam de processamento assíncrono

### Scheduler

- [ ] scheduler activo
- [ ] apenas uma instância de scheduler em escala horizontal
- [ ] jobs agendados verificados
- [ ] logs de execução monitorizados

### Storage

- [ ] `php artisan storage:link`
- [ ] uploads com validação de tipo/tamanho
- [ ] ficheiros financeiros importantes em object storage/persistent disk
- [ ] não depender de `/storage` local efémero em Render
- [ ] backups/retenção definidos

### Security

- [ ] zero secrets no Git
- [ ] `APP_DEBUG=false`
- [ ] HTTPS obrigatório
- [ ] secure cookies
- [ ] CSRF activo salvo webhooks explicitamente excluídos
- [ ] rate limiting activo
- [ ] Stripe/WhatsApp webhook signatures/secret validation
- [ ] logs sem passwords/tokens/PII desnecessária
- [ ] roles/workspaces testados
- [ ] dependências auditadas
- [ ] CI verde antes do release

## Conclusão

A aplicação tem uma base de deployment profissional e um Docker funcional, mas **a configuração final de produção pertence ao ambiente do comprador/hosting e não deve ser gravada no repositório**.

A correcção aplicada neste audit foi tornar o `.env.example` mais seguro e coerente com a arquitectura real. Os restantes pontos são sobretudo decisões operacionais de produção: secrets, domínio, BD, storage persistente, workers e scheduler.

**Não foi afirmado que um deploy real foi executado neste audit.** O GitHub connector permite inspeccionar e alterar o repositório, mas não substitui uma execução real no Render com as secrets/serviços do comprador.