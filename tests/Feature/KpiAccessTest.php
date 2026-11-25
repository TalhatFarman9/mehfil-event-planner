<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/kpi')->assertRedirect('/login');
    }

    public function test_non_admin_is_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user)->get('/admin/kpi/data')->assertForbidden();
    }

    public function test_admin_gets_kpi_json(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->getJson('/admin/kpi/data')
            ->assertOk()
            ->assertJsonStructure(['summary' => ['total_bookings', 'total_revenue'], 'labels', 'bookings', 'revenue']);
    }
}