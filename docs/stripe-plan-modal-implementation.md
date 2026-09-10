# Stripe plan modal implementation

This document records the intended safe Stripe fields for the Finance Pro plan modal.

The application must never retrieve, store, log, or display the full card PAN or CVC. The UI may display only card brand, last four digits, expiry, and billing name.

Safe fields:
- Customer name, email, phone, Stripe Customer ID, currency and billing address
- Stripe Subscription ID, status, renewal/current period end and cancellation state
- Payment method type, card brand, last4, expiry month/year and billing name
- Invoice ID/number, amount, currency, status, creation/payment dates, hosted invoice URL and PDF URL when available

Implementation should use config('services.stripe.secret'), handle missing Stripe data and API errors gracefully, and avoid exposing Stripe secrets.