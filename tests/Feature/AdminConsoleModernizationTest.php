<?php

namespace Tests\Feature;

use App\Models\Madhab;
use App\Models\QuestionAndAnswer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminConsoleModernizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        Cache::flush();
    }

    private function getOrCreateAdmin(): User
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        return User::firstOrCreate(['email' => 'admin_test@darulifta.com'], [
            'name' => 'Admin Tester',
            'despl_name' => 'Admin Tester',
            'password' => bcrypt('password123'),
            'role_id' => $adminRole->id,
            'is_active' => 1,
        ]);
    }

    public function test_question_management_renders_modern_dashboard_elements(): void
    {
        $admin = $this->getOrCreateAdmin();

        $response = $this->actingAs($admin)->get(route('admin.questions.index'));
        $response->assertStatus(200);

        // Assert shared layout modern sidebar & header elements
        $response->assertSee('Admin Console');
        $response->assertSee('Question Management');
        $response->assertSee('DARUL IFTA');
        $response->assertSee('admin-live-clock');

        // Assert 5 modern summary metric cards are rendered
        $response->assertSee('Total Questions');
        $response->assertSee('Published to Public');
        $response->assertSee('Pending Review');
        $response->assertSee('Under Review');
        $response->assertSee('Rejected');

        // Assert horizontal filter controls
        $response->assertSee('Search by Question ID');
        $response->assertSee('All Statuses');
        $response->assertSee('All Madhabs');
        $response->assertSee('All Assignments');

        // Assert modern table headers
        $response->assertSee('Questioner');
        $response->assertSee('Question Summary');
        $response->assertSee('Assigned To');
    }

    public function test_admin_dashboard_renders_modern_signals_and_charts(): void
    {
        $admin = $this->getOrCreateAdmin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Assert modern command center elements
        $response->assertSee('Administrative Command Center');
        $response->assertSee('Knowledge Growth & Fatwa Progression');
        $response->assertSee('Live Visitors Telemetry');
        $response->assertSee('lifelongChart');
    }
}
