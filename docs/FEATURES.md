# Finance Pro AI — Funcionalidades

Este documento descreve apenas funcionalidades identificadas no projecto actual.

## Personal

- Dashboard financeiro
- Receitas e despesas
- Categorias e campos específicos por categoria
- Orçamentos
- Objectivos
- Dívidas
- Investimentos
- Património
- Subscrições
- Contas bancárias
- Importação de extractos
- Calendário
- Lembretes
- Relatórios
- Exportação PDF
- Previsão de despesas
- Detecção/análise de anomalias
- Simulador de reforma
- Inflação
- Gestão familiar e ranking
- Gamificação
- Fitness e integração Strava configurável
- PWA/offline para fluxos suportados

## Business

- Onboarding empresarial
- Dashboard empresarial
- Clientes
- Fornecedores
- Facturação
- Propostas
- Despesas e aprovações
- Pagamentos e recebimentos
- Notas de crédito
- Contas bancárias
- Reconciliação
- Fluxo de caixa
- Resultados/P&L
- Impostos e e-fatura como apoio à gestão
- Centros de custo
- Projectos e custos de projecto
- Stock/inventário
- Equipa e funções
- Tarefas e timeline
- Calendário empresarial
- Férias/ausências
- Recrutamento
- Documentos
- Messenger
- Portais de cliente, fornecedor e banco
- IA empresarial

## AI

- AI Copilot
- AI Insights
- AI Control Center
- memória AI separada do histórico
- contexto por utilizador/workspace/plano/papel
- ferramentas AI autorizadas no backend
- confirmações antes de acções de escrita
- logs de acções AI
- insights proactivos
- Health Score
- feedback de insights
- revisões agendadas determinísticas

## Subscrições e loja

- catálogo de planos;
- checkout Stripe;
- gestão de subscrições;
- loja/marketplace;
- carrinho, favoritos e comparação;
- compras e downloads protegidos;
- produtos e entitlements.

## Admin

- dashboard;
- gestão de planos;
- analytics;
- AI monitor;
- produtividade;
- monitor de lembretes;
- utilizadores;
- billing;
- suporte;
- comunicação;
- gamificação;
- loja;
- logs;
- configurações;
- impersonation controlada.

## Finance Connect

A área social inclui feed, perfis, interacções sociais e mecanismos de comunidade. O conteúdo social é separado dos dados financeiros e do histórico do AI Copilot.

## Integrações

| Integração | Finalidade | Configuração |
|---|---|---|
| Stripe | Subscrições/checkout/webhooks | Obrigatória para billing pago |
| Resend | Email | Recomendada em produção |
| OpenRouter | IA | Necessária para funcionalidades AI dependentes do provider |
| Strava | Fitness | Opcional |
| WhatsApp | Webhook/integração | Opcional |
| Yahoo / Alpha Vantage | Dados de mercado | Auxiliares |
| VAPID | Push notifications | Opcional |

## Limites

As funcionalidades apresentadas não significam que o produto seja uma plataforma bancária universal, sistema contabilístico de dupla entrada, payroll legal completo ou software fiscal certificado. Essas capacidades devem ser adicionadas/certificadas separadamente quando aplicável.
