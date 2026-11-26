# Payment integrations

pH7Builder currently treats a successful payment as a one-time membership
purchase with a configured duration. It does not yet own a recurring
subscription lifecycle. In particular, the application has no durable model
for provider customers, subscriptions, invoices, billing periods, scheduled
cancellations, retries, or dunning.

The existing payment providers must therefore remain payment gateways. A
subscription billing platform must not be added to the gateway selector until
the subscription domain described below exists.

## Current integration boundary

- PayPal is the only bundled checkout that can currently be exposed after its
  configuration has been validated.
- The legacy Stripe and 2Checkout flows remain unavailable until they are
  migrated to their providers' current APIs and verified end to end.
- The deprecated Braintree Drop-in checkout was removed in pH7Builder 20.0.0.
- The CCBill integration is incomplete and must not be used in production.

New direct gateways should implement the existing one-time payment behaviour
without changing established PayPal checkout or membership fulfilment.

## Subscription platform decision

Chargebee is a subscription management and billing orchestration platform, not
a direct replacement for Braintree. It can provide hosted checkout, recurring
billing, invoices, retries, cancellation flows, and access to multiple payment
gateways. Those capabilities are useful only when pH7Builder can represent and
reconcile the complete subscription lifecycle.

Adding Chargebee to the current gateway selector would create a misleading
partial integration: checkout could succeed while renewals, failed payments,
refunds, plan changes, cancellations, and delayed or duplicated webhook events
could leave membership access out of sync. It would also add platform fees and
operational configuration without demonstrated operator demand.

For those reasons, Chargebee is intentionally not bundled at this stage. It
should be reconsidered only when recurring billing is a confirmed product
requirement and the provider-neutral subscription model below has been
implemented.

## Required subscription foundation

Before adding any subscription billing platform, introduce a provider-neutral
layer that includes:

1. Durable customer, subscription, price/plan, invoice, and entitlement
   records with external provider identifiers.
2. Explicit lifecycle states for trials, activation, renewal, past-due access,
   scheduled cancellation, cancellation, expiration, and refunds.
3. A unique event inbox keyed by provider and event identifier so webhook
   delivery is idempotent, auditable, and retryable.
4. Authenticated webhook endpoints following the provider's current security
   guidance, with strict payload validation and secrets kept out of source
   control and logs.
5. Ordering protection using provider resource versions or event timestamps,
   plus asynchronous processing that acknowledges valid delivery promptly.
6. Recovery and reconciliation jobs for missed, delayed, duplicated, and
   out-of-order events, with safe operator-visible failure states.
7. Transactional entitlement changes so a billing event cannot be recorded
   without applying, or explicitly retrying, its membership effect.
8. Automated contract, lifecycle, replay, failure-recovery, upgrade, and
   backward-compatibility tests.

The provider interface should live alongside, rather than inside, the direct
payment-gateway interface. A billing adapter may create a hosted checkout and
translate provider events, but the application must own canonical subscription
and entitlement state.
