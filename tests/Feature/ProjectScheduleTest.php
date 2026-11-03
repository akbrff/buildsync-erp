<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(string $code = 'PRJ-001'): Project
    {
        return Project::create([
            'project_code' => $code,
            'name' => 'Proyek '.$code,
            'start_date' => '2026-11-01',
            'budget' => 100000000,
            'status' => 'ongoing',
        ]);
    }

    private function validData(Project $project, array $overrides = []): array
    {
        return array_merge([
            'project_id' => $project->id,
            'name' => 'Pekerjaan Pondasi',
            'description' => 'Galian dan pengecoran pondasi.',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'weight' => 40,
        ], $overrides);
    }

    private function makeSchedule(Project $project, float $weight = 40, string $name = 'Pekerjaan A'): ProjectSchedule
    {
        return $project->schedules()->create([
            'name' => $name,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'weight' => $weight,
        ]);
    }

    public function test_schedule_pages_can_be_accessed(): void
    {
        $this->withoutVite();
        $project = $this->makeProject();
        $schedule = $this->makeSchedule($project);

        $this->get(route('schedules.index'))->assertStatus(200)->assertSee('Pekerjaan A');
        $this->get(route('schedules.create'))->assertStatus(200);
        $this->get(route('schedules.show', $schedule))->assertStatus(200)->assertSee('Pekerjaan A');
        $this->get(route('schedules.edit', $schedule))->assertStatus(200);
    }

    public function test_schedule_can_be_stored(): void
    {
        $project = $this->makeProject();

        $response = $this->post(route('schedules.store'), $this->validData($project));

        $response->assertRedirect(route('schedules.index', ['project_id' => $project->id]));
        $this->assertDatabaseHas('project_schedules', [
            'project_id' => $project->id,
            'name' => 'Pekerjaan Pondasi',
        ]);
    }

    public function test_name_is_required(): void
    {
        $project = $this->makeProject();

        $response = $this->post(route('schedules.store'), $this->validData($project, ['name' => '']));

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('project_schedules', 0);
    }

    public function test_end_date_cannot_be_before_start_date(): void
    {
        $project = $this->makeProject();

        $response = $this->post(route('schedules.store'), $this->validData($project, [
            'start_date' => '2026-11-10',
            'end_date' => '2026-11-09',
        ]));

        $response->assertSessionHasErrors('end_date');
        $this->assertDatabaseCount('project_schedules', 0);
    }

    public function test_weight_must_be_between_zero_and_one_hundred(): void
    {
        $project = $this->makeProject();

        $this->post(route('schedules.store'), $this->validData($project, ['weight' => 0]))
            ->assertSessionHasErrors('weight');
        $this->post(route('schedules.store'), $this->validData($project, ['weight' => 100.01]))
            ->assertSessionHasErrors('weight');

        $this->assertDatabaseCount('project_schedules', 0);
    }

    public function test_weight_cannot_have_more_than_two_decimals(): void
    {
        $project = $this->makeProject();

        $this->post(route('schedules.store'), $this->validData($project, ['weight' => 33.333]))
            ->assertSessionHasErrors('weight');

        $this->assertDatabaseCount('project_schedules', 0);
    }

    public function test_project_must_exist(): void
    {
        $this->post(route('schedules.store'), [
            'project_id' => 9999,
            'name' => 'Pekerjaan Hantu',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'weight' => 10,
        ])->assertSessionHasErrors('project_id');

        $this->assertDatabaseCount('project_schedules', 0);
    }

    public function test_total_weight_cannot_exceed_one_hundred_on_create(): void
    {
        $project = $this->makeProject();
        $this->makeSchedule($project, 60);

        $response = $this->post(route('schedules.store'), $this->validData($project, [
            'name' => 'Pekerjaan B',
            'weight' => 50,
        ]));

        $response->assertSessionHasErrors('weight');
        $this->assertDatabaseCount('project_schedules', 1);
    }

    public function test_total_weight_of_exactly_one_hundred_is_allowed(): void
    {
        $project = $this->makeProject();
        $this->makeSchedule($project, 60);

        $response = $this->post(route('schedules.store'), $this->validData($project, [
            'name' => 'Pekerjaan B',
            'weight' => 40,
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('project_schedules', 2);
    }

    public function test_weight_total_is_counted_per_project(): void
    {
        $projectA = $this->makeProject('PRJ-001');
        $projectB = $this->makeProject('PRJ-002');
        $this->makeSchedule($projectA, 90);

        $response = $this->post(route('schedules.store'), $this->validData($projectB, ['weight' => 90]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('project_schedules', 2);
    }

    public function test_schedule_can_be_updated_without_counting_its_own_weight(): void
    {
        $project = $this->makeProject();
        $schedule = $this->makeSchedule($project, 60);
        $this->makeSchedule($project, 40, 'Pekerjaan B');

        $response = $this->put(route('schedules.update', $schedule), $this->validData($project, [
            'name' => 'Pekerjaan A Diperbarui',
            'weight' => 60,
        ]));

        $response->assertRedirect(route('schedules.index', ['project_id' => $project->id]));
        $this->assertDatabaseHas('project_schedules', ['name' => 'Pekerjaan A Diperbarui']);
    }

    public function test_update_cannot_push_total_weight_over_one_hundred(): void
    {
        $project = $this->makeProject();
        $schedule = $this->makeSchedule($project, 60);
        $this->makeSchedule($project, 40, 'Pekerjaan B');

        $response = $this->put(route('schedules.update', $schedule), $this->validData($project, ['weight' => 70]));

        $response->assertSessionHasErrors('weight');
        $this->assertDatabaseHas('project_schedules', ['id' => $schedule->id, 'weight' => 60]);
    }

    public function test_update_cannot_change_the_project(): void
    {
        $projectA = $this->makeProject('PRJ-001');
        $projectB = $this->makeProject('PRJ-002');
        $schedule = $this->makeSchedule($projectA, 40);

        $this->put(route('schedules.update', $schedule), $this->validData($projectB));

        $this->assertSame($projectA->id, $schedule->fresh()->project_id);
    }

    public function test_schedule_can_be_deleted(): void
    {
        $project = $this->makeProject();
        $schedule = $this->makeSchedule($project);

        $response = $this->delete(route('schedules.destroy', $schedule));

        $response->assertRedirect(route('schedules.index', ['project_id' => $project->id]));
        $this->assertDatabaseMissing('project_schedules', ['id' => $schedule->id]);
    }

    public function test_index_can_be_filtered_by_project(): void
    {
        $this->withoutVite();
        $projectA = $this->makeProject('PRJ-001');
        $projectB = $this->makeProject('PRJ-002');
        $this->makeSchedule($projectA, 40, 'Pekerjaan Alpha');
        $this->makeSchedule($projectB, 40, 'Pekerjaan Beta');

        $this->get(route('schedules.index', ['project_id' => $projectA->id]))
            ->assertStatus(200)
            ->assertSee('Pekerjaan Alpha')
            ->assertDontSee('Pekerjaan Beta');
    }
}