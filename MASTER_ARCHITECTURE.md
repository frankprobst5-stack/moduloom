# Moduloom — Master Architecture

Status: Phases 0-6 implemented (kernel through authentication/
authorization — see `MASTER_ROADMAP.md`). Originally written
pre-implementation so these decisions could be challenged and revised
deliberately instead of discovered by accident mid-build; kept current
since as each one gets resolved or superseded by what actually got
built. Sections marked **OPEN** are still genuinely unresolved and
should not be treated as decided.

## Technology decisions

| Layer | Choice |
|---|---|
| Backend | Laravel / PHP |
| Frontend | React / TypeScript |
| Database | MariaDB |
| Styling | Tailwind |
| Package manager (backend) | Composer — every module is its own package |
| Source hosting | GitHub |
| Deployment target | Shared hosting (BlueHost/DreamHost-class), self-hosted |

Each module is an independent Composer package with its own namespace, its
own Laravel migrations, and its own internal versioning. The kernel never
needs to know how a module's internal schema evolves — only the module's
slug, version, and whether it is currently present in an instance. Module
internals and kernel bookkeeping are two separate concerns and must stay
that way.

## The model: a package manager for application capability

The relationship between the kernel, modules, and ecosystem profiles is
deliberately modeled on OS package managers (apt, Homebrew), because that
model has already solved the hard version of this problem: safely
installing, sharing, and removing interdependent pieces of software
without a human having to reason about every other piece that might
depend on what they're removing.

- A **module** is the unit of installable capability (CRM, Forums,
  Invoicing, ...).
- A **profile** (what the Vision doc calls an "ecosystem") is a
  meta-package: a named, versioned list of modules plus default
  configuration, default role grants, and a starter theme.
- Installing a profile is exactly like installing a meta-package: it
  pulls in everything it lists, skips anything already present, and
  records *why* each module is there.

## The four kernel invariants

Every module, profile, and migration must obey these. They are listed in
order of how much destructive-state risk they eliminate.

### 1. Reference-counted module registry

Modules track **who requested them**, not a boolean `is_installed` flag.

A module becomes eligible for removal only when it has zero remaining
referrers. A referrer can be:
- a **profile** (an ecosystem that bundled it),
- a **manual** install (a user added it directly, outside any profile),
- a **module dependency** (another installed module declares it as a
  requirement — e.g. Invoicing requires CRM).

All three source types matter and must be tracked distinctly. Collapsing
"module dependency" into "profile" loses the ability to say *"CRM is
still needed because Invoicing needs it"*, independent of which profile
either arrived through.

`module_references` must carry a **uniqueness constraint** on
`(module_slug, reference_type, reference_identifier)`. Without it,
re-applying the same profile twice — which invariant 4 requires to be
safe — can insert duplicate rows, and "reference count" stops meaning
anything. "Zero references" means zero *distinct* referrers, not zero
rows.

### 2. Additive overlays — create if missing, extend if present

A profile never owns configuration. It only adds what is missing.

If a module a profile wants is already present (installed by an earlier
profile, or manually), the new profile must still:
- **skip** re-provisioning the module itself (its config, its data, its
  settings are immutable to the incoming profile), but
- **always** write its own reference row for that module.

These are two different operations and must not share one
"already exists → skip entirely" code path. If they do, a profile that
finds its own modules already present can silently fail to register that
it depends on them — and a later, unrelated removal of the *original*
referrer can then delete a module a different profile is still actively
using.

### 3. Instance-scoped roles, profile-granted capabilities

Roles (Owner, Staff, Customer, ...) belong to the **instance**, not to any
one profile. A profile that wants to grant a capability to an existing
role (e.g. the Community profile granting `forums.view` to a `Customer`
role that Small Business already created) attaches a grant — it never
recreates or redefines the role.

`role_capability_grants` needs the same N:1 discipline as module
references: a given `(role, capability)` pair can have grants from
multiple sources. Removing one profile's grant must check whether *any
other* source still grants that same capability to that same role before
revoking it — never assume one row's deletion is authoritative.

### 4. Point-in-time snapshot semantics — no live profile sync

Installing a profile is an **imperative, versioned transition applied
once**, not a live-synced desired-state loop. `small-business@1.0.0`
installed today does not change if Moduloom ships `small-business@2.0.0`
next year. An instance only moves to a newer profile version if an admin
explicitly re-applies it.

This falls out of invariants 1–3 as a free property, not a separately
built feature: because profile application is idempotent and additive,
"upgrading" a profile is *the same reconciler operation* as installing it
for the first time — it just finds most modules already present and adds
whatever changed. If upgrade logic ever needs its own code path distinct
from install logic, that is a signal invariant 2 has a hole.

## Removal is non-destructive by default

Hitting zero references does **not** delete a module's data. It moves the
module to an `orphaned` state: hidden from navigation, disabled, data
retained in place. Actually dropping a module's tables and files is a
separate, explicitly-confirmed, distinct action. Auto-deleting on
refcount-zero is the one place this design could become destructive by
default, which directly contradicts the Vision document's core claim.

## Kernel schema sketch

```
installed_modules
  module_slug        (pk)
  version
  status             -- active | disabled | orphaned
  installed_at

module_references
  module_slug
  reference_type     -- profile | manual | module_dependency
  reference_identifier
  UNIQUE (module_slug, reference_type, reference_identifier)

roles
  role_slug          (pk)
  instance-scoped, created by whichever profile/module first needs them

capabilities
  capability_slug    (pk)

role_capability_grants
  role_slug
  capability_slug
  granted_by_source  -- which profile/module granted this
  UNIQUE (role_slug, capability_slug, granted_by_source)

profile_install_history
  profile_slug
  version
  installed_at
  installed_by

theme_selections
  theme_slug
  set_by             -- which profile/action set this
  -- append-only; the active theme is whichever row is latest
```

