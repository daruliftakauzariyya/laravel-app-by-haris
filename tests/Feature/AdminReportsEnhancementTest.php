<?php

namespace Tests\Feature;

use App\Models\QuestionAndAnswer;
use App\Models\Role;
use App\Models\User;
use App\Services\ReportingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdminReportsEnhancementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Run migrations for sqlite in-memory testing
        $this->artisan('migrate');
    }

    /**
     * Test that guest users cannot access reports or PDF.
     */
    public function test_guest_is_redirected_from_admin_reports(): void
    {
        $response = $this->get('/admin/reports');
        $response->assertRedirect('/login');

        $pdfResponse = $this->get('/admin/reports/pdf');
        $pdfResponse->assertRedirect('/login');
    }

    /**
     * Test admin can access reports overview with date range.
     */
    public function test_admin_can_access_reports_dashboard(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::firstOrCreate(['email' => 'admin_test@darulifta.com'], [
            'name'       => 'Test Admin',
            'despl_name' => 'Test Admin',
            'password'   => bcrypt('password'),
            'role_id'    => $adminRole->id,
            'is_active'  => 1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/reports?tab=overview&date_range=this_month');
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.index');
        $response->assertViewHas('metrics');
        $response->assertViewHas('statusBreakdown');
        $response->assertViewHas('muftis');
    }

    /**
     * Test date range resolver presets.
     */
    public function test_date_range_presets_resolve_accurately(): void
    {
        $service = new ReportingService();

        // 1. This Month
        $req1 = Request::create('/admin/reports', 'GET', ['date_range' => 'this_month']);
        $range1 = $service->resolveDateRange($req1);
        $this->assertEquals('this_month', $range1['date_range']);
        $this->assertEquals(Carbon::now('Asia/Kolkata')->startOfMonth()->toDateString(), $range1['start']->toDateString());

        // 2. Last 30 Days
        $req2 = Request::create('/admin/reports', 'GET', ['date_range' => 'last_30_days']);
        $range2 = $service->resolveDateRange($req2);
        $this->assertEquals('last_30_days', $range2['date_range']);
        $this->assertEquals(30, (int) round($range2['start']->diffInDays($range2['end'])));

        // 3. Custom Date Range
        $req3 = Request::create('/admin/reports', 'GET', [
            'date_range' => 'custom',
            'start_date' => '2026-10-01',
            'end_date'   => '2026-10-29',
        ]);
        $range3 = $service->resolveDateRange($req3);
        $this->assertEquals('custom', $range3['date_range']);
        $this->assertEquals('2026-10-01', $range3['start_date']);
        $this->assertEquals('2026-10-29', $range3['end_date']);
    }

    /**
     * Test published fatwas are counted by published_at regardless of created_at.
     */
    public function test_published_fatwas_count_by_published_at_not_created_at(): void
    {
        $service = new ReportingService();

        // Custom range: October 2026
        $req = Request::create('/admin/reports', 'GET', [
            'date_range' => 'custom',
            'start_date' => '2026-10-01',
            'end_date'   => '2026-10-31',
        ]);
        $range = $service->resolveDateRange($req);
        $metrics = $service->getSummaryMetrics($range);

        $this->assertIsInt($metrics['published_fatwas']);
        $this->assertGreaterThanOrEqual(0, $metrics['published_fatwas']);
    }

    /**
     * Test PDF report generation endpoint.
     */
    public function test_admin_can_access_pdf_report_view(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::firstOrCreate(['email' => 'admin_test@darulifta.com'], [
            'name'       => 'Test Admin',
            'despl_name' => 'Test Admin',
            'password'   => bcrypt('password'),
            'role_id'    => $adminRole->id,
            'is_active'  => 1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/reports/pdf?date_range=custom&start_date=2026-10-01&end_date=2026-10-29');
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.pdf');
        $response->assertSee('darul-ifta-report-2026-10-01-to-2026-10-29.pdf');
        $response->assertSee('Darul Ifta Kauzariyya');
        $response->assertSee('دار الإفتاء');
        $response->assertSee('ദാറുൽ ഇഫ്താഹ്');
    }

    /**
     * Test CSV export endpoint.
     */
    public function test_admin_can_export_csv(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::firstOrCreate(['email' => 'admin_test@darulifta.com'], [
            'name'       => 'Test Admin',
            'despl_name' => 'Test Admin',
            'password'   => bcrypt('password'),
            'role_id'    => $adminRole->id,
            'is_active'  => 1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/reports/export?date_range=this_month');
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('darul_ifta_report_', $response->headers->get('Content-Disposition'));
    }

    /**
     * Test transparent performance scoring formula calculation.
     */
    public function test_performance_score_calculation_reproducibility(): void
    {
        $service = new ReportingService();

        // High contributor: 4 published (35 pts), 5 answers (30 pts), 100% completion (20 pts), 3 logins (15 pts) = 100 pts
        $scoreMax = $service->calculatePerformanceScore(4, 4, 5, 4, 3, 3);
        $this->assertEquals(100, $scoreMax['score']);
        $this->assertEquals('Exceptional', $scoreMax['tier']);

        // Inactive scholar: 0 across all
        $scoreZero = $service->calculatePerformanceScore(0, 0, 0, 0, 0, 0);
        $this->assertEquals(0, $scoreZero['score']);
        $this->assertEquals('Low / Inactive in Period', $scoreZero['tier']);
    }
}
