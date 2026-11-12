<?php

namespace App\Profiles;

use App\Kernel\ProfileManifest;

class EducationProfile implements ProfileManifest
{
    public function slug(): string
    {
        return 'education';
    }

    public function label(): string
    {
        return 'Education';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function modules(): array
    {
        // Reuses website per MASTER_ROADMAP.md rather than shipping a
        // second "school website" module — it's the same capability.
        return ['website', 'classrooms', 'assignments', 'knowledge-base'];
    }

    public function optionalModules(): array
    {
        return [];
    }

    public function roleGrants(): array
    {
        return [
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'website.manage', 'capability_label' => 'Manage website pages'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'classrooms.manage', 'capability_label' => 'Manage classrooms'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'assignments.manage', 'capability_label' => 'Manage assignments'],
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'knowledge_base.manage', 'capability_label' => 'Manage knowledge base articles'],

            // Staff here means "teacher": runs classrooms and
            // assignments day to day, doesn't edit the public site.
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'classrooms.manage', 'capability_label' => 'Manage classrooms'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'assignments.manage', 'capability_label' => 'Manage assignments'],
            ['role_slug' => 'staff', 'role_label' => 'Staff', 'capability_slug' => 'knowledge_base.manage', 'capability_label' => 'Manage knowledge base articles'],

            // Customer here means "student/parent": can see and submit
            // assignments, nothing more.
            ['role_slug' => 'customer', 'role_label' => 'Customer', 'capability_slug' => 'assignments.view', 'capability_label' => 'View and submit assignments'],
        ];
    }

    public function themeSlug(): string
    {
        return 'education-default';
    }
}
