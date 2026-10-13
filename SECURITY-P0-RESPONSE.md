# P0 incident response — exposed repository credentials

The committed production credentials were removed from `backend/.env` on this branch, but **their historical commits remain public**. Treat the original DB, SMTP, Symfony APP_SECRET and SumUp API keys (including commented keys) as compromised.

## Required manual rotation BEFORE deployment
1. In LWS Panel: change the MariaDB account password (prefer creating a new least-privileged database user), update production DATABASE_URL.
2. In LWS Panel: change the SMTP account password, update MAILER_DSN.
3. In the SumUp merchant dashboard: create a replacement API key with minimum payment/checkout scopes, update SUMUP_API_KEY, revoke every published old key.
4. Generate a new cryptographically random APP_SECRET and update production APP_SECRET (existing remember-me sessions may be invalidated).
5. Put replacements in `backend/.env.local` on the hosting server (and restrict filesystem permissions); NEVER commit this file. Alternatively use secure server environment variables.
6. Deploy code, clear production cache, test one low-value SumUp checkout, verify successful server-side status, test email.
7. Review LWS/SMTP/SumUp access and transaction logs for misuse. Coordinate secret revocation and deployment to avoid payment outages.
8. After rotation, scrub Git history using git-filter-repo or BFG (all branches/tags) and force-push in a coordinated maintenance window; notify collaborators to reclone. Rewriting history does NOT replace rotation.

## SumUp checkout webhooks
Official documentation: https://developer.sumup.com/online-payments/webhooks

The documented checkout webhook payload contains event_type=CHECKOUT_STATUS_CHANGED and id. **No HMAC signature contract is documented for these checkout events**. Never invent or require a made-up x-payload-signature header. The endpoint now treats the notification as untrusted and retrieves the checkout using the server API key; it checks checkout ID, reference, amount and currency before processing the SumUp-confirmed status. Failed upstream verification returns a retryable status.

Ensure production SUMUP_WEBHOOK_URL remains configured and HTTPS-accessible. The callback URL and customer redirect URL are different settings.

### Follow-up hardening
Use transactional deduplication/locking for parallel webhook deliveries, and add integration tests for paid, pending, forged, mismatched and retry scenarios.
