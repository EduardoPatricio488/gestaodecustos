# Finance Pro AI — Performance Audit

**Data:** 2026-09-11  
**Scope:** aplicação Laravel, Livewire, base de dados, integrações externas, frontend, produção e segurança/performance.

## Executive summary

The project already contains several meaningful performance improvements: workspace-aware composite indexes, cached dashboard market data, optimized bank-account balance aggregation, frontend event/DOM reductions, and production asset compression/cache headers.

The most important production incident identified during this audit is PostgreSQL incompatibility in the dashboard reporting queries. Render uses PostgreSQL/Neon while the application still contains MySQL `DATE_FORMAT(..., '%Y-%m')` expressions. A PostgreSQL compatibility migration has been added as an immediate production safeguard so existing reports can execute while the application is progressively made driver-aware.

## Priority matrix

### P0 — production correctness

- **Dashboard PostgreSQL date grouping:** Render was failing with `Undefined function: date_format(date, unknown)` in `Dashboard::buildSixMonthSeries()`.
- **Immediate mitigation:** migration `2026_09_11_200000_add_postgresql_date_format_compatibility.php` adds the narrow `%Y-%m` PostgreSQL compatibility function. This preserves MySQL/SQLite behaviour and prevents the production dashboard from failing on the known query.

### P1 — high-impact query volume

- `BancoService::getMonthlyFlow()` performs one income aggregate and one expense aggregate per month. For 12 months this can reach **24 aggregate queries**.
- The preferred permanent optimization is two grouped queries for the complete date range, followed by an in-memory month map.
- `BancoService::getStats()` has historically loaded amount collections into PHP for aggregate calculations. SQL `MAX()`/`SUM()` should be used instead.
- `BancoService::getAvgMonthlyExpense()` should use one date-range `SUM()` rather than one query per month.

### P1 — external integrations

- Dashboard market data is already cached with stale-while-revalidate semantics and short HTTP timeouts.
- External APIs must never be required for the core financial dashboard to render. Failures should degrade to cached/empty market data rather than block the request.

### P1 — Livewire / dashboard

- Avoid expensive database work in every Livewire render cycle.
- Computed properties and cache should be preferred for expensive, read-heavy dashboard calculations.
- Mutable financial values must not receive long-lived global caches without explicit invalidation.

### P2 — frontend

Already improved:

- removed redundant theme application during Livewire navigation;
- removed the global MutationObserver watching `<html>` class changes;
- reduced global form-formatting DOM scans and cached field classification;
- production Nginx gzip and long-lived immutable Vite asset caching are enabled.

Further work should keep JavaScript event delegation and DOM traversal scoped to the relevant component instead of document-wide handlers.

### P2 — database

A performance-index migration is already present and covers the most frequently filtered workspace-scoped financial tables, including expenses, incomes, categories, bank accounts, investments, subscriptions, debts, goals, reminders, fitness activities and social notifications.

Indexes must remain workspace-first so tenant isolation and common filtering patterns benefit from the same composite indexes.

### P2 — exports and lists

- Large exports should stream/chunk data instead of loading complete datasets into PHP memory.
- Tables with potentially unbounded transaction histories should paginate.
- Avoid relationship access inside loops unless eager-loaded or aggregated with SQL.

### P3 — observability

Production performance should eventually expose:

- request duration percentiles;
- slow query count/time;
- external API latency and failure rate;
- queue latency;
- cache hit/miss rate;
- Livewire request duration;
- export duration and memory usage.

Do not log financial payloads, credentials, tokens or unnecessary personal data.

## Production performance budget

Recommended target for normal authenticated pages:

| Metric | Target |
|---|---:|
| HTML/server response | < 500 ms typical |
| Dashboard DB time | < 250 ms typical |
| Dashboard SQL query count | < 30 typical |
| External API blocking time | 0 ms preferred |
| JS/CSS blocking | minimized; hashed Vite assets cached |
| Large financial list | paginated/chunked |

These are engineering targets, not measured guarantees. Real values should be captured in production observability before making claims about absolute latency.

## Verification checklist

Run after the next application-code optimization batch:

```powershell
php artisan optimize:clear
php artisan test
./vendor/bin/pint --test
npm run build
```

Never use `migrate:fresh` against development/production data as part of performance testing.

## Next permanent code optimizations

1. Replace `DATE_FORMAT()` in `Dashboard::buildSixMonthSeries()` with a database-driver-aware expression (`TO_CHAR` for PostgreSQL, `strftime` for SQLite, `DATE_FORMAT` for MySQL).
2. Rewrite `BancoService::getMonthlyFlow()` as two grouped SQL queries.
3. Rewrite `BancoService::getStats()` using SQL aggregates.
4. Rewrite `BancoService::getAvgMonthlyExpense()` using one date-range aggregate.
5. Audit remaining Livewire components for query-in-render patterns and repeated relationship access.
6. Audit exports for chunking/streaming and memory growth.
7. Add production-safe slow-query/request instrumentation.

## Regression rules

- Preserve workspace isolation on every query.
- Preserve existing financial calculations and labels.
- Preserve MySQL local development and PostgreSQL production compatibility.
- Never replace a database column with an Eloquent accessor in SQL (`current_balance` is an accessor; the physical bank column is `balance`).
- Do not introduce global financial caches without invalidation.
- Do not remove existing security/rate-limit/audit controls for the sake of speed.
