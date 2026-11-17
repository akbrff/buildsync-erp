<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(): Project
    {
        return Project::create([
            'project_code' => 'PRJ-BUD-001',
            'name' => 'Proyek Uji Anggaran',
            'start_date' => now()->toDateString(),
            'budget' => 500000000,
            'status' => 'planning',
        ]);
    }

    public function test_budget_can_be_created_for_a_project(): void
    {
        $project = $this->makeProject();

        $response = $this->post(route('budgets.store'), [
            'project_id' => $project->id,
            'category' => 'Material',
            'description' => 'Pembelian semen',
            'amount' => 1500000,
            'date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('budgets.index', ['project_id' => $project->id]));
        $this->assertDatabaseHas('budgets', [
            'project_id' => $project->id,
            'category' => 'Material',
            'description' => 'Pembelian semen',
            'amount' => 1500000,
        ]);
    }

    public function test_budget_requires_valid_project_and_positive_amount(): void
    {
        $response = $this->post(route('budgets.store'), [
            'project_id' => 999999,
            'category' => 'Material',
            'description' => 'Pembelian semen',
            'amount' => 0,
            'date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['project_id', 'amount']);
        $this->assertDatabaseCount('budgets', 0);
    }

    public function test_budget_can_be_updated_and_deleted(): void
    {
        $project = $this->makeProject();
        $budget = Budget::create([
            'project_id' => $project->id,
            'category' => 'Material',
            'description' => 'Pembelian semen',
            'amount' => 1500000,
            'date' => now()->toDateString(),
        ]);

        $this->put(route('budgets.update', $budget), [
            'project_id' => $project->id,
            'category' => 'Operasional',
            'description' => 'Biaya operasional',
            'amount' => 2000000,
            'date' => now()->toDateString(),
        ])->assertRedirect(route('budgets.index', ['project_id' => $project->id]));

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'category' => 'Operasional',
            'amount' => 2000000,
        ]);

        $this->delete(route('budgets.destroy', $budget))
            ->assertRedirect(route('budgets.index', ['project_id' => $project->id]));

        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    }

    public function test_project_with_budget_history_cannot_be_deleted(): void
    {
        $project = $this->makeProject();
        $budget = Budget::create([
            'project_id' => $project->id,
            'category' => 'Material',
            'description' => 'Pembelian semen',
            'amount' => 1500000,
            'date' => now()->toDateString(),
        ]);

        $this->delete(route('projects.destroy', $project))
            ->assertRedirect(route('projects.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('projects', ['id' => $project->id]);
        $this->assertDatabaseHas('budgets', ['id' => $budget->id]);
    }

    public function test_budgets_index_page_loads_with_star_admin_ui(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'finance']);
        $project = $this->makeProject();

        // English default
        $response = $this->actingAs($user)->get(route('budgets.index', ['project_id' => $project->id]));

        $response->assertOk();
        $response->assertSee('Project Budget &amp; Cash Realization', false);
        $response->assertSee('Search voucher / description...');
        $response->assertSee('rounded-2xl');
        $response->assertSee('bg-[#F8FBFD]');
        $response->assertSee('bg-[#136F9A]');
        $response->assertSee('Apply');
        $response->assertSee('No budget expense records match your filter.');
        $response->assertSee('+ Record First Expense');

        // Indonesian preference
        $user->update(['locale' => 'id']);
        $responseId = $this->actingAs($user)->get(route('budgets.index', ['project_id' => $project->id]));
        $responseId->assertOk();
        $responseId->assertSee('Realisasi Anggaran &amp; Kas Proyek', false);
        $responseId->assertSee('Cari voucher / keterangan...');
        $responseId->assertSee('Terapkan');
        $responseId->assertSee('Belum ada catatan pengeluaran kas yang sesuai filter.');
        $responseId->assertSee('+ Catat Pengeluaran Pertama');
    }
}
