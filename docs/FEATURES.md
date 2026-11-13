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

Na configuração inicial e no módulo Banco é possível importar comprovativos de IBAN português em PDF com texto legível (até 5 MB). A leitura preenche os dados identificados; é necessário confirmá-los e introduzir o saldo antes de guardar.

Na configuração inicial, o formulário de salário é o mesmo do módulo Receitas, incluindo a estimativa fiscal e o scanner de contratos por fotografia. A análise envia a imagem ao OpenRouter e os dados detetados devem ser revistos antes de guardar.

A configuração inicial inclui ainda passos opcionais para outras fontes de rendimento, investimentos, importação de extratos e assinaturas, com os formulários dos respetivos módulos. Os extratos são pré-visualizados e os movimentos selecionados antes da confirmação.

No passo de investimentos, é também possível analisar e importar relatórios XTB (Excel ou CSV) e extratos AforroNet em PDF, revendo as posições antes da confirmação. As posições XTB com identificador são guardadas individualmente, mesmo quando partilham o mesmo símbolo. Se o OpenRouter estiver configurado, a análise XTB envia um resumo das posições e respetivos valores para validação por IA.

As posições XTB guardadas podem ser expandidas, editadas ou eliminadas nesse passo. Uma nova importação pode atualizar os valores editados ou voltar a criar uma posição eliminada.

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
- O CFO pessoal inclui a secção Valores vs Idade/País. A referência salarial usa o país de trabalho e a faixa etária, quando disponíveis. Fonte: [Eurostat, EARN_SES22_20 — remuneração mensal bruta média](https://ec.europa.eu/eurostat/databrowser/product/page/EARN_SES22_20), dados do inquérito de 2022, descarregados em 13/11/2026 e filtrados para setores B-S exceto administração pública, empresas com pelo menos 10 trabalhadores e ambos os sexos. Os valores históricos incluem trabalhadores a tempo inteiro e parcial, são apresentados em euros e não representam custo de vida. Não há referência para todos os países; a comparação com o salário base bruto atual é apenas indicativa, não equivalendo ao salário total, e não deve ser feita com valores líquidos.

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
