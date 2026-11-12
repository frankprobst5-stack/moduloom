<?php

namespace App\Profiles;

use App\Kernel\ProfileManifest;

/**
 * This is the exact scenario MASTER_ARCHITECTURE.md's docblock for
 * invariant 3 names directly: "Community safely attach[es]
 * forums.view onto an existing Customer role created by Small
 * Business." If Community is installed on an instance that already
 * has a Customer role (from Small Business), it extends that same
 * role rather than creating a second one — and if it's installed
 * fresh, it creates Customer itself, same as Small Business does.
 */
class CommunityProfile implements ProfileManifest
{
    public function slug(): string
    {
        return 'community';
    }

    public function label(): string
    {
        return 'Community';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function modules(): array
    {
        return ['forums', 'membership', 'events', 'directories'];
    }

    public function optionalModules(): array
    {
        return [];
    }

    public function roleGrants(): array
    {
        return [
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'forums.manage', 'capability_label' => 'Moderate forums'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'membership.manage', 'capability_label' => 'Manage memberships'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'events.manage', 'capability_label' => 'Manage events'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'directories.manage', 'capability_label' => 'Manage directory listings'],

            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'forums.manage', 'capability_label' => 'Moderate forums'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'events.manage', 'capability_label' => 'Manage events'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'directories.manage', 'capability_label' => 'Manage directory listings'],

            ['role_slug' => 'customer', 'role_label' => 'Customer', 'capability_slug' => 'forums.view', 'capability_label' => 'View and post in forums'],
        ];
    }

    public function themeSlug(): string
    {
        return 'community-default';
    }
}
