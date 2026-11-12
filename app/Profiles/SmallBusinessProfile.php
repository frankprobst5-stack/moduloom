<?php

namespace App\Profiles;

use App\Kernel\ProfileManifest;

/**
 * The profile the whole project exists to prove: installed on top of
 * an existing Personal instance (Website, Notes, Files, Tasks,
 * Personal databases already live), this must not touch any of that —
 * Website is shared between both profiles, not re-provisioned, and the
 * Owner role it finds already present gets extended, never redefined.
 *
 * Creates Staff and Customer roles too, even though Customer only gets
 * one narrow capability today — a later profile (Community, Phase 4)
 * is expected to extend the same Customer role with its own grants
 * without redefining it, which only works if the role already exists.
 */
class SmallBusinessProfile implements ProfileManifest
{
    public function slug(): string
    {
        return 'small-business';
    }

    public function label(): string
    {
        return 'Small Business';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function modules(): array
    {
        return ['website', 'crm', 'inventory', 'invoicing'];
    }

    public function optionalModules(): array
    {
        return [];
    }

    public function roleGrants(): array
    {
        return [
            // Owner: everything, including website — deliberately
            // overlapping with whatever Personal already granted the
            // same role, to exercise the N:1 capability-grant rule for
            // real (two sources granting website.manage to the same
            // role; removing one must leave the other's grant intact).
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'website.manage', 'capability_label' => 'Manage website pages'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'crm.manage', 'capability_label' => 'Manage CRM contacts'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'inventory.manage', 'capability_label' => 'Manage inventory'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'invoicing.manage', 'capability_label' => 'Manage invoices'],

            // Staff: day-to-day operation, not site editing.
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'crm.manage', 'capability_label' => 'Manage CRM contacts'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'inventory.manage', 'capability_label' => 'Manage inventory'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'invoicing.manage', 'capability_label' => 'Manage invoices'],

            // Customer: can see their own invoices, nothing more yet.
            ['role_slug' => 'customer', 'role_label' => 'Customer', 'capability_slug' => 'invoicing.view', 'capability_label' => 'View own invoices'],
        ];
    }

    public function themeSlug(): string
    {
        return 'small-business-default';
    }
}
