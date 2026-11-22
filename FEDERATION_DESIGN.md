# Federation Between Separate Moduloom Instances — Design Scope

Status: **shipped in full 2026-11-22** — Fed-a, Fed-b, and Fed-c all
complete, see the build order below for what each stage actually did.
`MASTER_ROADMAP.md`'s "Explicitly
deferred" list has carried "Federation between separate Moduloom
instances" as a single, unelaborated bullet since it was first written
— no description anywhere of what it should actually do. This document
does the same thing `PHASE_6_AUTH_DESIGN.md` did for auth: ground the
design in what the codebase and `MASTER_VISION.md` actually say before
proposing a mechanism, rather than reaching for the generic
federation pattern (ActivityPub-style identity/follows/replies) by
default.

## What "federation" can't mean here

`MASTER_VISION.md` is explicit and this has to be read as a hard
constraint, not a preference:

- **"Not a SaaS."** The deployment target is the owner's own shared
  hosting or self-hosted server; "the owner holds their own data on
  their own server." Any federation design that requires a central
  directory, hub, or discovery service that Moduloom-the-project would
  have to run is itself a SaaS component and contradicts this on its
  face.
- **Every kernel table is explicitly instance-scoped** (confirmed
  throughout `MASTER_ARCHITECTURE.md`, reinforced by the multi-tenancy
  decision) — roles, capabilities, users, installed modules. There is
  no existing concept anywhere in this codebase of one instance's
  identity meaning anything to another instance. Phase 6 built real
  per-instance accounts with zero cross-instance identity, by design.
- **No outbound network client exists anywhere in the kernel today.**
  Every module's data lives and dies locally. Federation is a genuinely
  new capability, not an extension of something half-built.

Given this, full social-federation (shared identity, cross-instance
follows/likes/replies, something ActivityPub-shaped) would mean solving
cross-instance trust and identity — its own project on the scale of
Phase 6, and in tension with "the owner holds their own data." That's
not what this document scopes.

## What federation *can* mean here, grounded in what already exists

Three modules already produce genuinely public content, unauthenticated,
through `ThemeManager` — confirmed directly in the routes, not assumed:

| Module | Model | Public route |
|---|---|---|
| Website | `WebsitePage` (title, slug, content) | `GET /site/{slug}` |
| Knowledge base | `Article` (title, slug, category, content) | `GET /kb/{slug}` |
| Public information | `Notice` (title, body, published_at) | `GET /notices` |

This content is already public to anyone who reaches the instance, with
no login and no capability check. Federating it — letting a *different*
instance's Owner choose to mirror and display it too — adds no new
privacy or trust surface beyond what already exists; it's the same
content, just reachable from a second front door. That makes this the
natural, honestly-scoped v1, modeled on RSS/Atom syndication rather
than full social federation: one instance publishes a feed of its own
already-public content, another instance's Owner opts in to pulling
from it.

Not included in v1, and why:
- **Events and Directories** (Community ecosystem) have no public page
  at all today — only `.manage`-gated admin endpoints. Federating them
  would mean building public pages for them first, which is separate,
  prior scope, not a federation design question.
- **Any module behind a capability check** (everything else) stays
  instance-local, full stop. Federation only ever touches content that
  was already public before federation existed.
- **Identity/accounts** — explicitly out of scope, per the constraint
  above. A Customer's login on instance A means nothing on instance B.

## Design decisions

### 1. Mechanism: pull-based syndication, no central service
Each instance exposes its own public content as a feed; peer instances
*pull* from it on their own schedule. No directory, no hub, no
discovery service — an Owner adds a peer by typing in its URL, the same
way they'd subscribe to an RSS feed. This is the only mechanism
consistent with "not a SaaS": nothing exists that Moduloom-the-project
operates or could take down.

Push (webhook-notify-on-publish) was considered and rejected for v1:
more real-time, but adds delivery/retry complexity for a feature whose
content already tolerates a sync delay (none of Website/KB/Public
information is time-critical in the way, say, a chat message is).

### 2. Outbound: one public JSON feed endpoint per federatable module
`GET /federation/website.json`, `/federation/knowledge-base.json`,
`/federation/public-information.json` — unauthenticated, same trust
level as the public pages themselves, returning this instance's own
records: `title`, `slug` (where the model has one), `content`/`body`,
`published_at` (Notices already have it; Pages/Articles get `updated_at`
as a stand-in), and a `canonical_url` pointing back to this instance's
own public page for that item. An instance's own identity (its base
URL, a display name from config) is included once in the feed envelope,
not repeated per item.

No new capability gate on these endpoints — the content has none today,
and adding one here would be stricter than the same data's existing
public page, which makes no sense.

### 3. Inbound: Owner-managed peer list, opt-in per content type
A new kernel table, `federation_peers`: `base_url`, `label`,
`subscribed_content_types` (a small set — `website`, `knowledge-base`,
`public-information`), `added_at`. Managed through the same
`instance.manage` capability as the Team screen and Marketplace
install/uninstall — this is the same class of instance-wide
configuration action, not a new capability worth inventing.

A scheduled job (Laravel's own scheduler, already available, nothing
new to add) fetches each subscribed peer's relevant feed(s) and upserts
into a second new table, `federated_content`: `peer_id`, `content_type`,
`title`, `body`, `canonical_url`, `published_at`, `fetched_at`. Keyed on
`(peer_id, canonical_url)` so re-fetching is idempotent, matching this
kernel's existing idempotent-everywhere convention (`firstOrCreate`
throughout `Reconciler`).

### 4. Display: always attributed, never silently merged
The public pages for Website/Knowledge base/Public information show
federated content from subscribed peers alongside local content, but
every federated item is visibly labeled with its origin instance's name
and links out to the `canonical_url` on the real originating instance —
never presented as if it were this instance's own content. This isn't
just good practice; it's the same principle invariant 2's "additive,
not re-provisioning" already enforces for modules, applied to content
instead of schema.

