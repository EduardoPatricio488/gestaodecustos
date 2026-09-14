# Finance Pro AI — Modal Audit

Generated automatically from the repository source.

## Scope

Scanned all Blade views under `resources/views` for Flux modals, ARIA/HTML dialogs and modal-specific Alpine/Livewire patterns.

## Inventory

- Blade files scanned: **675**
- Files containing modal/dialog patterns: **47**
- `flux:modal` occurrences: **258**
- `role="dialog"` / `role='dialog'` occurrences: **4**
- `<dialog>` occurrences: **1**
- Modal event references: **51**

## Global corrections applied

- Added a single global modal behaviour layer for Flux, Livewire and Alpine dialogs.
- Standardized a 40x40px minimum close target with `aria-label="Fechar"`.
- Added Escape-to-close for the topmost visible dialog.
- Added body scroll locking while a dialog is open.
- Added focus management and basic keyboard focus trapping.
- Added automatic dialog semantics (`role="dialog"`, `aria-modal="true"`, title association when possible).
- Added automatic close-button fallback when a visible dialog has no detectable top-right close control.
- Added mobile-safe close-button sizing and visible focus states.
- Kept existing modal state/business logic intact; the global layer delegates closing to the modal’s existing close action whenever available.

## Modal inventory

| Ficheiro | Linha | Mecanismo |
|---|---:|---|
| `resources/views/components/layouts/app.blade.php` | 596 | modal event |
| `resources/views/components/layouts/app.blade.php` | 1806 | Flux modal |
| `resources/views/components/layouts/app.blade.php` | 1835 | Flux modal |
| `resources/views/components/layouts/app.blade.php` | 1848 | modal event |
| `resources/views/components/layouts/app.blade.php` | 1852 | Flux modal |
| `resources/views/components/layouts/app.blade.php` | 1856 | Flux modal |
| `resources/views/components/layouts/app.blade.php` | 1861 | Flux modal |
| `resources/views/components/layouts/app.blade.php` | 1892 | Flux modal, Livewire state |
| `resources/views/components/layouts/app.blade.php` | 1900 | Flux modal |
| `resources/views/components/modal.blade.php` | 47 | ARIA dialog |
| `resources/views/flux/modal/close.blade.php` | 3 | modal event |
| `resources/views/flux/modal/index.blade.php` | 111 | HTML dialog |
| `resources/views/flux/modal/index.blade.php` | 120 | modal event |
| `resources/views/flux/modal/index.blade.php` | 121 | modal event |
| `resources/views/flux/modal/index.blade.php` | 130 | Flux modal |
| `resources/views/flux/modal/index.blade.php` | 132 | Flux modal |
| `resources/views/flux/modal/index.blade.php` | 142 | Flux modal |
| `resources/views/flux/modal/index.blade.php` | 144 | Flux modal |
| `resources/views/flux/modal/trigger.blade.php` | 11 | modal event |
| `resources/views/flux/modal/trigger.blade.php` | 13 | modal event |
| `resources/views/livewire/admin/analytics-hub.blade.php` | 190 | Flux modal |
| `resources/views/livewire/admin/analytics-hub.blade.php` | 341 | Flux modal |
| `resources/views/livewire/admin/analytics-hub.blade.php` | 343 | Flux modal |
| `resources/views/livewire/admin/analytics-hub.blade.php` | 349 | Flux modal |
| `resources/views/livewire/admin/gamification-hub.blade.php` | 85 | Flux modal |
| `resources/views/livewire/admin/gamification-hub.blade.php` | 87 | Flux modal |
| `resources/views/livewire/admin/gamification-hub.blade.php` | 100 | Flux modal |
| `resources/views/livewire/admin/gamification-hub.blade.php` | 143 | Flux modal |
| `resources/views/livewire/admin/gamification-hub.blade.php` | 146 | Flux modal |
| `resources/views/livewire/admin/global-logs.blade.php` | 114 | Flux modal |
| `resources/views/livewire/admin/global-logs.blade.php` | 150 | Flux modal |
| `resources/views/livewire/admin/global-logs.blade.php` | 154 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 71 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 75 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 89 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 93 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 114 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 116 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 125 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 132 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 135 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 137 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 147 | Flux modal |
| `resources/views/livewire/admin/site-settings.blade.php` | 150 | Flux modal |
| `resources/views/livewire/admin/user-management.blade.php` | 308 | Flux modal |
| `resources/views/livewire/admin/user-management.blade.php` | 333 | Flux modal |
| `resources/views/livewire/admin/user-management.blade.php` | 339 | Flux modal |
| `resources/views/livewire/business/absence-hub.blade.php` | 22 | Flux modal |
| `resources/views/livewire/business/absence-hub.blade.php` | 26 | Flux modal |
| `resources/views/livewire/business/absence-hub.blade.php` | 129 | Flux modal |
| `resources/views/livewire/business/absence-hub.blade.php` | 167 | Flux modal |
| `resources/views/livewire/business/absence-hub.blade.php` | 173 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 25 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 50 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 89 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 91 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 99 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 101 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 104 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 105 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 106 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 109 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 111 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 124 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 127 | Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 131 | Alpine modal, Flux modal |
| `resources/views/livewire/business/bank-account-hub.blade.php` | 133 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 28 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 32 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 198 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 212 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 214 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 277 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 279 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 285 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 288 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 291 | modal event |
| `resources/views/livewire/business/client-hub.blade.php` | 350 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 352 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 365 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 368 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 379 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 381 | Flux modal |
| `resources/views/livewire/business/client-hub.blade.php` | 421 | Flux modal |
| `resources/views/livewire/business/collaborator-expense-hub.blade.php` | 17 | modal event |
| `resources/views/livewire/business/collaborator-expense-hub.blade.php` | 208 | Flux modal |
| `resources/views/livewire/business/collaborator-expense-hub.blade.php` | 281 | Flux modal |
| `resources/views/livewire/business/collaborator-expense-hub.blade.php` | 283 | Flux modal |
| `resources/views/livewire/business/collaborator-expense-hub.blade.php` | 289 | Flux modal |
| `resources/views/livewire/business/document-vault.blade.php` | 33 | Flux modal |
| `resources/views/livewire/business/document-vault.blade.php` | 37 | Flux modal |
| `resources/views/livewire/business/document-vault.blade.php` | 172 | Flux modal |
| `resources/views/livewire/business/document-vault.blade.php` | 177 | Flux modal |
| `resources/views/livewire/business/document-vault.blade.php` | 179 | Flux modal |
| `resources/views/livewire/business/document-vault.blade.php` | 268 | Flux modal |
| `resources/views/livewire/business/document-vault.blade.php` | 270 | Flux modal |
| `resources/views/livewire/business/document-vault.blade.php` | 284 | Flux modal |
| `resources/views/livewire/business/inventory-hub.blade.php` | 41 | Flux modal |
| `resources/views/livewire/business/inventory-hub.blade.php` | 48 | Flux modal |
| `resources/views/livewire/business/inventory-hub.blade.php` | 273 | Flux modal |
| `resources/views/livewire/business/inventory-hub.blade.php` | 278 | Flux modal |
| `resources/views/livewire/business/inventory-hub.blade.php` | 280 | Flux modal |
| `resources/views/livewire/business/inventory-hub.blade.php` | 391 | Flux modal |
| `resources/views/livewire/business/inventory-hub.blade.php` | 397 | Flux modal |
| `resources/views/livewire/business/inventory-hub.blade.php` | 407 | Flux modal |
| `resources/views/livewire/business/invoicing-hub.blade.php` | 29 | Flux modal |
| `resources/views/livewire/business/invoicing-hub.blade.php` | 37 | Flux modal |
| `resources/views/livewire/business/my-company-profile.blade.php` | 415 | Flux modal |
| `resources/views/livewire/business/my-company-profile.blade.php` | 419 | Flux modal |
| `resources/views/livewire/business/my-company-profile.blade.php` | 425 | Flux modal |
| `resources/views/livewire/business/my-company-profile.blade.php` | 433 | Flux modal |
| `resources/views/livewire/business/my-company-profile.blade.php` | 437 | Flux modal |
| `resources/views/livewire/business/partials/invoice-modal.blade.php` | 5 | modal event |
| `resources/views/livewire/business/project-hub.blade.php` | 39 | Flux modal |
| `resources/views/livewire/business/project-hub.blade.php` | 44 | Flux modal |
| `resources/views/livewire/business/project-hub.blade.php` | 299 | Flux modal |
| `resources/views/livewire/business/project-hub.blade.php` | 303 | Flux modal |
| `resources/views/livewire/business/project-hub.blade.php` | 305 | Flux modal |
| `resources/views/livewire/business/project-hub.blade.php` | 485 | Flux modal |
| `resources/views/livewire/business/project-hub.blade.php` | 490 | Flux modal |
| `resources/views/livewire/business/project-hub.blade.php` | 499 | Flux modal |
| `resources/views/livewire/business/proposal-hub.blade.php` | 32 | Flux modal |
| `resources/views/livewire/business/proposal-hub.blade.php` | 39 | Flux modal |
| `resources/views/livewire/business/proposal-hub.blade.php` | 314 | Flux modal |
| `resources/views/livewire/business/proposal-hub.blade.php` | 319 | Flux modal |
| `resources/views/livewire/business/proposal-hub.blade.php` | 321 | Flux modal |
| `resources/views/livewire/business/proposal-hub.blade.php` | 441 | Flux modal |
| `resources/views/livewire/business/proposal-hub.blade.php` | 447 | Flux modal |
| `resources/views/livewire/business/proposal-hub.blade.php` | 457 | Flux modal |
| `resources/views/livewire/business/supplier-hub.blade.php` | 28 | Flux modal |
| `resources/views/livewire/business/supplier-hub.blade.php` | 32 | Flux modal |
| `resources/views/livewire/business/supplier-hub.blade.php` | 254 | Flux modal |
| `resources/views/livewire/business/supplier-hub.blade.php` | 259 | modal event |
| `resources/views/livewire/business/supplier-hub.blade.php` | 352 | Flux modal |
| `resources/views/livewire/business/supplier-hub.blade.php` | 354 | Flux modal |
| `resources/views/livewire/business/supplier-hub.blade.php` | 361 | Flux modal |
| `resources/views/livewire/business/supplier-hub.blade.php` | 364 | Flux modal |
| `resources/views/livewire/business/supplier-hub.blade.php` | 367 | modal event |
| `resources/views/livewire/business/supplier-hub.blade.php` | 426 | Flux modal |
| `resources/views/livewire/business/supplier-hub.blade.php` | 428 | Flux modal |
| `resources/views/livewire/business/supplier-hub.blade.php` | 441 | Flux modal |
| `resources/views/livewire/business/task-hub.blade.php` | 32 | Flux modal |
| `resources/views/livewire/business/task-hub.blade.php` | 37 | Flux modal |
| `resources/views/livewire/business/task-hub.blade.php` | 282 | Flux modal |
| `resources/views/livewire/business/task-hub.blade.php` | 288 | Flux modal |
| `resources/views/livewire/business/task-hub.blade.php` | 291 | Flux modal |
| `resources/views/livewire/business/task-hub.blade.php` | 408 | Flux modal |
| `resources/views/livewire/business/task-hub.blade.php` | 413 | Flux modal |
| `resources/views/livewire/business/task-hub.blade.php` | 421 | Flux modal |
| `resources/views/livewire/business/task-timeline.blade.php` | 262 | Flux modal |
| `resources/views/livewire/business/task-timeline.blade.php` | 266 | Flux modal |
| `resources/views/livewire/business/task-timeline.blade.php` | 268 | Flux modal |
| `resources/views/livewire/business/task-timeline.blade.php` | 304 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 490 | modal event |
| `resources/views/livewire/business/team-hub.blade.php` | 728 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 733 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 735 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 789 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 791 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 798 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 800 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 845 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 847 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 867 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 869 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 924 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 926 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 931 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 934 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 978 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 984 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 987 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 1000 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 1002 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 1079 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 1081 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 1120 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 1124 | Flux modal |
| `resources/views/livewire/business/team-hub.blade.php` | 1138 | Flux modal |
| `resources/views/livewire/category-hub.blade.php` | 123 | modal event |
| `resources/views/livewire/client-portal.blade.php` | 159 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 163 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 265 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 269 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 276 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 279 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 283 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 321 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 324 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 327 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 331 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 344 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 346 | Flux modal |
| `resources/views/livewire/client-portal.blade.php` | 351 | Flux modal |
| `resources/views/livewire/company-expenses.blade.php` | 31 | Flux modal |
| `resources/views/livewire/company-expenses.blade.php` | 32 | modal event |
| `resources/views/livewire/company-expenses.blade.php` | 35 | Flux modal |
| `resources/views/livewire/company-expenses.blade.php` | 221 | Flux modal |
| `resources/views/livewire/company-expenses.blade.php` | 226 | Flux modal |
| `resources/views/livewire/company-expenses.blade.php` | 228 | Flux modal |
| `resources/views/livewire/company-expenses.blade.php` | 305 | Flux modal |
| `resources/views/livewire/company-expenses.blade.php` | 307 | Flux modal |
| `resources/views/livewire/company-expenses.blade.php` | 314 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 278 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 292 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 298 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 346 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 388 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 394 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 841 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 892 | modal event |
| `resources/views/livewire/dashboard.blade.php` | 895 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 898 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 974 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 978 | Flux modal |
| `resources/views/livewire/dashboard.blade.php` | 1003 | Flux modal |
| `resources/views/livewire/debt-hub.blade.php` | 148 | modal event |
| `resources/views/livewire/debt-hub.blade.php` | 217 | modal event |
| `resources/views/livewire/expense-form.blade.php` | 5 | ARIA dialog, Alpine modal |
| `resources/views/livewire/expense-form.blade.php` | 6 | Alpine modal |
| `resources/views/livewire/expense-form.blade.php` | 7 | Alpine modal |
| `resources/views/livewire/expense-form.blade.php` | 37 | ARIA dialog, Alpine modal |
| `resources/views/livewire/expense-form.blade.php` | 38 | Alpine modal |
| `resources/views/livewire/expense-form.blade.php` | 39 | Alpine modal |
| `resources/views/livewire/expenses.blade.php` | 26 | ARIA dialog, Alpine modal |
| `resources/views/livewire/goals-hub.blade.php` | 471 | modal event |
| `resources/views/livewire/goals-hub.blade.php` | 473 | modal event |
| `resources/views/livewire/goals-hub.blade.php` | 662 | modal event |
| `resources/views/livewire/goals-hub.blade.php` | 663 | modal event |
| `resources/views/livewire/hub/reminders.blade.php` | 174 | Alpine modal |
| `resources/views/livewire/hub/reminders.blade.php` | 179 | Alpine modal |
| `resources/views/livewire/income-hub.blade.php` | 65 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 438 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 560 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 561 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 712 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1293 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1304 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1305 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1408 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1409 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1527 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1528 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1640 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1641 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1733 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1734 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1836 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1837 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1933 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 1934 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 2217 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 2218 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 2359 | modal event |
| `resources/views/livewire/income-hub.blade.php` | 2360 | modal event |
| `resources/views/livewire/investments-hub.blade.php` | 1189 | modal event |
| `resources/views/livewire/investments-hub.blade.php` | 1190 | modal event |
| `resources/views/livewire/personal-calendar.blade.php` | 244 | Alpine modal |
| `resources/views/livewire/personal-calendar.blade.php` | 260 | Alpine modal |
| `resources/views/livewire/profile/delete-user-form.blade.php` | 42 | Alpine modal |
| `resources/views/livewire/public/candidate-portal.blade.php` | 163 | Flux modal |
| `resources/views/livewire/public/candidate-portal.blade.php` | 165 | Flux modal, Livewire state |
| `resources/views/livewire/public/candidate-portal.blade.php` | 167 | Flux modal |
| `resources/views/livewire/public/careers-portal.blade.php` | 87 | Flux modal |
| `resources/views/livewire/public/careers-portal.blade.php` | 94 | Flux modal |
| `resources/views/livewire/public/careers-portal.blade.php` | 150 | Flux modal |
| `resources/views/livewire/public/careers-portal.blade.php` | 159 | Flux modal |
| `resources/views/livewire/public/careers-portal.blade.php` | 162 | Flux modal |
| `resources/views/livewire/public/careers-portal.blade.php` | 167 | Flux modal |
| `resources/views/livewire/public/careers-portal.blade.php` | 215 | modal event |
| `resources/views/livewire/public/careers-portal.blade.php` | 265 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 61 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 63 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 73 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 75 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 138 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 156 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 160 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 179 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 182 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 191 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 194 | Flux modal |
| `resources/views/livewire/public/supplier-dashboard.blade.php` | 203 | Flux modal |
| `resources/views/livewire/social/social-hub.blade.php` | 695 | modal event |
| `resources/views/livewire/social/social-hub.blade.php` | 696 | modal event |
| `resources/views/livewire/social/social-profile.blade.php` | 270 | Alpine modal |
| `resources/views/pages/settings/delete-user-form.blade.php` | 13 | Flux modal |
| `resources/views/pages/settings/delete-user-form.blade.php` | 17 | Flux modal |
| `resources/views/pages/settings/delete-user-modal.blade.php` | 28 | Flux modal |
| `resources/views/pages/settings/delete-user-modal.blade.php` | 41 | Flux modal |
| `resources/views/pages/settings/delete-user-modal.blade.php` | 43 | Flux modal |
| `resources/views/pages/settings/delete-user-modal.blade.php` | 50 | Flux modal |
| `resources/views/pages/settings/security.blade.php` | 239 | Flux modal |
| `resources/views/pages/settings/security.blade.php` | 246 | Flux modal |
| `resources/views/pages/settings/security.blade.php` | 311 | Flux modal |
| `resources/views/pages/settings/security.blade.php` | 315 | Livewire state |
| `resources/views/pages/settings/security.blade.php` | 340 | Flux modal |
| `resources/views/pages/settings/two-factor-setup-modal.blade.php` | 155 | Flux modal |
| `resources/views/pages/settings/two-factor-setup-modal.blade.php` | 310 | Flux modal |

## Verification

- Source inventory generated by the same repository checkout used for the changes.
- `npm run build`, Pint and Pest are left to the repository CI workflow for final verification.
- Browser/device visual certification still requires an actual browser; this source audit does not claim pixel-perfect certification.
