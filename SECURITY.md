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

## The critical, deliberately unresolved finding

**There is no authentication or authorization enforcement anywhere in
this application.** Every route — every module's admin screen, every
module's API, the setup wizard, the dashboard, the module marketplace,
theme switching — is completely open. Anyone who can reach the server
can install or uninstall any module, create or delete any data, switch
the active theme, or install an entire ecosystem, with no login of any
kind.

This is not an oversight this review is introducing as a surprise — it's
the explicit, named OPEN item in `MASTER_ARCHITECTURE.md`
("Authentication/session model details beyond 'roles are
instance-scoped'"), carried forward deliberately since Phase 0 rather
than bolted on half-designed. The kernel's Owner/Staff/Customer roles
and their capability grants are real, correctly modeled, and
thoroughly tested — but nothing in the application layer currently
checks them against an authenticated identity, because there is no
concept of an authenticated identity yet.

**This is the one thing that must be resolved before any deployment
reachable by anyone other than its owner on their own machine.** It's
a real design decision, not a quick fix: session-based auth vs. tokens,
how a logged-in user maps to the Owner/Staff/Customer roles already
in the schema, whether Customer-role users (people outside the
business — forum members, students, citizens submitting a request)
get their own accounts at all or something lighter. Building it without
that design would mean reverting invariant 3's instance-scoped roles
into something the auth system fights, not works with.

**Recommendation:** resolve this as its own deliberate phase — call it
Phase 6 — before `MASTER_ROADMAP.md`'s "decide public visibility" item
is acted on for anything beyond a local demo. Everything else in this
review is in genuinely good shape; this is the one blocker.
