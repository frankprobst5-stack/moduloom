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
- [ ] Security review, deployment documentation for shared hosting
- [ ] Decide public visibility (this repo starts private; the same
      due-diligence pass given to Stratum and SnapPress before they went
      public applies here too, once there's a real codebase to audit)

## Explicitly deferred, not forgotten

- Multi-tenancy (OPEN in architecture doc)
- Marketplace monetization / paid modules, if ever
- Mobile app wrapper
- Federation between separate Moduloom instances
