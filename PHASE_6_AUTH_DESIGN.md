# Phase 6 — Authentication & Authorization: Design Scope

Status: **6a and 6b shipped 2026-11-15/16**, including a kernel-level
`instance.manage` capability and a real Staff account-creation screen —
see below. **6c (Customer tier) not started.** This document grounds
the design in what actually existed in the codebase before this pass —
not the generic "add a login screen" version of this task — and records
the decisions made, per `SECURITY.md`'s recommendation that this be a
deliberate phase rather than a bolted-on fix.

## What's actually there today

Verified directly against the current code, not assumed from the roadmap note:

- **Identity exists but is dormant.** `users` table and the `User` model
  (`app/Models/User.php`) are the untouched Laravel scaffold from Phase 0
  — `name`/`email`/`password`, `Authenticatable`, nothing more. No login
  controller, no session guard usage, no Sanctum/Breeze/Jetstream/Passport
  installed. `config/auth.php`'s `web` session guard is the stock
  default, wired to nothing.
- **Authorization is modeled and actively used, but only for
  provisioning.** `roles`, `capabilities`, and `role_capability_grants`
  (migrated 2026-11-12) are real, instance-scoped per invariant 3, and
  every ecosystem profile populates them on install — e.g. installing
  Small Business creates an `owner` role with `crm.manage`,
  `invoicing.manage`, etc. But nothing ever *checks* a capability before
  serving a request. These tables answer "what can the `owner` role do,"
  never "who is logged in and which role are they."
- **There is no link between the two.** No `user_role_assignments` table
  or equivalent exists anywhere. A `User` row and a `Role` row have
  never been connected in this codebase.
- **Every route is open.** Confirmed by reading `routes/api.php` and all
  22 modules' own route files directly — zero `auth` middleware, zero
  `Gate::`/`->authorize()` calls anywhere in `app/` or `modules/`. This
  matches `SECURITY.md`'s finding exactly.
- **The `customer` role's capabilities are already scoped to "own"
  records**, and this wasn't called out explicitly anywhere before now:
  `invoicing.view` → "View **own** invoices" (Small Business),
  `applications.view` → "Submit and view **own** applications"
  (Enterprise), `assignments.view` → "View and submit **own**
  assignments" (Education), `workflows.view` → "Submit and view **own**
  requests" (Government/Enterprise). Five ecosystems define a `customer`
  role this way; Personal defines no `staff` or `customer` role at all —
  it's `owner`-only by design.

That last point matters: Phase 6 isn't just "who's logged in" — the
Customer tier specifically needs **row-level ownership**, not just a
role-level capability check. A customer who can see *an* invoice because
their role has `invoicing.view` but no ownership check would see
*everyone's* invoices. None of the four modules behind those "own"
capabilities (Invoicing, Applications, Assignments, Workflows) currently
have an owning-user column — that's new schema work this phase, not
previously scoped anywhere.

## Design decisions

### 1. Identity → role link
New `user_role_assignments` table: `user_id`, `role_slug`, timestamps.
Many-to-many (a user can hold more than one role — e.g. the instance
owner is also, trivially, able to see their own stuff everywhere) rather
than a single `role_id` column on `users`. Costs nothing extra and
matches the project's existing pattern of append-only join tables
(`profile_install_history`, `theme_selections`, `role_capability_grants`
itself) over a single mutable foreign key.

### 2. Session vs. token auth
**Session-based**, using Laravel's already-scaffolded `web` guard — not
Sanctum, not a separate token layer. Every admin screen (Dashboard,
Marketplace, Setup, and now Customer-facing views) is same-origin,
cookie-friendly React served by the same Laravel app per
`DEPLOYMENT.md`'s shared-hosting model. There's no second client (mobile
app, third-party integration) consuming this API yet — `MASTER_ROADMAP.md`
explicitly defers a mobile wrapper. Adding token auth now would be
solving a problem this project doesn't have; the login screen becomes
just another blade+entry+component triplet in the existing style
(`LoginController` + `login.blade.php` + `Login.tsx` +
`login-entry.tsx`), consistent with how Picker/Onboarding/Dashboard are
already built, using Laravel's built-in `Auth::attempt()` — no new
dependency required for this part.

### 3. Customer accounts: real, not anonymous
Given finding above — Customer capabilities are ownership-scoped — a
Customer identity has to be stable across sessions and devices for
"my own invoices" to mean anything. That rules out cookie-only or
device-bound identity. Recommendation: Customers get real `User` rows
too, reusing the exact same table, login flow, and
`user_role_assignments` link as Owner/Staff — not a second, parallel
identity system. Whether sign-up is self-serve (a customer registers
themselves, e.g. a forum member) or invite-only (a student account
created by Staff, e.g. Education) varies *by ecosystem*, not by the auth
system itself — that's a per-profile onboarding detail, not a schema
difference.

