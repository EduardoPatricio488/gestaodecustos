# Production Readiness Checklist

## Security

- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] APP_KEY configurada
- [ ] HTTPS ativo
- [ ] .env fora do repositório em produção
- [ ] permissões de pastas corretas
- [ ] sessões seguras
- [ ] CSRF ativo
- [ ] proteção de rota por middleware

## Database

- [ ] migrations executadas
- [ ] seeders apenas em ambiente de desenvolvimento
- [ ] conexões corretas
- [ ] backups configurados
- [ ] índice de performance revisado

## Payments

- [ ] Stripe testado em sandbox
- [ ] webhooks configurados e validados
- [ ] planos de assinatura ativos
- [ ] cancelamento e upgrade testados
- [ ] alertas de falha configurados

## UX / Frontend

- [ ] layout sem quebra em desktop
- [ ] layout sem quebra em mobile
- [ ] imagens e assets carregam corretamente
- [ ] no console não há erros visíveis
- [ ] theme e dashboard funcionam

## AI / External APIs

- [ ] OpenRouter/AI key configurada
- [ ] fallback quando a API falhar
- [ ] timeouts definidos
- [ ] logs de erro ativos

## Operations

- [ ] fila configurada
- [ ] queue worker ativo
- [ ] jobs reproduzíveis e monitorizados
- [ ] logs de produção configurados
- [ ] monitorização e alertas

## Release ready

- [ ] README final atualizado
- [ ] demo account pronta
- [ ] landing page finalizada
- [ ] screenshots finais coletados
- [ ] checklist de what’s included pronto
- [ ] documentos de vendas organizados
