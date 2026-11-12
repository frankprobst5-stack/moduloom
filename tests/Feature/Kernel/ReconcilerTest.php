<?php

namespace Tests\Feature\Kernel;

use App\Kernel\Reconciler;
use App\Models\InstalledModule;
use App\Models\ModuleReference;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReconcilerTest extends TestCase
{
    use RefreshDatabase;

    private Reconciler $reconciler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reconciler = new Reconciler;
    }

    /**
     * This is the Phase 0 acceptance criterion from MASTER_ROADMAP.md,
     * verbatim: installing the same fake module via two different fake
     * profiles produces exactly two reference rows, and the module is
     * removed only when both are gone.
     */
    public function test_module_referenced_by_two_profiles_needs_both_removed(): void
    {
        $this->reconciler->installModule('fake-module', 'profile', 'profile-a');
        $this->reconciler->installModule('fake-module', 'profile', 'profile-b');

        $this->assertSame(2, $this->reconciler->referenceCount('fake-module'));
        $this->assertTrue($this->reconciler->isInstalled('fake-module'));

        $this->reconciler->removeReference('fake-module', 'profile', 'profile-a');

        $this->assertSame(1, $this->reconciler->referenceCount('fake-module'));
        $this->assertTrue(
            $this->reconciler->isInstalled('fake-module'),
            'module must survive while profile-b still references it',
        );

        $this->reconciler->removeReference('fake-module', 'profile', 'profile-b');

        $this->assertSame(0, $this->reconciler->referenceCount('fake-module'));
        $this->assertFalse(
            $this->reconciler->isInstalled('fake-module'),
            'module must become orphaned once its last referrer is gone',
        );
    }

    public function test_orphaning_never_deletes_the_module_row(): void
    {
        $this->reconciler->installModule('fake-module', 'manual', 'owner');
        $this->reconciler->removeReference('fake-module', 'manual', 'owner');

        $module = InstalledModule::find('fake-module');

        $this->assertNotNull($module, 'orphaning must not delete the row');
        $this->assertSame('orphaned', $module->status);
    }

    public function test_reapplying_the_same_referrer_is_idempotent(): void
    {
        $this->reconciler->installModule('fake-module', 'profile', 'profile-a');
        $this->reconciler->installModule('fake-module', 'profile', 'profile-a');
        $this->reconciler->installModule('fake-module', 'profile', 'profile-a');

        $this->assertSame(
            1,
            ModuleReference::where('module_slug', 'fake-module')->count(),
            'the unique constraint must prevent duplicate reference rows for the same referrer',
        );
    }

    public function test_three_reference_types_are_tracked_distinctly(): void
    {
        $this->reconciler->installModule('crm', 'profile', 'small-business');
        $this->reconciler->installModule('crm', 'manual', 'owner-added-it-directly');
        $this->reconciler->installModule('crm', 'module_dependency', 'invoicing');

        $this->assertSame(3, $this->reconciler->referenceCount('crm'));

        // Removing the profile's reference must not affect the other two.
        $this->reconciler->removeReference('crm', 'profile', 'small-business');

        $this->assertSame(2, $this->reconciler->referenceCount('crm'));
        $this->assertTrue($this->reconciler->isInstalled('crm'));
    }

    public function test_an_already_installed_module_is_never_reprovisioned(): void
    {
        $this->reconciler->installModule('website', 'profile', 'personal', version: '1.0.0');

        // A second profile references the same module. This must add a
        // reference, but must NOT touch the module's existing version —
        // that's the "create if missing, extend if present" rule from
        // invariant 2, and it's the exact bug a naive "skip if exists"
        // implementation would introduce.
        $this->reconciler->installModule('website', 'profile', 'small-business', version: '9.9.9');

        $module = InstalledModule::find('website');

        $this->assertSame('1.0.0', $module->version, 'an existing module must never be re-provisioned by a later profile');
        $this->assertSame(2, $this->reconciler->referenceCount('website'));
    }

    public function test_orphaned_module_reactivates_when_newly_referenced(): void
    {
        $this->reconciler->installModule('fake-module', 'manual', 'owner');
        $this->reconciler->removeReference('fake-module', 'manual', 'owner');

        $this->assertSame('orphaned', InstalledModule::find('fake-module')->status);

        $this->reconciler->installModule('fake-module', 'profile', 'some-profile');

        $this->assertSame('active', InstalledModule::find('fake-module')->status);
    }

    public function test_capability_grant_from_two_sources_survives_one_revocation(): void
    {
        $this->reconciler->grantCapability('customer', 'Customer', 'forums.view', 'View forums', 'small-business');
        $this->reconciler->grantCapability('customer', 'Customer', 'forums.view', 'View forums', 'community');

        $this->assertTrue($this->reconciler->roleHasCapability('customer', 'forums.view'));

        $this->reconciler->revokeGrant('customer', 'forums.view', 'small-business');

        $this->assertTrue(
            $this->reconciler->roleHasCapability('customer', 'forums.view'),
            'the community profile still grants this capability, so it must remain',
        );

        $this->reconciler->revokeGrant('customer', 'forums.view', 'community');

        $this->assertFalse($this->reconciler->roleHasCapability('customer', 'forums.view'));
    }

    public function test_role_is_extended_not_redefined_by_a_second_profile(): void
    {
        $this->reconciler->grantCapability('customer', 'Customer', 'invoices.view', 'View invoices', 'small-business');
        $this->reconciler->grantCapability('customer', 'Customer', 'forums.view', 'View forums', 'community');

        $this->assertTrue($this->reconciler->roleHasCapability('customer', 'invoices.view'));
        $this->assertTrue($this->reconciler->roleHasCapability('customer', 'forums.view'));
        $this->assertSame(1, Role::count(), 'only one Customer role row must ever exist');
    }

    /**
     * Phase 3's real acceptance criterion, proven here in miniature:
     * installing Personal, then Small Business on top, then removing
     * Small Business, must leave every Personal-owned module and grant
     * completely untouched.
     */
    public function test_removing_a_profile_leaves_a_shared_module_and_other_profiles_grants_intact(): void
    {
        $this->reconciler->applyProfile('personal', '1.0.0', [
            ['module_slug' => 'website'],
            ['module_slug' => 'notes'],
        ], [
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'website.edit', 'capability_label' => 'Edit website'],
        ]);

        $this->reconciler->applyProfile('small-business', '1.0.0', [
            ['module_slug' => 'website'],
            ['module_slug' => 'crm'],
        ], [
            ['role_slug' => 'owner', 'role_label' => 'Owner', 'capability_slug' => 'crm.manage', 'capability_label' => 'Manage CRM'],
        ]);

        $this->assertTrue($this->reconciler->isInstalled('website'));
        $this->assertTrue($this->reconciler->isInstalled('notes'));
        $this->assertTrue($this->reconciler->isInstalled('crm'));

        $this->reconciler->removeProfile('small-business');

        $this->assertTrue($this->reconciler->isInstalled('website'), 'website is still referenced by the personal profile');
        $this->assertTrue($this->reconciler->isInstalled('notes'), 'notes belongs to personal and must be untouched');
        $this->assertFalse($this->reconciler->isInstalled('crm'), 'crm had no other referrer and must now be orphaned');
        $this->assertFalse($this->reconciler->roleHasCapability('owner', 'crm.manage'));
    }
}
