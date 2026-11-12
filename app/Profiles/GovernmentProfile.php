<?php

namespace App\Profiles;

use App\Kernel\ProfileManifest;

class GovernmentProfile implements ProfileManifest
{
    public function slug(): string
    {
        return 'government';
    }

    public function label(): string
    {
        return 'Government';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function modules(): array
    {
        // Reuses workflows per MASTER_ROADMAP.md rather than shipping a
        // second approval-request module — citizens submitting a
        // request to the city and an employee requesting approval
        // internally are the same underlying capability.
        return ['public-information', 'forms', 'records', 'workflows'];
    }

    public function optionalModules(): array
    {
        return [];
    }

    public function roleGrants(): array
    {
        return [
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'public_information.manage', 'capability_label' => 'Publish public notices'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'forms.manage', 'capability_label' => 'Manage forms and submissions'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'records.manage', 'capability_label' => 'Manage public records'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'workflows.manage', 'capability_label' => 'Process workflow requests'],

            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'public_information.manage', 'capability_label' => 'Publish public notices'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'forms.manage', 'capability_label' => 'Manage forms and submissions'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'records.manage', 'capability_label' => 'Manage public records'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'workflows.manage', 'capability_label' => 'Process workflow requests'],

            // Customer here means "citizen": can submit a request
            // (e.g. a permit application) and track its status — the
            // same workflows.view capability Enterprise already grants
            // its own Customer role, reused rather than reinvented.
            ['role_slug' => 'customer', 'role_label' => 'Customer', 'capability_slug' => 'workflows.view', 'capability_label' => 'Submit and view own requests'],
        ];
    }

    public function themeSlug(): string
    {
        return 'government-default';
    }
}
