# Finance Pro AI — Resiliência e Operações

Data: 29/09/2026

## 1. Rate limiting
- Login, 2FA, passkeys, API, IA e endpoints públicos têm limites.
- O AI Copilot também limita pedidos por utilizador/workspace.
- O formulário de contacto limita envios repetidos.

## 2. Limites das APIs
- Limites centralizados em `config/limits.php`.
- `API_RATE_LIMIT`, `PUBLIC_RATE_LIMIT` e `AI_RATE_LIMIT` podem ser ajustados por ambiente.

## 3. Limites de gastos
- A utilização da IA tem um teto mensal de tokens por utilizador/workspace.
- `AI_MONTHLY_TOKEN_LIMIT=100000` é o valor predefinido.
- Administradores podem operar sem este limite.
- Stripe deve continuar a ser a fonte de verdade para pagamentos.

## 4. Tratamento de erros
- Erros 500 e 429 têm respostas públicas genéricas.
- APIs recebem JSON quando apropriado.
- Detalhes técnicos ficam nos logs e não são enviados ao cliente.

## 5. Estados de loading
- A aplicação usa estados Livewire/Alpine existentes.
- Operações longas de IA/importação/exportação devem manter indicador de processamento e impedir nova submissão até terminar.

## 6. Estados vazios
- As páginas de listas incluem estados vazios específicos em vez de listas sem contexto.

## 7. Pedidos falhados
- Integrações externas devolvem mensagens genéricas e registam o erro técnico.
- Operações financeiras não devem ser consideradas concluídas apenas porque a página foi aberta.

## 8. Timeouts
- Timeouts de conexão e execução estão centralizados.
- IA: 5s de conexão / 30s de execução por defeito.
- Outras APIs: 5s / 15s.

## 9. Envios duplicados
- Endpoints sensíveis têm rate limiting.
- Checkouts usam estado pendente + lock transacional.
- Compras da loja são idempotentes por utilizador/produto e checkout Stripe.

## 10. Pagamentos duplicados
- Checkout Stripe valida sessão, metadata, utilizador, valor e moeda.
- A conclusão do checkout é protegida por transação e lock.
- IDs internos de fatura são únicos e não dependem apenas do timestamp.

## 11. Queries
- Consultas críticas usam filtros por workspace/utilizador.
- Listas grandes usam paginação ou `chunkById`.
- Agregações e caches existentes são preservados.

## 12. Índices
Foi adicionada uma migration de índices para consultas frequentes de despesas, receitas, workspaces, IA, logs e acções.

## 13. Paginação
- As listas principais já usam `paginate()`.
- Para datasets grandes, preferir `simplePaginate()` ou `cursorPaginate()` quando não forem necessários números de página.

## 14. Compressão
- Nginx comprime respostas textuais com gzip.
- Assets Vite têm cache de longa duração e nomes com hash.

## 15. Uploads
- Limite Nginx: 10 MB.
- Limite PHP: 10 MB por ficheiro / 12 MB por pedido.
- Os validadores da aplicação continuam a definir limites específicos por funcionalidade.

## 16. Cache
- APIs de mercado, câmbio, catálogo e várias áreas do dashboard usam cache.
- O rate limiting usa o cache configurado pela aplicação.

## 17. Uptime
- `/health` verifica a ligação à base de dados e responde 200/503.
- GitHub Actions verifica produção a cada 15 minutos.
- O endpoint não expõe detalhes internos.

## 18. Logs
- Erros de integrações externas são registados sem devolver payloads sensíveis ao utilizador.
- Não devem ser registadas passwords, tokens, chaves API ou dados financeiros desnecessários.

## 19. Utilizadores em simultâneo
- Rate limiter usa chaves por utilizador/IP.
- Operações críticas usam transações e locks.
- Jobs e backups usam `withoutOverlapping` quando aplicável.

## 20. Restauro de backups
Comandos disponíveis em produção:

```bash
php artisan backup:restore finance-pro-ai-YYYY-MM-DD_HH-mm-ss.sql.gz --confirm
```

O comando só aceita nomes de backup no formato esperado, descarrega do armazenamento privado S3, descomprime localmente e só executa a substituição da base de dados quando `--confirm` é fornecido.

**Procedimento recomendado:** parar operações de escrita, confirmar o backup selecionado, restaurar, executar migrações necessárias e validar login, workspaces, pagamentos e dados antes de voltar a abrir o serviço.
