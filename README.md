# Moduloom

One platform. Six ecosystems. Infinite combinations.

Moduloom is a modular, open-source application platform — Laravel/PHP
backend, React/TypeScript frontend, MariaDB, Tailwind. Instead of
shipping separate products for different needs, Moduloom ships one
platform and lets an installation grow by adding modules, never by
migrating to a different product. Install Personal today; add Small
Business on top six months later without losing a single note, file,
or published page.

## What's actually built

Six phases in, this is real, working, tested software — not a plan:

- A package-manager-style kernel (reference-counted modules, additive
  profile overlays, instance-scoped roles, non-destructive removal)
  enforced by real database constraints, not convention.
- **22 real modules** across all six ecosystems — Personal, Small
  Business, Community, Education, Enterprise, Government — each its
  own Composer package with a real migration, model, API, and React
  admin screen.
- A setup wizard, a dashboard, a module marketplace for installing
  anything standalone outside a profile, and an instance-scoped theme
  system for public-facing pages.
- 45 automated tests, run against real MariaDB (not SQLite) on every
  push — [check the latest run](../../actions).

## Read these first

- [`MASTER_VISION.md`](./MASTER_VISION.md) — what this is, what it
  isn't, and the one idea the whole project exists to protect: never
  make someone abandon their installation because their needs changed.
- [`MASTER_ARCHITECTURE.md`](./MASTER_ARCHITECTURE.md) — the kernel
  invariants, schema, and technology decisions.
- [`MASTER_ROADMAP.md`](./MASTER_ROADMAP.md) — phased build order,
  what's done, what's next.
- [`SECURITY.md`](./SECURITY.md) — **read this before deploying
  anywhere reachable by the public internet.** The short version:
  there is no authentication or authorization built yet. That's a
  real, scoped, named next phase (Phase 6), not an oversight — but it
  means this is source code to read, learn from, and build on right
  now, not something to point a public URL at yet.
- [`DEPLOYMENT.md`](./DEPLOYMENT.md) — real steps for shared hosting,
  once Phase 6 is done.

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

Public, pre-authentication. The platform and module mechanics are
real and thoroughly tested; access control is the one deliberate gap
standing between this and a real deployment.
