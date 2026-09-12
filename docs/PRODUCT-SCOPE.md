# Finance Pro AI — Product Scope

## Posicionamento

**Finance Pro AI** é uma plataforma SaaS de gestão financeira pessoal e empresarial com inteligência artificial.

O produto deve ser apresentado como uma ferramenta de **financial management / finance operations**, não como um substituto de contabilista nem como um ERP contabilístico certificado.

## Planos actualmente configurados

- **Free** — acesso às funcionalidades base pessoais.
- **Pro** — 5 €/mês no catálogo actualmente configurado; inclui funcionalidades pessoais avançadas, IA e funcionalidades premium configuradas.
- **Business** — 10 €/mês no catálogo actualmente configurado; inclui funcionalidades empresariais e equipa.

Os preços são configuração actual e podem ser alterados. Os Price IDs Stripe são injectados através de `STRIPE_PRICE_PRO` e `STRIPE_PRICE_BUSINESS`.

## Finanças pessoais

- receitas e despesas;
- orçamentos;
- objectivos de poupança;
- dívidas;
- investimentos e património;
- subscrições;
- contas bancárias;
- relatórios e exportação;
- análise financeira com IA;
- PWA e fluxos offline suportados pela aplicação;
- calendários, lembretes e funcionalidades familiares.

## Finanças empresariais

- clientes e fornecedores;
- facturação e propostas;
- despesas e aprovações;
- pagamentos e recebimentos parciais;
- notas de crédito;
- fluxo de caixa e P&L;
- contas bancárias;
- reconciliação de movimentos;
- centros de custo;
- projectos e custos;
- stock/inventário;
- equipa, funções e permissões;
- documentos e operações internas;
- análise empresarial com IA;
- portais de terceiros.

## Permissões e isolamento

O acesso é determinado por autenticação, verificação, plano, workspace e papel. Operações sensíveis devem ser validadas no backend. Os dados financeiros devem permanecer isolados pelo workspace actual.

## Limites importantes

Finance Pro AI **não deve ser apresentado** como:

- software de contabilidade de dupla entrada;
- substituto de um contabilista certificado;
- software oficialmente certificado pela Autoridade Tributária sem a certificação/licenciamento aplicável;
- sistema completo de payroll legal;
- motor fiscal completo para todas as jurisdições;
- plataforma bancária com integração directa a todas as instituições financeiras;
- sistema completo de reconciliação contabilística avançada com todas as capacidades de matching/clearing.

As funcionalidades fiscais são de apoio à gestão e devem ser validadas com um profissional competente antes de serem usadas para obrigações oficiais.

## Arquitectura financeira

Os valores críticos devem ser calculados no backend. A IA pode explicar, resumir e recomendar, mas não é a autoridade para permissões nem deve inventar valores financeiros.

Pagamentos, notas de crédito e reconciliações devem respeitar o workspace actual e as permissões do utilizador.

## Evolução futura recomendada

1. E2E browser testing.
2. Reconciliação bancária avançada.
3. Alocações divididas por centro de custo/projecto.
4. Camada contabilística opcional de dupla entrada.
5. Integrações bancárias por fornecedor/open banking.
6. Melhorias adicionais de performance e observabilidade.