### 4. Enforcement mechanism
A capability-checking middleware (e.g. `EnsureUserHasCapability`,
parameterized with a capability slug) applied **per-route inside each
module's own route file** — not a central kernel-wide route/capability
map. This matches the existing convention that "each module registers
its own API routes via its own service provider" (`routes/api.php`'s own
comment) — capability requirements stay co-located with the routes they
guard. Row-level ownership (decision above) is a second, narrower check
inside the four affected controllers, not the middleware's job.

### 5. Personal profile's login requirement — open question
Personal has only an `owner` role, no `staff`/`customer` — it's
explicitly single-owner. Forcing a login screen in front of a
single-user local instance adds friction the other five ecosystems
don't share this problem with. Two reasonable paths, and this is a
product call, not an engineering one — see the question below.

## Build order

- **6a — close the hole. Shipped 2026-11-15.** Session login/logout,
  applied globally. See "What 6a actually shipped" above.
- **6b — capability enforcement. Shipped 2026-11-15.** See "What 6b
  actually shipped" below.
- **6c — Customer tier.** Self-serve/invited Customer accounts, plus the
  ownership columns and row-level scoping on Invoicing, Applications,
  Assignments, and Workflows. Not started.
- **Deferred candidates (not blocking a real deployment):** password
  reset flow, email verification, invite-link UX polish — real work, but
  none of it is the thing standing between here and "safe to deploy."

## Decisions (Frank, 2026-11-15)

1. **Personal profile requires login too** — one consistent auth model
   across all six ecosystems, no special-cased exception.
2. **This pass builds 6a only** — close the hole, nothing more. 6b
   (capability enforcement, Staff) and 6c (Customer tier, row-level
   ownership) stay future work.

## What 6a actually shipped

- `AuthController` (`showRegister`/`register`/`showLogin`/`login`/
  `logout`) using `Auth::attempt()`/`Auth::login()` against the existing
  `web` session guard — no new dependency.
- Bootstrap flow: `/` now checks, in order, "does any `User` exist" →
  `/register` if not; "is there an active session" → `/login` if not;
  then the original fresh-profile-vs-dashboard logic. `/register`
  closes itself (404s) the instant one `User` row exists — there is
  deliberately no multi-account concept yet, so this is a one-time
  bootstrap screen, not a sign-up form.
- `routes/web.php`'s admin pages (`/setup`, `/dashboard`, `/marketplace`)
  sit behind `Route::middleware('auth')`.
- Every `/api/*` route — `routes/api.php` and all 22 modules' own route
  files — is gated globally from `bootstrap/app.php`, not touched file
  by file. No Owner/Staff distinction yet: this is a single all-or-
  nothing gate, matching the "6a only" scope decision above.
- Login/register are plain server-rendered Blade forms (not another
  React/fetch screen) — the right call specifically for this pair,
  since a login flow is a one-shot redirect-and-cookie round trip, not
  an interactive SPA screen like the rest of the shell. Styled with the
  same brand tokens as everything else.
- A real "Log out" control now exists in the Dashboard sidebar and
  Marketplace header — there was previously no way to end a session
  from the UI at all, since there was no session.

**Real bug found while wiring this up, not anticipated in the design
above:** Laravel's `api` middleware group is stateless by default (no
`EncryptCookies`/`StartSession`) — that's normally Sanctum's job to add
for an SPA talking to its own session-authenticated API. Appending
`Authenticate::class` alone made every `/api/*` call 401 even with a
valid session cookie, confirmed independently via `curl` with a cookie
jar before touching the browser. Fixed by prepending
`EncryptCookies`/`AddQueuedCookiesToResponse`/`StartSession` to the
`api` group ahead of the auth check (`bootstrap/app.php`) — gets
Sanctum's stateful-SPA effect without the package, consistent with
decision #2 above (no token layer for a single-client app).

