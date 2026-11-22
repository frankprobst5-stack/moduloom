# Moduloom — Master Roadmap

This is a first-draft sequencing, written before any code exists. It is a
blueprint to challenge, not a commitment. Phases are ordered so that each
one proves the riskiest unproven idea as early as possible — the kernel
invariants in `MASTER_ARCHITECTURE.md` are the real risk in this project,
not any individual module's business logic.

## Phase 0 — Kernel foundation

Build the part that has no business logic in it at all: the registry.

- Laravel project scaffold, Composer module-package convention
- `installed_modules`, `module_references`, `roles`, `capabilities`,
  `role_capability_grants`, `profile_install_history` tables
- A reconciler service implementing invariants 1–3 against a single,
  deliberately trivial fake "module" with no real functionality
- CI: a disposable MariaDB test database, per the established pattern
  used elsewhere in this builder's other projects

**Acceptance criteria:** installing the same fake module twice via two
different fake "profiles" produces exactly two reference rows and the
module is removed only when both are gone. This is the one test that
matters most in the entire project — it proves invariant 1 before
anything else gets built on top of it.

## Phase 1 — One real module, no ecosystems yet

Build the smallest real module end-to-end through the kernel: **Notes**
(from the Personal ecosystem — chosen because it has no dependencies on
any other module).

- Module manifest format finalized against a real example
- Module's own Laravel migrations, its own CRUD, a minimal React admin
  screen
- Manual install/uninstall through the kernel (no profile involved yet)

**Acceptance criteria:** Notes can be installed, used, and manually
uninstalled without touching any other part of the system, and uninstall
correctly refuses if a second module later declares a dependency on it
(fake-tested the same way as Phase 0).

## Phase 2 — First profile: Personal

Personal is the smallest ecosystem (Website, Notes, Files, Tasks,
Personal databases) and has no cross-module business dependencies, which
makes it the right first profile to prove the *profile* mechanism itself,
separate from the module mechanism already proven in Phase 1.

- Build out Files, Tasks, Personal databases, and a minimal Website module
- Profile manifest format finalized
- Ecosystem picker UI (first real screen, informed by the design concepts
  already explored)
- Full install flow: modules → dependencies → DB structures → default
  permissions → theme → nav → initial dashboard → onboarding

**Acceptance criteria:** a fresh instance can install the Personal profile
end-to-end on a clean database and land on a working onboarding wizard.

## Phase 3 — The idea the whole project exists to prove: evolution

Build Small Business (Website, CRM, Inventory, Invoicing) as a second
profile installed **on top of** an existing Personal instance.

- CRM, Inventory, Invoicing modules
- Prove additive overlay (invariant 2): Website is shared between both
  profiles and must not be re-provisioned or reset
- Prove role extension (invariant 3): Owner/Staff/Customer roles created
  fresh by Small Business, later extended by a future profile without
  redefinition
- Prove non-destructive removal: uninstalling Small Business later leaves
  Website alone because Personal still references it

**Acceptance criteria:** a real, scripted test — install Personal, add
content, install Small Business on top, verify every piece of Personal
content is untouched, then remove Small Business and verify Personal
still works. This is the test that validates the entire Vision document,
not just the architecture.

## Phase 4 — Remaining ecosystems

With the module and profile mechanisms both proven under real load,
remaining ecosystems are mostly module-writing, not architecture work:

- Community (Forums, Membership, Events, Directories)
- Education (Classrooms, Assignments, Knowledge base — reuses Website)
- Enterprise (Documents, Workflows, Projects, Applications)
- Government (Public information, Forms, Records — reuses Workflows)

## Phase 5 — Marketplace, theming, public readiness