## Decisions (Frank, 2026-11-22)

1. **Granularity of opt-in: per content type.** Confirmed — matches how
   the rest of this kernel already works (capabilities, module grants
   are all granular by default). `federation_peers.subscribed_content_types`
   above already reflects this.
2. **Ship Website/KB/Public-information first, extend to
   Events/Directories later.** Confirmed — the stronger motivating case
   (several Community-ecosystem instances cross-listing a shared events
   calendar) needs Events to have a public page first, which is
   separate, prior scope, not a reason to hold up what's already
   buildable. v1 stays exactly the three modules in the table above;
   Events/Directories federation is a real, named v2 once those modules
   have public pages of their own — not a sign v1 is incomplete.

3. **Priority: yes, build it now.** Confirmed 2026-11-22 — separate from
   the mobile wrapper (still blocked on app-store developer accounts,
   not being pulled forward by this).

## Open questions for Frank

None remaining — all three resolved above.

## Build order

- **Fed-a: shipped 2026-11-22.** Outbound feed endpoints for the three
  existing public modules — no peer concept yet, just making the data
  federatable. `App\Kernel\FederationFeed::respond()` is the one shared
  envelope (`instance`, `content_type`, `items`); each module maps its
  own model into the common item shape
  (`title`/`slug`/`content`/`published_at`/`canonical_url`) and hands it
  there, same pattern as every other kernel/module boundary in this
  codebase. `GET /federation/{website,knowledge-base,public-information}.json`,
  same `module.active` gate as each module's own public page (an
  orphaned module's feed 404s exactly like its page would), no new
  auth — the content has none today on its public page either. Notices
  have no per-item detail page, so their `canonical_url` is the
  `/notices` listing with a `#notice-{id}` fragment, the closest honest
  answer given what actually exists. 76/76 tests passing; verified live
  against the real dev instance with a cold `curl` (no cookies, no
  session at all) confirming a real page shows up with zero auth.
- **Fed-b: shipped 2026-11-22.** `federation_peers` (base URL, label,
  per-content-type opt-in, `last_fetched_at`) and `federated_content`
  (unique on peer + canonical URL, so re-fetching is idempotent —
  Reconciler's own `firstOrCreate`/unique-constraint convention, applied
  to content instead of schema). `App\Kernel\FederationFetcher` pulls
  each subscribed peer's feed(s) and upserts; an unreachable or broken
  peer is skipped, never fatal, so one bad peer can't block fetching
  from the rest. `php artisan federation:fetch` runs it, scheduled
  hourly in `routes/console.php`; peer management
  (`GET/POST /api/federation/peers`, `DELETE .../{peer}`,
  `POST .../{peer}/fetch` for an immediate pull) sits behind
  `instance.manage`, same as Team and Marketplace install/uninstall.
  81/81 tests passing (peer HTTP responses faked with `Http::fake()` —
  the standard way to test an outbound HTTP client deterministically,
  not a shortcut on correctness; the pipeline itself, including
  idempotency and failure handling, runs for real against that faked
  response). One bug the tests caught before anything shipped:
  `last_fetched_at` wasn't in the model's `$fillable`, so the update
  was silently no-op'd by mass-assignment protection — the test
  asserting it got set is what caught this, not inspection.

  Live verification needed a real second process, not the originally-
  planned self-peering trick: a single-worker dev server deadlocks
  trying to serve an outbound request to itself while it's still
  handling the inbound one that triggered it. Fixed the dev environment
  for real along the way — `.env`'s `PHP_CLI_SERVER_WORKERS` was
  present but commented out; enabled it, which is a legitimate fix
  regardless of federation (any future concurrent-request scenario
  would hit the same deadlock). Even with that fixed, still verified
  against a genuinely separate second `php artisan serve` process on a
  different port rather than self-peering, since two distinct processes
  is the actually-representative case. Confirmed for real: created a
  page on the "peer," triggered a fetch from the real instance, got
  `{"fetched": 1, ...}`, and found the cached row in `federated_content`
  with the correct `content_type`, `title`, `canonical_url`, and
  `published_at`. All test data and the second process cleaned up
  afterward.
- **Fed-c: shipped 2026-11-22.** Public-page display of federated
  content, with attribution — plus a real management UI for peers,
  which Fed-b had only exposed through the raw API. A genuine
  constraint shaped this one: Website and Knowledge base have no public
  *listing* page at all (`/site/{slug}` and `/kb/{slug}` are direct
  lookups only), so there was no existing page to blend their federated
  content into the way `/notices` already had one. `/notices`
  (`PublicNoticesController::index`) now shows a "From other instances"
  section alongside local notices, each item linking back to its real
  origin via `canonical_url`. A new kernel-level page,
  `/federation/browse`, is the display surface for everything else —
  grouped by content type, same attribution — public, no module tied to
  it since it isn't one module's content. New `/federation` screen
  (Dashboard sidebar link, `instance.manage` gated like Team and
  Marketplace) lets an Owner actually add/remove peers and trigger an
  immediate fetch, rather than needing raw `curl` against the API Fed-b
  shipped.

  85/85 tests passing. Verified live with a real second
  `php artisan serve` process again (same lesson from Fed-b): seeded it
  with a real notice and a real page, added it as a peer from the real
  instance's actual Federation screen (verified in the browser, zero
  console errors), fetched for real, and confirmed both `/notices` and
  `/federation/browse` render the federated items with correct
  attribution and working links back to the origin — with zero
  authentication on either page, same trust level as the content they
  mirror. All test data and the second process cleaned up afterward.
  **Federation (Fed-a/b/c) is complete.**
