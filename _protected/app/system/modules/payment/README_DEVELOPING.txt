Payment integrations must be verified against each provider's current API and
sandbox before they are enabled. The CCBill integration remains incomplete;
do not use it for production without implementation and end-to-end tests.

Subscription billing platforms such as Chargebee are not direct payment
gateways and must not be added to the gateway selector without a complete,
provider-neutral subscription lifecycle. See docs/PAYMENT_INTEGRATIONS.md for
the current boundary and security requirements.

Contributions belong in the pH7Builder GitHub repository:
https://github.com/pH7Software/pH7-Social-Dating-CMS
