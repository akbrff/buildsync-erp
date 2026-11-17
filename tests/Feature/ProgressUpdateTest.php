<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchedule(): ProjectSchedule
    {
        $project = Project::create([
            'project_code' => 'PRJ-001',
            'name' => 'Proyek Uji',
            'start_date' => '2026-11-01',
            'budget' => 100000000,
            'status' => 'ongoing',
        ]);

        return $project->schedules()->create([
            'name' => 'Pekerjaan Pondasi',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'weight' => 40,
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'progress_percentage' => 30,
            'recorded_at' => now()->toDateString(),
            'notes' => 'Pekerjaan berjalan lancar.',
        ], $overrides);
    }

    private function seedProgress(ProjectSchedule $schedule, float $percentage, string $date, ?string $notes = null): void
    {
        $schedule->progressUpdates()->create([
            'progress_percentage' => $percentage,
            'recorded_at' => $date,
            'notes' => $notes,
        ]);
    }

    private function submitProgress(ProjectSchedule $schedule, array $data)
    {
        return $this->from(route('schedules.show', $schedule))
            ->post(route('schedules.progress.store', $schedule), $data);
    }

    public function test_progress_can_be_recorded(): void
    {
        $schedule = $this->makeSchedule();

        $response = $this->submitProgress($schedule, $this->payload());

        $response->assertRedirect(route('schedules.show', $schedule));
        $this->assertDatabaseHas('progress_updates', [
            'project_schedule_id' => $schedule->id,
            'progress_percentage' => 30,
        ]);
    }

    public function test_each_update_adds_a_history_row_and_keeps_old_ones(): void
    {
        $schedule = $this->makeSchedule();
        $this->seedProgress($schedule, 20, now()->subDays(3)->toDateString());

        $this->submitProgress($schedule, $this->payload(['progress_percentage' => 50]));

        $this->assertDatabaseCount('progress_updates', 2);
        $this->assertDatabaseHas('progress_updates', ['progress_percentage' => 20]);
        $this->assertDatabaseHas('progress_updates', ['progress_percentage' => 50]);
    }

    public function test_first_record_can_start_at_zero(): void
    {
        $schedule = $this->makeSchedule();

        $this->submitProgress($schedule, $this->payload(['progress_percentage' => 0]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('progress_updates', 1);
    }

    public function test_percentage_must_be_between_zero_and_one_hundred(): void
    {
        $schedule = $this->makeSchedule();

        $this->submitProgress($schedule, $this->payload(['progress_percentage' => -1]))
            ->assertSessionHasErrors('progress_percentage');
        $this->submitProgress($schedule, $this->payload(['progress_percentage' => 100.01]))
            ->assertSessionHasErrors('progress_percentage');

        $this->assertDatabaseCount('progress_updates', 0);
    }

    public function test_percentage_cannot_have_more_than_two_decimals(): void
    {
        $schedule = $this->makeSchedule();

        $this->submitProgress($schedule, $this->payload(['progress_percentage' => 33.333]))
            ->assertSessionHasErrors('progress_percentage');

        $this->assertDatabaseCount('progress_updates', 0);
    }

    public function test_recorded_at_is_required(): void
    {
        $schedule = $this->makeSchedule();

        $this->submitProgress($schedule, $this->payload(['recorded_at' => '']))
            ->assertSessionHasErrors('recorded_at');

        $this->assertDatabaseCount('progress_updates', 0);
    }

    public function test_recorded_at_cannot_be_in_the_future(): void
    {
        $schedule = $this->makeSchedule();

        $this->submitProgress($schedule, $this->payload(['recorded_at' => now()->addDay()->toDateString()]))
            ->assertSessionHasErrors('recorded_at');

        $this->assertDatabaseCount('progress_updates', 0);
    }

    public function test_recorded_at_cannot_be_earlier_than_latest_record(): void
    {
        $schedule = $this->makeSchedule();
        $this->seedProgress($schedule, 20, now()->subDay()->toDateString());

        $this->submitProgress($schedule, $this->payload([
            'progress_percentage' => 40,
            'recorded_at' => now()->subDays(3)->toDateString(),
        ]))->assertSessionHasErrors('recorded_at');

        $this->assertDatabaseCount('progress_updates', 1);
    }

    public function test_same_date_as_latest_record_is_allowed(): void
    {
        $schedule = $this->makeSchedule();
        $this->seedProgress($schedule, 20, now()->toDateString());

        $this->submitProgress($schedule, $this->payload(['progress_percentage' => 40]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('progress_updates', 2);
    }

    public function test_lower_progress_requires_correction_flag(): void
    {
        $schedule = $this->makeSchedule();
        $this->seedProgress($schedule, 60, now()->subDay()->toDateString());

        $this->submitProgress($schedule, $this->payload(['progress_percentage' => 40]))
            ->assertSessionHasErrors('is_correction');

        $this->assertDatabaseCount('progress_updates', 1);
    }

    public function test_lower_progress_requires_notes(): void
    {
        $schedule = $this->makeSchedule();
        $this->seedProgress($schedule, 60, now()->subDay()->toDateString());

        $this->submitProgress($schedule, $this->payload([
            'progress_percentage' => 40,
            'is_correction' => 1,
            'notes' => '',
        ]))->assertSessionHasErrors('notes');

        $this->assertDatabaseCount('progress_updates', 1);
    }

    public function test_lower_progress_is_accepted_as_a_recorded_correction(): void
    {
        $schedule = $this->makeSchedule();
        $this->seedProgress($schedule, 60, now()->subDay()->toDateString());

        $this->submitProgress($schedule, $this->payload([
            'progress_percentage' => 40,
            'is_correction' => 1,
            'notes' => 'Salah input sebelumnya.',
        ]))->assertSessionHasNoErrors();

        $this->assertDatabaseCount('progress_updates', 2);
        $this->assertDatabaseHas('progress_updates', [
            'progress_percentage' => 40,
            'is_correction' => 1,
        ]);
    }

    public function test_equal_or_higher_progress_needs_no_correction(): void
    {
        $schedule = $this->makeSchedule();
        $this->seedProgress($schedule, 60, now()->subDay()->toDateString());

        $this->submitProgress($schedule, $this->payload(['progress_percentage' => 60]))
            ->assertSessionHasNoErrors();
        $this->submitProgress($schedule, $this->payload(['progress_percentage' => 80]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('progress_updates', 3);
    }

    public function test_show_page_displays_latest_progress_and_history(): void
    {
        $this->withoutVite();
        $schedule = $this->makeSchedule();
        $this->seedProgress($schedule, 20, now()->subDays(5)->toDateString());
        $this->seedProgress($schedule, 70, now()->subDay()->toDateString());

        $this->get(route('schedules.show', $schedule))
            ->assertStatus(200)
            ->assertSee('Latest Progress')
            ->assertSee('Progress History')
            ->assertSee('70,00%')
            ->assertSee('20,00%');

        // Test Indonesian locale
        $user = auth()->user();
        $user->update(['locale' => 'id']);
        $this->get(route('schedules.show', $schedule))
            ->assertStatus(200)
            ->assertSee('Progres Terakhir')
            ->assertSee('Riwayat Progres');
        $user->update(['locale' => 'en']);
    }

    public function test_history_is_ordered_newest_first(): void
    {
        $this->withoutVite();
        $schedule = $this->makeSchedule();
        $this->seedProgress($schedule, 20, now()->subDays(5)->toDateString(), 'Catatan lama');
        $this->seedProgress($schedule, 70, now()->subDay()->toDateString(), 'Catatan baru');

        $this->get(route('schedules.show', $schedule))
            ->assertSeeInOrder(['Catatan baru', 'Catatan lama']);
    }

    public function test_progress_cannot_be_recorded_for_missing_schedule(): void
    {
        $this->post(route('schedules.progress.store', 9999), $this->payload())
            ->assertStatus(404);
    }
}