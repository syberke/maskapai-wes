<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_download_revenue_excel_report(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $response = $this->actingAs($manager)->get(route('manager.reports.export.excel', ['report' => 'revenue']));

        $response->assertSuccessful();
        $response->assertDownload();
    }

    public function test_manager_can_download_revenue_pdf_report(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $response = $this->actingAs($manager)->get(route('manager.reports.export.pdf', ['report' => 'revenue']));

        $response->assertSuccessful();
        $response->assertDownload();
    }
}
