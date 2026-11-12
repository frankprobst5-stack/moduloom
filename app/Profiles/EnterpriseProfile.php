<?php

namespace App\Profiles;

use App\Kernel\ProfileManifest;

class EnterpriseProfile implements ProfileManifest
{
    public function slug(): string
    {
        return 'enterprise';
    }

    public function label(): string
    {
        return 'Enterprise';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function modules(): array
    {
        return ['documents', 'workflows', 'projects', 'applications'];
    }

    public function optionalModules(): array
    {
        return [];
    }

    public function roleGrants(): array
    {
        return [
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'documents.manage', 'capability_label' => 'Manage documents'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'workflows.manage', 'capability_label' => 'Process workflow requests'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'projects.manage', 'capability_label' => 'Manage projects'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'applications.manage', 'capability_label' => 'Review applications'],

            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'documents.manage', 'capability_label' => 'Manage documents'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'workflows.manage', 'capability_label' => 'Process workflow requests'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'projects.manage', 'capability_label' => 'Manage projects'],

            // Customer here means "external applicant/requester": can
            // submit and track their own applications and requests.
            ['role_slug' => 'customer', 'role_label' => 'Customer', 'capability_slug' => 'applications.view', 'capability_label' => 'Submit and view own applications'],
            ['role_slug' => 'customer', 'role_label' => 'Customer', 'capability_slug' => 'workflows.view', 'capability_label' => 'Submit and view own requests'],
        ];
    }

    public function themeSlug(): string
    {
        return 'enterprise-default';
    }
}
