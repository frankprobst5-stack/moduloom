# Moduloom

One platform. Six ecosystems. Infinite combinations.

Moduloom is a modular, open-source application platform — Laravel/PHP
backend, React/TypeScript frontend, MariaDB. Instead of shipping
separate products for different needs, Moduloom ships one platform and
lets an installation grow by adding modules, never by migrating to a
different product. Install Personal today; add Small Business on top
six months later without losing a single note, file, or published
page.

## What's actually built

This is real, working, tested software — not a plan:

- A package-manager-style kernel (reference-counted modules, additive
  profile overlays, instance-scoped roles, non-destructive removal —
  orphaning and actual data purge are two distinct, separately
  confirmed actions) enforced by real database constraints, not
  convention.
- **22 real modules** across all six ecosystems — Personal, Small
  Business, Community, Education, Enterprise, Government — each its
  own Composer package with a real migration, model, API, and React
  admin screen.
- **Real authentication and authorization.** Session login, Owner/Staff/
  Customer roles mapped to the kernel's existing capability grants, a
  Team screen for an Owner to create accounts, and row-level ownership
  for the four "view only your own X" capabilities (Invoicing,
  Applications, Assignments, Workflows) — not just a login screen in
  front of an otherwise-open app. See `PHASE_6_AUTH_DESIGN.md`.
- **Federation between instances.** An instance can publish its public
  Website/Knowledge base/Public information content as a feed, and an
  Owner can subscribe to another instance's feed per content type —
  pull-based, no central directory, no shared identity. See
  `FEDERATION_DESIGN.md`.
- A setup wizard, a dashboard, a module marketplace for installing
  anything standalone outside a profile (including permanently purging
  an orphaned module's data, a separate confirmed action from
  uninstalling it), and an instance-scoped theme system for
  public-facing pages — kept deliberately separate from the admin
  shell's own brand identity.
- 85 automated tests, run against real MariaDB (not SQLite) on every
  push — [check the latest run](../../actions).

## Read these first

- [`MASTER_VISION.md`](./MASTER_VISION.md) — what this is, what it
  isn't, and the one idea the whole project exists to protect: never
  make someone abandon their installation because their needs changed.
- [`MASTER_ARCHITECTURE.md`](./MASTER_ARCHITECTURE.md) — the kernel
  invariants, schema, and technology decisions. Its OPEN questions list
  is currently empty — everything it ever raised has either been built
  or deliberately decided against (multi-tenancy, on the record).
- [`MASTER_ROADMAP.md`](./MASTER_ROADMAP.md) — phased build order and
  what shipped in each phase.
- [`PHASE_6_AUTH_DESIGN.md`](./PHASE_6_AUTH_DESIGN.md) — the
  authentication/authorization design and what each sub-phase actually
  shipped.
- [`FEDERATION_DESIGN.md`](./FEDERATION_DESIGN.md) — the federation
  design and what each stage actually shipped.
- [`SECURITY.md`](./SECURITY.md) — the real review: what's clean, what
  was found and fixed, and the two honest remaining items (no CSRF
  token check beyond `Sec-Fetch-Site`/`SameSite=Lax`, no mechanism yet
  for upgrading an already-installed module's schema).
- [`DEPLOYMENT.md`](./DEPLOYMENT.md) — real steps for shared hosting,
  verified end-to-end against a genuinely fresh database, not just
  written down.

## License

AGPL-3.0-or-later, with an additional permission under Section 7:
modules, profiles, and themes that interact with Moduloom only through
its documented extension points (the `ModuleManifest`/`ProfileManifest`
interfaces, the module package boundary, the theme layout convention)
are independent works and may be licensed on any terms — only the
kernel itself, and anything that copies its source rather than
extending it through those points, is bound by the AGPL. See
[`LICENSE`](./LICENSE) for the exact terms.

## Status

Public, with authentication, authorization, and federation all
complete. Safe to deploy somewhere reachable by the public internet —
`SECURITY.md` and `DEPLOYMENT.md` have the honest remaining details.
Marketplace monetization and a mobile app wrapper remain explicitly
out of scope for now: both need real business or account decisions
(a payment processor, Apple/Google developer accounts) outside what a
coding pass can resolve on its own.
