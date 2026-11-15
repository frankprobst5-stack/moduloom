# Moduloom — Security Review (Phase 5)

Status as of this review: pre-authentication, pre-public. This document
is honest about what that means — see the critical finding below before
anything here gets deployed anywhere reachable by the public internet.

## Methodology

Not a checklist exercise. A real pass through the codebase for the
concrete vulnerability classes that actually apply to this stack
(Laravel/PHP backend, React admin screens, file uploads, MariaDB),
verified by grepping for the actual anti-patterns rather than assuming
their absence, and — where something was found — fixed and proven fixed
with a real test, the same standard every other piece of this project
has been held to.

## Reviewed and found clean

- **Mass assignment.** Every model uses an explicit `$fillable` array;
  nothing uses `$guarded = []` or passes `$request->all()` straight into
  `create()`/`update()`. Every controller validates first and passes
  only the validated array through.
- **SQL injection.** No raw SQL anywhere (`DB::raw`, `whereRaw`,
  `selectRaw`, `->statement()`) — every query goes through Eloquent's
  parameterized query builder.
- **XSS in Blade.** Only four templates use unescaped output (`{!! !!}`):
  Website's and Knowledge base's public pages use
  `{!! nl2br(e($content)) !!}` — content is escaped with `e()` *first*,
  `nl2br` only inserts `<br>` tags into the already-escaped string, so
  this is safe. The two theme layouts use `{!! $content !!}` to embed an
  already-rendered, already-escaped inner View — also safe, since the
  escaping happened one layer down before the layout ever sees it.
- **XSS in React.** No `dangerouslySetInnerHTML` anywhere in any admin
  screen. User content (including Personal databases' and Forms'
  free-form JSON) is only ever rendered as text, never as HTML.
- **Command injection.** No `exec`, `shell_exec`, `system`, `passthru`,
  or `eval` anywhere in the codebase.
- **Secrets.** `.env` is gitignored and was never committed; nothing in
  this repo's history contains real credentials.

## Found and fixed

**Unrestricted file uploads (Files and Documents modules).** Both
accepted any file type with only a size cap (`max:10240`) — no
extension or content restriction. On shared hosting, where PHP
execution often can't be disabled per-directory, an unrestricted upload
is a real path to remote code execution (upload a `.php` file disguised
with any name, then request it directly) or, at minimum, stored XSS (an
uploaded `.html` file served back verbatim with a browser-interpretable
content type). Fixed by adding a `mimes:` allowlist
(`pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,gif,txt,csv,zip`) to both
controllers' validation — Laravel's `mimes` rule sniffs real file
content via PHP's fileinfo extension, not just the client-supplied
extension, so a `.php` renamed to `.pdf` still fails. Proven by
`tests/Feature/Security/FileUploadRestrictionTest.php`: a `.php` and a
`.html` upload are both rejected with 422, a real `.pdf` still succeeds.

## The critical finding — partially resolved (Phase 6a/6b, 2026-11-15)

**Originally: there was no authentication or authorization enforcement
anywhere in this application.** Every route — every module's admin
screen, every module's API, the setup wizard, the dashboard, the module
marketplace, theme switching — was completely open. Anyone who could
reach the server could install or uninstall any module, create or
delete any data, switch the active theme, or install an entire
ecosystem, with no login of any kind.

This was not an oversight this review introduced as a surprise — it was
the explicit, named OPEN item in `MASTER_ARCHITECTURE.md`
("Authentication/session model details beyond 'roles are
instance-scoped'"), carried forward deliberately since Phase 0 rather
than bolted on half-designed.

**Phase 6a closed the "anyone can reach it" hole.** Every route now
requires a logged-in session. **Phase 6b made that session mean
something**: the kernel's existing role/capability grants are now
actually enforced — `EnsureUserHasCapability` sits on every one of the
22 modules' routes, so holding a session alone isn't enough, holding the
right role is. See `PHASE_6_AUTH_DESIGN.md` for the full design and what
shipped in each.

What's still missing: a way for an Owner to actually create a Staff
account and assign it a role — the enforcement is real and complete,
but there's no invite/create-user flow anywhere in the app yet, so every
account today is functionally an Owner. That, plus the Customer tier
(6c: self-serve/invited accounts, row-level ownership on
Invoicing/Applications/Assignments/Workflows), are what's left.

**Recommendation:** 6a+6b together are enough for a single-admin
deployment (no anonymous access, and the capability model isn't just
decorative anymore). A deployment with real multiple users — Staff who
shouldn't have Owner's reach, Customers who should only see their own
records — still needs the Staff account-creation flow and 6c first.
Everything else in this review is in genuinely good shape.
