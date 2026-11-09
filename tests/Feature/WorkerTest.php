<?php

namespace Tests\Feature;

use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkerTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'worker_code' => 'WRK-001',
            'name' => 'Budi Santoso',
            'position' => 'Tukang Batu',
            'phone' => '081234567890',
            'daily_rate' => 150000,
            'status' => 'active',
            'notes' => 'Pekerja lapangan.',
        ], $overrides);
    }

    public function test_worker_can_be_stored(): void
    {
        $response = $this->post(route('workers.store'), $this->validData());

        $response->assertRedirect(route('workers.index'));
        $this->assertDatabaseHas('workers', [
            'worker_code' => 'WRK-001',
            'name' => 'Budi Santoso',
        ]);
    }

    public function test_worker_code_must_be_unique(): void
    {
        Worker::create($this->validData());

        $response = $this->post(route('workers.store'), $this->validData([
            'name' => 'Orang Lain',
        ]));

        $response->assertSessionHasErrors('worker_code');
        $this->assertDatabaseCount('workers', 1);
    }

    public function test_worker_index_page_can_be_accessed(): void
    {
        $this->withoutVite();
        Worker::create($this->validData());

        $response = $this->get(route('workers.index'));

        $response->assertStatus(200);
    }

    public function test_status_must_be_active_or_inactive(): void
    {
        $response = $this->post(route('workers.store'), $this->validData([
            'status' => 'cuti',
        ]));

        $response->assertSessionHasErrors('status');
    }

    public function test_daily_rate_must_not_be_negative(): void
    {
        $response = $this->post(route('workers.store'), $this->validData([
            'daily_rate' => -1,
        ]));

        $response->assertSessionHasErrors('daily_rate');
    }

    public function test_worker_can_be_updated_with_same_code(): void
    {
        $worker = Worker::create($this->validData());

        $response = $this->put(route('workers.update', $worker), $this->validData([
            'name' => 'Budi Diperbarui',
        ]));

        $response->assertRedirect(route('workers.index'));
        $this->assertDatabaseHas('workers', ['name' => 'Budi Diperbarui']);
    }

    public function test_worker_can_be_deleted(): void
    {
        $worker = Worker::create($this->validData());

        $response = $this->delete(route('workers.destroy', $worker));

        $response->assertRedirect(route('workers.index'));
        $this->assertDatabaseMissing('workers', ['id' => $worker->id]);
    }

    public function test_worker_create_page_can_be_accessed(): void
    {
        $this->withoutVite();

        $this->get(route('workers.create'))->assertStatus(200);
    }

    public function test_worker_show_and_edit_pages_can_be_accessed(): void
    {
        $this->withoutVite();
        $worker = Worker::create($this->validData());

        $this->get(route('workers.show', $worker))->assertStatus(200)->assertSee('Budi Santoso');
        $this->get(route('workers.edit', $worker))->assertStatus(200);
    }

    public function test_worker_can_be_stored_with_formatted_phone(): void
    {
        $response = $this->post(route('workers.store'), $this->validData([
            'worker_code' => 'WRK-002',
            'phone' => '0858-1234-1234',
        ]));

        $response->assertRedirect(route('workers.index'));
        $this->assertDatabaseHas('workers', [
            'worker_code' => 'WRK-002',
            'phone' => '0858-1234-1234',
        ]);
    }

    public function test_worker_formatted_phone_attribute(): void
    {
        $worker1 = new Worker(['phone' => '085812341234']);
        $this->assertEquals('0858-1234-1234', $worker1->formatted_phone);

        $worker2 = new Worker(['phone' => '0812345678901']);
        $this->assertEquals('0812-3456-78901', $worker2->formatted_phone);

        $worker3 = new Worker(['phone' => '0812-3456-7890']);
        $this->assertEquals('0812-3456-7890', $worker3->formatted_phone);

        $worker4 = new Worker(['phone' => null]);
        $this->assertNull($worker4->formatted_phone);
    }

    public function test_worker_edit_page_formats_stored_phone(): void
    {
        $this->withoutVite();
        $worker = Worker::create($this->validData([
            'phone' => '085812341234',
        ]));

        $response = $this->get(route('workers.edit', $worker));
        $response->assertStatus(200);
        $response->assertSee('value="0858-1234-1234"', false);
    }

    public function test_worker_can_be_searched_by_phone(): void
    {
        $this->withoutVite();
        Worker::create($this->validData([
            'phone' => '0858-1234-1234',
        ]));

        // Search with hyphens
        $response1 = $this->get(route('workers.index', ['q' => '0858-1234']));
        $response1->assertStatus(200)->assertSee('0858-1234-1234');

        // Search with raw digits
        $response2 = $this->get(route('workers.index', ['q' => '08581234']));
        $response2->assertStatus(200)->assertSee('0858-1234-1234');
    }
}