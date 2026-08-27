# Stripe e Planos — Checklist

## Objetivo

Garantir que a parte de billing e planos está funcional e pronta para venda.

## Checklist

- [ ] Stripe instalado e configurado
- [ ] chave pública definida
- [ ] chave secreta definida
- [ ] webhook secret definido
- [ ] planos configurados em Stripe
- [ ] preços configurados no .env
- [ ] upgrade de Free para Plus testado
- [ ] upgrade de Free para Pro testado
- [ ] downgrade testado
- [ ] cancelamento testado
- [ ] webhooks a atualizar o plano do utilizador
- [ ] acesso às features premium bloqueado corretamente
- [ ] logs de falha de billing ativos
- [ ] mensagens de erro amigáveis

## Váriaveis esperadas

```env
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
STRIPE_PRICE_PLUS=
STRIPE_PRICE_PRO=
CASHIER_CURRENCY=eur
```

## Observações

Se o sistema usa planos por workspace ou por utilizador, precisa de ficar documentado no README de venda e no setup guide.