- [x] Theme system resolution — see MASTER_ARCHITECTURE.md's "Theme
      system" section. Also surfaced and fixed a real test-isolation bug
      (RefreshDatabase's transaction rollback silently broken by any test
      that triggers a module's first-install migration).
- [x] Module marketplace UI — every registered module, not just the ones
      your current profile bundled, with real install/uninstall through
      the same ModuleInstaller/Reconciler path profiles use. Verified live:
      installed Forums standalone with no profile involved, fully usable.
- [x] Security review — see SECURITY.md. Found and fixed one real
      vulnerability (unrestricted file uploads in Files/Documents, a real
      path to stored XSS or RCE on shared hosting; fixed with a content
      allowlist, proven by tests/Feature/Security/FileUploadRestrictionTest.php).
      Surfaced the one genuine blocker: there is no authentication or
      authorization enforcement anywhere in the app yet — deliberately
      deferred since Phase 0, not an oversight, but it has to become its
      own phase (Phase 6) before public visibility is acted on for
      anything beyond a local demo.
- [x] Deployment documentation for shared hosting — see DEPLOYMENT.md.
- [x] Decide public visibility — public. The repo and the running kernel
      are different things: making the *source* public carries none of the
      risk a live, publicly-reachable deployment would. Did the same
      due-diligence pass Stratum and SnapPress got first (secret audit,
      dead-code/stale-doc check, and — found missing here — a real license:
      AGPL-3.0-or-later with a Section 7 exception for third-party modules/
      profiles/themes, since MASTER_VISION.md said "open-source" but nothing
      had actually granted a license yet). SECURITY.md's finding still
      stands and is now prominent in the README: do not deploy a live
      instance publicly until Phase 6 (authentication) is done.

## Phase 6 — Authentication & authorization (newly identified, Phase 5's security review)

Not optional before any real deployment. A real design task, not a quick
addition — how a logged-in identity maps to the Owner/Staff/Customer
roles already modeled and tested since Phase 2, session vs. token auth,
and whether Customer-role users (forum members, students, citizens)
get full accounts or something lighter. Building this without deliberate
design would fight invariant 3's instance-scoped roles instead of using
them.

- [x] Design scoped — see `PHASE_6_AUTH_DESIGN.md`. Grounded in the
      actual current state (identity dormant, authorization modeled but
      unenforced, zero link between them) and a build order (6a close
      the hole / 6b capability enforcement / 6c Customer tier).
- [x] 6a shipped 2026-11-15 — session login/logout, every route gated
      (every ecosystem including Personal, per Frank's call), 56/56
      tests passing, verified live end-to-end against the real dev
      instance. No Owner/Staff/Customer distinction yet — single
      all-or-nothing gate.
- [x] 6b shipped 2026-11-15 — `user_role_assignments` links identity to
      role, `capability:{slug}` middleware wired onto all 22 modules'
      own routes alongside `module.active`, registration assigns the
      new owner role. Found and fixed a real gap along the way: a
      module installed standalone (marketplace, or direct
      `ModuleInstaller::install()`) was never granted to anyone —
      `ModuleManifest::capabilities()` has existed since Phase 1 but
      nothing ever consumed it. Fixed at `ModuleInstaller`, not patched
      around. 58/58 tests passing, verified live against the real dev
      instance (including the migration's backfill of the existing
      pre-6b account to Owner).
- [x] 6b follow-up shipped 2026-11-16 — a kernel-level `instance.manage`
      capability (granted to Owner at registration, same mechanism any
      module uses) now gates the three kernel routes 6b's module-by-
      module pass didn't touch: setup's profile install, and marketplace
      install/uninstall. Plus a real Team screen (`/team`) for an Owner
      to create a Staff account with a directly-set password (no email
      infra exists to do an invite-link) — the piece that actually makes
      "Staff role support" reachable, not just enforced. Verified live:
      a real Staff account logged in via curl gets 200 on a module it
      holds, 403 on both an instance-wide action and `/api/team`.
      63/63 tests passing.
- [x] 6c shipped 2026-11-16 — row-level ownership for the four modules
      whose `customer` role grant was already worded as "own" something
      but had nothing to scope against: a nullable `user_id` FK added
      to Invoices/Applications/Assignment submissions/Workflow requests,
      a second `.view`-gated route group per module at a distinct URI
      (not the same URI as `.manage` with different middleware — caught
      before shipping, would have made the first-registered route
      always win), each scoped to the authenticated user's own rows.
      Account creation needed no new code — the Team screen already
      handles any role, `customer` included. 67/67 tests passing,
      verified live: created a real Customer account, had the Owner
      issue one invoice to her and one to no one, logged in as her, and
      confirmed she saw exactly her own invoice and was 403'd on the
      Staff/Owner list. **Phase 6 complete** — see `PHASE_6_AUTH_DESIGN.md`.
- [x] CSRF on `/api/*` closed 2026-11-21 — Laravel 13's
      `PreventRequestForgery` middleware trusts the browser's own
      `Sec-Fetch-Site: same-origin` header first, falling back to a
      token only when that's absent. Every `fetch()` call in this shell
      is same-origin, so none of the 27 files needed to change — turned
      out simpler than the original "add a header to every fetch()
      call" plan. Verified live via curl: no origin header and no
      token → 419; `Sec-Fetch-Site: same-origin` → 201 (the real
      browser's path); a valid token with no origin header → 201 (the
      fallback a non-browser client would use). 67/67 tests still
      passing (CSRF is auto-bypassed under `php artisan test` by
      Laravel's own `runningUnitTests()` check — verification for this
      one had to be live, not unit tests).
- [x] Module schema upgrades closed 2026-11-21 — `ModuleInstaller::install()`
      now runs `migrate --path` every time it's called, not just on
      first create. Laravel's own migration repository already tracks
      which specific files have run per path, so repeating the call is
      a safe no-op for anything already applied and picks up a new
      migration file automatically — no new tracking machinery needed,
      just removing a gate that no longer served a purpose. Proven with
      a real migration file written at test time (not a mock): installed
      `notes`, confirmed a column didn't exist, dropped a new migration
      into the module's own directory simulating a later-shipped
      version, called `install()` again as a second referrer (not a
      fresh install), and confirmed the column now exists. Also verified
      live against the real dev instance the same way, then rolled the
      temp migration back and deleted it. 68/68 tests passing.
- [x] Orphaned module data purge closed 2026-11-21 —
      `ModuleInstaller::purge()`, the "separate, explicitly-confirmed,
      distinct action" `MASTER_ARCHITECTURE.md`'s non-destructive
      removal rule always said this would have to be. Refuses outright
      on anything not actually orphaned, reverses every migration the
      module's path has ever run (`migrate:rollback --step=9999`, not
      just the first batch — works correctly with the schema-upgrade
      fix above since Laravel tracks every file, not just the first
      one), releases its own capability grant, deletes the kernel
      record. Reachable only through the marketplace
      (`DELETE /api/marketplace/purge/{slug}`, `instance.manage`
      gated), with a confirmation dialog before the UI ever calls it.
      72/72 tests passing. Verified live: orphaned a module left over
      from an earlier test, purged it for real, confirmed its table
      actually dropped, the kernel record gone, and the marketplace
      listing back to looking like it was never installed.

## Explicitly deferred, not forgotten

- Multi-tenancy (OPEN in architecture doc)
- Marketplace monetization / paid modules, if ever
- Mobile app wrapper
- Federation between separate Moduloom instances
