# Dashboard performance

The dashboard now uses Laravel stale-while-revalidate caching for shared market data, notification checks, and workspace-scoped AI insights. Bank-account dashboard widgets also use database-side aggregation and ordering to avoid loading unnecessary rows into PHP.