**Known, deliberate gap carried forward:** the `api` group still has no
CSRF check — only `SameSite=Lax` (Laravel's default) stands between a
cross-site page and a forged state-changing request against it. That's
a reasonable baseline, not full hardening. Closing it properly means
sending a CSRF header from every one of the existing `fetch()` calls in
Dashboard/Marketplace/Picker — real but small work, worth doing before
a non-local deployment, intentionally not bundled into this pass.

All 56 tests pass (47 pre-existing + 9 new in
`tests/Feature/Auth/AuthTest.php`), including explicit coverage of
unauthenticated rejection on both `/api/*` and the admin pages.
Verified live end-to-end in the browser against the real persistent dev
instance: fresh install → register → setup → dashboard (nav and theme
panel both load real data) → log out → `/dashboard` bounces to `/login`
→ log back in.

## What 6b actually shipped

- `user_role_assignments` (`user_id`, `role_slug`, many-to-many) links
  identity to the roles that already existed. `User::roles()`,
  `Role::users()`, and `User::hasCapability(string $slug): bool` (true
  if any assigned role has a grant for it) are the only new query
  surface.
- `EnsureUserHasCapability` (alias `capability`) sits alongside
  `module.active` on every one of the 22 modules' own route files —
  `Route::middleware(['module.active:notes', 'capability:notes.manage'])`
  — not a central kernel-wide map, same reasoning as `module.active`
  itself: the requirement stays next to the route it guards.
- `AuthController::register` now also does `Role::firstOrCreate('owner')`
  and assigns the new user to it — the 'owner' role_slug is shared
  across every profile (invariant 3), and registration is simply the
  first thing in the instance's life that needs it to exist.

**Real gap found and fixed, not anticipated in the design above:**
`ModuleManifest::capabilities()` has existed since Phase 1, but nothing
ever granted those capabilities to anyone — a comment in
`ModuleInstaller` said as much, correctly, at the time ("Phase 1 doesn't
grant them to any role yet — that's a profile's job... there are no
profiles until Phase 2"). Five phases later that deferral had become a
live bug: a module installed standalone — through the marketplace, or a
direct `ModuleInstaller::install()` call — was never granted to anyone,
Owner included, because only a *profile's* `roleGrants()` ever attached
capability grants. Enforcing capabilities for real (this phase) turned
a silent gap into every marketplace-only module being unusable by
everyone. Fixed at the source: `ModuleInstaller::install()` now grants
the module's own declared capabilities to `owner` unconditionally,
sourced as `module:{slug}` — a distinct source from any profile's own
grant for the same capability, so they coexist (invariant 3) rather than
conflict, and non-destructive on uninstall (an orphaned module's grant
survives, same rule as the module row itself; `module.active` alone
still blocks real access). `tests/Feature/Kernel/CapabilityEnforcementTest.php`
covers this directly; `EvolutionTest`'s existing assertions about exactly
which sources grant `website.manage`/`crm.manage` were updated to
include it, not relaxed.

**What "Staff role support" meant at this point, precisely:** the
enforcement mechanism was real and complete — a user assigned to
`staff` was granted or refused exactly per that role's capability
grants, same as `owner`. What didn't exist yet was any way for an Owner
to actually create a Staff account and assign them the role. Closed
right after — see "What shipped after 6b" below.

58/58 tests pass (56 prior + 2 new in `CapabilityEnforcementTest`), and
the standalone-module gap was verified closed live against the real dev
instance via `curl` with a real session cookie: installing `notes`
through `/api/marketplace/install/notes` as the existing Owner, then
immediately calling `/api/modules/notes/notes`, returns 200 — not the
403 it would have returned before the `ModuleInstaller` fix. The
migration also backfills: an instance that registered its owner under
6a, before this table existed, has that existing user assigned to
`owner` automatically — verified on the real persistent dev instance,
not just a fresh test database.

## What shipped after 6b, same day (2026-11-15/16)

Two things Frank explicitly asked for once 6b's gaps were visible:

**A kernel-level `instance.manage` capability**, granted to `owner` at
registration (same mechanism as any module granting its own capability
— `Reconciler::grantCapability`), now required on the three kernel
routes that were gated only by "logged in" and nothing else: the setup
wizard's profile install, and marketplace install/uninstall. These sit
in `routes/api.php` directly, not inside any module's own route file,
so the per-module `capability:{slug}` sed pass never touched them — a
real gap 6b's own module-by-module framing missed. A backfill migration
grants it to any pre-existing `owner` role too.

**A Team screen** (`/team`, `TeamController`, `Team.tsx`) — the Staff
account-creation flow flagged as missing right after 6b shipped. Owner
sets the password directly (Frank's call: no invite-link, this app has
no real outbound mail). Lists current accounts and their roles; the
"create account" form only offers roles that already exist (a role
with zero capabilities granted would be pointless to assign), and is
itself gated by `instance.manage` — so a Staff account can see a module
it's been granted, but can't see or add to the team, and can't install
or remove anything instance-wide. All three boundaries verified for
real against the dev instance: logged in as the newly-created Staff
account via `curl`, confirmed a granted module returns 200, an
instance-wide action returns 403, and `/api/team` returns 403.

This is what actually closes "Staff role support end-to-end" — 6b built
the enforcement, this is the account-creation flow that makes it
reachable.

## Open follow-ups

- CSRF on `/api/*` (noted under 6a).
- 6c, not started: Customer tier and row-level ownership.
