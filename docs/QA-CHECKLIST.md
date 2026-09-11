# Finance Pro AI — Production QA Checklist

## CI

- [ ] `composer validate --strict`
- [ ] Pint passes
- [ ] Full Laravel test suite passes
- [ ] GitHub Actions is green on the latest commit

## Authentication

- [ ] Registration
- [ ] Login/logout
- [ ] Email verification
- [ ] Password reset
- [ ] Session expiry
- [ ] Passkeys, when configured

## Personal workspace

- [ ] Dashboard loads without external API failure blocking the page
- [ ] Create/edit/delete expense
- [ ] Create/edit/delete income
- [ ] Categories remain workspace-scoped
- [ ] Budgets
- [ ] Goals
- [ ] Debts
- [ ] Investments
- [ ] Subscriptions
- [ ] Bank accounts
- [ ] Imports
- [ ] PDF export
- [ ] Privacy mode
- [ ] Offline queue and online recovery

## Business workspace

- [ ] Onboarding
- [ ] Fiscal configuration
- [ ] Clients
- [ ] Suppliers
- [ ] Invoices
- [ ] Expenses
- [ ] Partial receipts
- [ ] Partial supplier payments
- [ ] Credit notes
- [ ] Bank transactions
- [ ] Reconciliation
- [ ] Cost centres
- [ ] Projects
- [ ] Employees
- [ ] Roles and permissions
- [ ] Audit log
- [ ] Business AI

## Security / tenant isolation

- [ ] User A cannot read User B's workspace records
- [ ] Employee cannot perform manager operations
- [ ] Viewer cannot mutate financial records
- [ ] Workspace IDs are never trusted from client input without server validation
- [ ] AI tools re-check authorization server-side
- [ ] Bank accounts and transactions validate workspace ownership
- [ ] Payment allocations validate workspace ownership

## Browser / responsive QA

- [ ] Desktop 1440px
- [ ] Laptop 1280px
- [ ] Tablet
- [ ] Mobile 390px
- [ ] Dark mode
- [ ] Keyboard navigation
- [ ] Empty states
- [ ] Loading states
- [ ] Error states
- [ ] Long text / large numbers

## Production configuration

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] Production `APP_KEY`
- [ ] Persistent database configured
- [ ] Queue worker configured if queues are enabled
- [ ] Scheduler configured
- [ ] Mail provider configured and verified
- [ ] Stripe webhook configured
- [ ] AI provider configured
- [ ] External market data keys/configuration present where required
- [ ] Storage configured
- [ ] HTTPS enabled
- [ ] Logs and error monitoring available
