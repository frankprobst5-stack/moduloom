# Moduloom — Master Vision

## What this is

Moduloom is a single, open-source web application platform. It is not six
products wearing one name — it is one Laravel/PHP backend, one React +
TypeScript admin experience, one MariaDB schema convention, and one module
system, all shared by every installation regardless of what that
installation is used for.

What makes an installation feel like "a CRM" or "a school site" or "a
forum" is entirely a matter of **which modules are installed and which
permissions are granted** — never a different codebase, a different fork,
or a different product.

An **ecosystem** (Small Business, Education, Community, Enterprise,
Personal, Government) is nothing more than a named, curated starting
bundle: a list of modules to install, sensible default configuration, a
starter theme, and a default role/permission set. Six ecosystems ship as
six install-time presets. Under the hood there is exactly one platform.

## Why this exists

Directus, Strapi, Appsmith, Nextcloud, and NocoDB each solved one real
piece of this problem well — data modeling and APIs, modular content
management, application building, a self-hosted ecosystem of first-party
apps, and friendly database interfaces, respectively. None of them ship
the idea at the center of Moduloom: **that the bundle itself is the
product you distribute**, and that a user's starting bundle should never
become a ceiling.

## The one idea worth protecting

> **Never make someone abandon their installation because their needs
> changed.**

A Personal installation — a website, some notes, some files, some tasks —
is not a different product from a Small Business installation. It is the
same installation with fewer modules installed. The owner of that
Personal site should be able to add a CRM, Inventory, and Invoicing to it
six months later and keep every note, every file, every published page,
exactly as it was. No migration. No export/import. No "upgrade to our
business tier." Just more modules, installed onto the same instance.

This is the single claim in this document that the rest of the
architecture exists to serve. If a future decision makes this claim
false or awkward, that decision is wrong, no matter how convenient it is
elsewhere.

## Who this is for

| Ecosystem | Audience | Starting capabilities |
|---|---|---|
| Small Business | Sole proprietors, local shops, small service businesses (1–20 employees) | Website, CRM, Inventory, Invoicing |
| Education | Schools, classrooms, tutors | School website, Classrooms, Assignments, Knowledge base |
| Community | Clubs, hobby groups, membership organizations | Forums, Membership, Events, Directories |
| Enterprise | Teams that need structured internal process | Documents, Workflows, Projects, Applications |
| Personal | Individuals, freelancers, hobbyists | Website, Notes, Files, Tasks, Personal databases |
| Government | Public agencies, municipal offices | Public information, Forms, Records, Workflows |

Any of these can add modules from any other ecosystem at any time. The
table describes a starting point, never a boundary.

## What this is not

- **Not a fork or extension of Stratum or SnapPress.** Both are real,
  separate, previously-shipped projects in this builder's portfolio.
  Moduloom shares no code, no database schema, and no architectural
  assumptions with either. It is a ground-up build, deliberately, because
  the module/profile model described here could not be retrofitted onto
  either project's existing design without a rewrite indistinguishable
  from starting over.
- **Not a page builder or static site generator.** The Website module is
  one module among many, not the center of gravity.
- **Not a SaaS.** The deployment target is GitHub-hosted source, deployed
  by the owner to their own shared hosting (e.g. BlueHost, DreamHost) or
  equivalent. The owner holds their own data on their own server.
- **Not a live-synced product.** Installing an ecosystem is a point-in-time
  action, not a subscription to an ever-changing bundle. See
  `MASTER_ARCHITECTURE.md` for why this is a hard invariant, not a
  simplification.

## How "done" looks, eventually

A person picks an ecosystem, installs it in under two minutes on cheap
shared hosting, and has something real and usable immediately. A year
later, their needs have changed twice, and the same installation has
quietly grown to match — never rebuilt, never migrated, never abandoned.
