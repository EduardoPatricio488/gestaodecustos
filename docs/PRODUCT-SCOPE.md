# Finance Pro AI — Product Scope

## Posicionamento

**Finance Pro AI** é uma plataforma SaaS de gestão financeira pessoal e empresarial com inteligência artificial.

O produto deve ser apresentado como uma ferramenta de **finance operations / financial management**, não como um substituto de contabilista ou como um ERP contabilístico certificado.

## O que o produto faz

### Finanças pessoais
- receitas e despesas;
- orçamentos;
- objectivos de poupança;
- dívidas;
- investimentos;
- subscrições;
- contas bancárias;
- relatórios e exportação;
- análise financeira com IA;
- PWA e suporte a utilização offline.

### Finanças empresariais
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
- equipa, funções e permissões;
- documentos e operações internas;
- IA financeira empresarial.

## Limites importantes

Finance Pro AI **não deve ser apresentado** como:

- software de contabilidade de dupla entrada;
- substituto de um contabilista certificado;
- software oficialmente certificado pela Autoridade Tributária sem a certificação/licenciamento aplicável;
- sistema completo de payroll legal;
- motor fiscal completo para todas as jurisdições;
- plataforma bancária com integração directa a todas as instituições financeiras;
- sistema completo de reconciliação contabilística com extractos, clearing accounts e matching avançado.

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