A module's own internal schema (its own tables, its own migrations) lives
entirely inside that module's Composer package and is versioned
independently via Laravel's own migration system. The kernel schema above
only ever answers "which modules exist in this instance and why" — never
"what does this module's data look like."

## Module manifest (sketch)

Every module package ships a manifest describing:
- `slug`, `version`
- `requires`: other module slugs it depends on (feeds `module_dependency`
  references)
- `nav`: what it contributes to the sidebar, with a sort weight
- `capabilities`: what capabilities it introduces and which default role(s)
  it suggests granting them to
- `migrations`: its own, run through Laravel's normal migration system

A profile manifest describes:
- `slug`, `version`
- `modules`: required module slugs
- `optional_modules`: suggested, not auto-installed
- `default_config`: applied only to modules it is the first to provision
- `role_grants`: capability grants it attaches to roles it creates or
  finds already present

## Theme system (resolved, Phase 5)

A theme governs **only public-facing pages** — the views a visitor
reaches without being in the app (Website's `/site/{slug}`, Knowledge
base's `/kb/{slug}`, Public information's `/notices`). It never touches
the admin shell: the dashboard, the setup wizard, and every module's own
admin screen look the same regardless of which theme is active.

This isn't just convention-following (though it does match WordPress's
theme/wp-admin split, and the fact that Directus, Strapi, NocoDB, and
Appsmith all keep one fixed admin chrome regardless of what the public
output looks like). It's forced by something Phase 3 already proved:
once ecosystems evolve together, there is no single "current ecosystem"
left to theme a *shared* dashboard by. An instance with Personal, Small
Business, and Community all installed has one dashboard, used by
whichever modules happen to be active — there's no coherent answer to
"whose theme wins" at that layer. Public-facing module output doesn't
have that problem: each page is produced by one specific module, and an
instance's visitor-facing surface can meaningfully share one consistent
look across all of them.

Mechanically:
- A theme is a directory under `resources/views/themes/{slug}/` with a
  `layout.blade.php` — presentation only, no migrations, no
  capabilities, not a module and not a profile.
- **Instance-scoped**, not per-module or per-ecosystem: `theme_selections`
  is an append-only table (same idiom as `profile_install_history`) —
  the active theme is whichever row is latest. Switching themes appends
  a row rather than updating one in place, which gives a free history
  at no extra cost.
- **Adopted non-destructively**: a profile's `themeSlug()` is adopted as
  the instance default only if no theme has ever been chosen yet. A
  later profile installed on top never silently changes a look the
  owner (or an earlier profile) already has — the same rule invariant 2
  applies to module configuration, applied here to presentation.
- **Explicitly switchable** afterward, from the dashboard. An explicit
  switch to a theme with no real layout on disk is refused outright
  (loud failure, not a silent no-op) — but every profile's `themeSlug()`
  names a theme that may not have a real design built yet, and that gap
  has to degrade gracefully rather than 500: rendering a public page
  falls back to the built-in `default` theme whenever the active slug
  has no `layout.blade.php` on disk.
- A module's public-facing controller never returns `view(...)`
  directly for a page a visitor will see — it wraps that content view
  through `App\Kernel\ThemeManager::wrap()`, which renders it inside
  the active theme's layout.

## Authentication & authorization (resolved, Phase 6)

Session-based, through Laravel's own `web` guard — not Sanctum, not a
separate token layer, since every client is this same app's own
same-origin React shell. Identity (`users`) and the kernel's existing
instance-scoped roles are linked by `user_role_assignments`
(many-to-many); a `capability:{slug}` middleware sits alongside
`module.active` on every module's own routes, plus a kernel-level
`instance.manage` capability on the handful of routes that live outside
any module (setup's profile install, marketplace install/uninstall,
team management). An Owner creates Staff and Customer accounts through
`/team` with a directly-set password — there's no invite-link flow,
since this app has no real outbound mail. Customer-facing "view own X"
capabilities are enforced with real row-level ownership (a `user_id` on
the owned record, scoped queries), not just a role check. Full design
and what shipped in each sub-phase: `PHASE_6_AUTH_DESIGN.md`.

Two things this resolution didn't cover at the time — both since closed
2026-11-21, see `MASTER_ROADMAP.md`: CSRF on `/api/*`, and no mechanism
for upgrading an already-installed module's schema.

## API surface (resolved by precedent)

REST, not GraphQL — every module's admin screen has talked to its own
`/api/modules/{slug}/...` JSON endpoints since Phase 1, with zero
exceptions across six phases. Never written down as a decision because
it was never actually in question once the first module shipped.

## Module/profile manifest format (resolved by precedent)

A PHP class implementing `ModuleManifest`/`ProfileManifest`, returned
from `App::make()` against a slug registered in `config/modules.php` /
`config/profiles.php` — not a JSON file, not a separate schema. Same
reasoning as the API surface: every module and profile has done it this
way since Phase 0, so there's no real decision left to make, just a
convention nothing has ever deviated from.

## OPEN questions (deliberately unresolved)

- How an orphaned module's data is eventually, optionally, purged — and
  by whom. Nothing built across six phases has touched this; orphaning
  (not purging) is still the only thing `ModuleInstaller`/`Reconciler`
  ever do on removal.
- Multi-tenancy: is one Moduloom install always one organization, or can
  a single deployment host multiple independent instances? Deliberately
  deferred — see `MASTER_ROADMAP.md`'s "Explicitly deferred" list.
