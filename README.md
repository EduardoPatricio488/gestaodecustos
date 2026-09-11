# Finance Pro AI

Finance Pro AI é uma solução SaaS de gestão financeira pessoal e empresarial, desenvolvida em Laravel com Livewire, Tailwind e tecnologias modernas de web app. O produto centraliza receitas, despesas, categorias, orçamentos, subscrições, relatórios, operações empresariais e análise financeira num único ambiente para utilizadores individuais e equipas.

## AI Intelligence Layer

A plataforma inclui uma camada central de inteligência financeira com:

- contexto seguro por utilizador, workspace, papel, plano e página;
- cálculos financeiros determinísticos no backend antes de qualquer análise generativa;
- AI Copilot global persistente;
- histórico de conversas separado dos chats sociais;
- memória da IA separada do histórico, com eliminação pelo utilizador;
- ferramentas AI com autorização server-side e fronteira de workspace;
- confirmação explícita antes de ações de escrita;
- registo de auditoria das ações AI;
- proteção contra prompt injection e contra invenção de dados;
- insights proativos com prioridade, confiança, score, deduplicação e deep links;
- AI Observer assíncrono e revisões semanais/mensais determinísticas;
- Health Score pessoal e empresarial explicável;
- AI Insight Center / Action Center;
- feedback de insights;
- contexto Bunker Offline que distingue estado online/offline e dados pendentes;
- análise empresarial encaminhada pelo mesmo AI Brain central;
- respostas e relatórios em Português de Portugal.

## Segurança

A IA nunca é a autoridade de autorização. O backend valida sempre o utilizador, workspace e permissões antes de ler ou alterar dados. Operações destrutivas não são executadas diretamente pelo modelo: são primeiro apresentadas ao utilizador e só depois executadas através do backend.

Os dados financeiros e empresariais são isolados por workspace e as operações sensíveis são novamente validadas no servidor.

## Dados e confiança

As respostas devem distinguir factos provenientes da aplicação, inferências e recomendações. Valores financeiros críticos são calculados pelo backend. Quando os dados não são suficientes, a aplicação deve indicar essa limitação em vez de inventar informação.

## Business Finance

A área empresarial inclui clientes, fornecedores, facturação, despesas, pagamentos, notas de crédito, contas bancárias, reconciliação de movimentos, fluxo de caixa, P&L, centros de custo, projectos, equipa, permissões, documentos e análise por IA.

### Posicionamento fiscal e contabilístico

Finance Pro AI é uma plataforma de **gestão financeira e operações empresariais**. Não deve ser apresentada como software de contabilidade de dupla entrada, substituto de contabilista certificado, payroll legal completo ou software oficialmente certificado pela Autoridade Tributária sem a certificação/licenciamento aplicável.

As funcionalidades fiscais são de apoio à gestão e devem ser validadas com um profissional competente antes de serem usadas para obrigações oficiais.

## Testing / CI

O projecto inclui testes de integridade financeira, autorização, métricas e settlement, além de uma pipeline GitHub Actions para:

- validação do Composer;
- syntax check de PHP;
- Pint;
- suite Laravel completa.

A pipeline deve estar verde no commit final antes de uma entrega de produção.

Consulte `docs/QA-CHECKLIST.md` para o checklist de lançamento e `docs/PRODUCT-SCOPE.md` para o âmbito e limitações do produto.

## Performance

Funcionalidades externas, como dados de mercado, devem ser tratadas como dados auxiliares e não como dependências críticas do dashboard. Cache, timeouts e degradação graciosa são preferíveis a bloquear a experiência principal.

## Agendamento

O AI Observer pode analisar workspaces através da fila existente. Existem também revisões semanais e mensais calculadas deterministicamente sem consumir tokens de um modelo generativo.

<!-- modal-audit-trigger -->
