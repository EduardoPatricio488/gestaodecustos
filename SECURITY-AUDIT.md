# Finance Pro AI — Security & Integrity Audit

Date: 2026-09-29

## Critical/high findings addressed

- Client and supplier portal credentials were 6-digit values. Replaced generation with 64-character cryptographically secure tokens and rotate legacy short tokens when access credentials are generated.
- Client portal login had no brute-force throttling. Added per-IP/per-NIF rate limiting.
- Supplier portal login had no brute-force throttling. Added per-IP/per-NIF rate limiting.
- Public client/supplier/bank company search exposed `business_email`. Removed the email field from public result payloads.
- Public client/supplier/bank access-request forms had no rate limiting. Added per-IP/per-email throttling.
- Admin user-management destructive/security operations were available to `moderator` and `analyst` through the shared admin role gate. Sensitive operations now require the `admin` role.
- Admin impersonation was available to all admin-like roles. Start/stop and impersonation-session validation now require the real `admin` role.
- Email verification codes used `rand()`. Registration now uses `random_int()` and verification attempts are rate-limited.
- WhatsApp verification accepted empty configuration values and used non-constant comparison. Empty secrets are rejected and `hash_equals()` is used.
- WhatsApp webhook logs contained sender phone numbers and message contents. Logs now only record whether sender/text fields were present.
- Stripe store checkout completion trusted a paid session without proving it belonged to the exact pending checkout. Completion now verifies session ID, metadata, amount and currency.
- Stripe checkout completion could complete a cancelled/stale pending checkout. Completion now requires `pending` status and locks the checkout row transactionally.
- Stripe checkout could silently complete while a purchased product had become inactive/missing. Checkout completion now aborts the transaction instead of silently granting a partial purchase.
- Income model accepted non-positive values. Model-level integrity validation now rejects zero/negative income values.

## Additional reviewed protections

- Workspace-scoped mutations are enforced by `BelongsToWorkspace` for models using the trait.
- Workspace context switching resolves the workspace through the authenticated user's membership.
- Bank portal authentication already uses hashed, revocable, expiring audit tokens and is additionally rate-limited.
- Bank portal dashboard revalidates the portal session and token state on every render.
- Employee invitation acceptance is transactional, uses row locking, hashed invite tokens and one-time consumption.
- Client and supplier portal ticket actions scope ticket lookup through the authenticated portal entity.
- Store checkout completion uses a database transaction and row locking to reduce double-completion races.
- CSRF protection remains enabled for normal web requests; Stripe/WhatsApp exceptions are explicitly required by their webhook architecture.

## Hardening completed on 2026-09-29

- Production secrets remain environment configuration; an explicit `.env.production.example` was added without real credentials.
- Production HTTPS is enforced by middleware; production session cookies default to Secure and HTTP-only, and session payloads are encrypted.
- Security response headers are applied globally, including HSTS on secure production responses.
- API/AI endpoints have named rate limits; the existing authentication and verification throttles remain active.
- Privileged User fields (`is_admin`, `is_active`, `role`, `plan`) are no longer mass assignable; internal plan changes use explicit `forceFill()` writes.
- Stored user IP addresses are encrypted at rest through Laravel encrypted casting.
- Fitness file imports validate type/size, disable network access during XML parsing, cap imported records, and no longer return parser exception details to clients.
- Smartwatch AI input is validated, its response is allowlisted, and the provider key is read through server-side configuration.
- Composer and npm dependency audits now run automatically on pushes/PRs and weekly through GitHub Actions.
- Application-level workspace authorization remains mandatory; database row isolation must not be treated as the only security boundary.

## Remaining attention

- Portal tokens are still stored as reversible plaintext values for compatibility with the existing token-in-URL architecture. A future migration can introduce `portal_token_hash` and one-way lookup/rotation if required.
- The verification code is still stored in the existing `verification_code` column for compatibility. A future migration can add expiry/attempt metadata and store only a hash.
- Admin/moderator/analyst separation should be reviewed route-by-route for every admin screen; only the highest-impact user-management and impersonation operations were tightened in this pass.
- Full local execution of Pest/Pint/Vite requires the project's local PHP/Composer/Node environment. GitHub Actions contains a PHP 8.3 + Pint + Pest + Vite integrity workflow; its status should be checked after the latest push.
