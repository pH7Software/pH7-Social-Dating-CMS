# pH7Builder 2027 Product Roadmap

Last reviewed: 2026-11-13

This roadmap keeps product work focused on independent dating and community
founders. It is evidence-led, but it is not a promise that every experiment
will ship. Validate larger ideas before adding permanent maintenance cost.

## Current market evidence

The following observations come from vendor-published pages and describe how
those products position themselves. They are not independent verification of
every feature or outcome.

- [SkaDate pricing](https://www.skadate.com/order/) bundles source code,
  installation, a progressive web app, templates, and optional ongoing
  hosting, support, and plugin services. Its higher package adds native-app
  source and store-submission services.
- [DatingPro](https://www.datingpro.com/dating-software/) leads with a working
  site delivered quickly, admin-led branding and configuration, monetisation,
  moderation, and starter AI profiles.
- [Chameleon](https://www.chameleonsocial.com/in/features.php) emphasises a
  large switchable feature set, templates, built-in communication formats,
  administration, monetisation, and editable PHP source, while identifying
  exceptions that remain protected.
- [UNA](https://una.io/) positions itself as a self-hosted, MIT-licensed
  community platform with feeds, groups, events, messaging, extensibility,
  owner-controlled data, and optional model-provider or local AI agents.

The consistent founder promise is not merely feature count. It is a shorter,
more understandable path from installation to a credible live community.
pH7Builder should compete through transparent readiness, complete ownership,
privacy-respecting defaults, and focused member experiences rather than fake
activity or an expensive hosted dependency.

## Immediate improvements

### Deterministic launch readiness

- **Problem:** The admin dashboard's original getting-started list disappeared
  after eight days even when essential production work remained incomplete.
- **Target audience:** First-time and occasional self-hosted founders.
- **Supporting evidence:** Competitors prominently sell setup speed and guided
  launch. pH7Builder's own launch checklist shows that installation alone is
  not production readiness.
- **Proposed solution:** Keep a five-check technical readiness panel visible
  until HTTPS, installer removal, production mode, online status, and a cron
  secret are confirmed. Link to the full human-reviewed launch checklist.
- **Differentiation:** Deterministic local checks; no telemetry, account, AI,
  or external service is required.
- **Expected impact:** Fewer silent launch omissions and clearer next actions.
- **Complexity:** Low.
- **Risks:** A green technical panel could imply more certainty than it has.
  The interface must state which email, payment, legal, safety, backup, and
  accessibility checks remain manual.
- **Validation criteria:** Automated tests cover every check and escaping; a
  configured site reports five of five; any missing requirement keeps the
  panel visible.
- **Status:** In progress for 20.0.0.

## Next major release

### Guided founder launch workspace

- **Problem:** Branding, registration, email, moderation, memberships,
  payments, backups, and final member-journey tests are spread across several
  admin areas and external operational tasks.
- **Target audience:** Independent founders without a dedicated launch team.
- **Supporting evidence:** SkaDate and DatingPro make turnkey setup central to
  their positioning. The pH7Builder launch checklist already defines the work,
  but the admin experience does not yet organise the complete journey.
- **Proposed solution:** Extend the readiness panel into a small launch
  workspace with grouped actions, explicit manual confirmations, and safe
  niche presets that preview changes before applying them.
- **Differentiation:** Founders retain the source, hosting, database, and
  configuration; presets remain inspectable and reversible.
- **Expected impact:** Reduce navigation and uncertainty between installation
  and the first tested signup.
- **Complexity:** Medium.
- **Risks:** Presets can overwrite intentional configuration or encourage a
  generic community. Never apply consequential changes silently.
- **Validation criteria:** A clean installation exposes every required launch
  area from one workspace; a founder can preview and reject preset changes;
  existing settings are preserved unless explicitly selected.

## 2027 product opportunities

### Community-first dating spaces

- **Problem:** Profile search and one-to-one messaging give new members little
  shared context and make small communities feel empty.
- **Target audience:** Niche, local, interest-led, and relationship-intent
  communities.
- **Supporting evidence:** UNA makes feeds, groups, events, and discussions
  core building blocks, while Chameleon promotes varied interaction formats.
  These are vendor claims, so member demand still requires validation.
- **Proposed solution:** Prototype opt-in spaces around an interest, place, or
  relationship intent, then let members move from a moderated conversation to
  a private introduction.
- **Differentiation:** The community creates context before private contact;
  founders can run a distinctive niche without relying on endless swiping.
- **Expected impact:** More meaningful reasons to return and safer first
  interactions for small member bases.
- **Complexity:** High.
- **Risks:** Moderation workload, fragmented activity, privacy leakage, and
  cold-start failure.
- **Validation criteria:** Test first with one space type; require explicit
  visibility controls and reporting; continue only if participants start
  conversations without materially increasing unresolved moderation work.

## Breakthrough experiments

### Bounded introduction circles

- **Problem:** Unlimited browsing can turn people into inventory and rewards
  volume rather than attention, while small communities cannot imitate the
  density of mass-market apps.
- **Target audience:** Members who prefer intentional introductions over an
  infinite catalogue.
- **Supporting evidence:** Existing competitors compete heavily on breadth and
  feature count. That leaves room to test a deliberately smaller interaction
  model instead of reproducing the same catalogue experience.
- **Proposed solution:** Run an opt-in experiment that introduces a small
  circle of compatible members for a limited period around a shared prompt or
  activity. Members choose whether to continue privately; no fabricated
  compatibility score or undisclosed AI conversation is used.
- **Differentiation:** Scarcity is used to protect attention, not to manipulate
  payment. The model can work with a modest, focused member base.
- **Expected impact:** Deeper conversations and a clearer identity for niche
  communities.
- **Complexity:** Medium for a manual prototype; high for automation.
- **Risks:** Weak matching, exclusion, awkward group dynamics, safety concerns,
  and insufficient participation.
- **Validation criteria:** Begin as a founder-operated, consent-based pilot;
  define safety and exit controls first; measure opt-in, introductions that
  receive a reply, participant-reported usefulness, and moderation incidents
  before considering product integration.

## Product decisions

- Do not copy undisclosed generated profiles, fake activity, fabricated
  matches, or automated conversations.
- Prefer deterministic logic when it solves the problem; AI must be optional,
  transparent, and privacy-conscious.
- Do not add a paid service or recurring monitoring system merely to claim
  competitive intelligence. Record a new dated observation only when it can
  change a product decision.
- Keep the README durable. Release-specific requirements and procedures belong
  in the Quick Start, release notes, upgrade guide, and launch checklist.
