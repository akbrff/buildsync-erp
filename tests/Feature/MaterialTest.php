<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialTest extends TestCase
{
    use RefreshDatabase;

    private function validMaterialData(array $overrides = []): array
    {
        return array_merge([
            'code'      => 'MAT-CMT-01',
            'name'      => 'Semen Portland Komposit (50kg)',
            'unit'      => 'Sak',
            'price'     => 68000,
            'stock'     => 100,
            'min_stock' => 20,
        ], $overrides);
    }

    public function test_material_index_page_can_be_accessed(): void
    {
        $user = User::factory()->create();
        Material::create($this->validMaterialData());

        // Default English
        $response = $this->actingAs($user)->get(route('materials.index'));
        $response->assertStatus(200);
        $response->assertSee('Materials &amp; Inventory', false);
        $response->assertSee('MAT-CMT-01');
        $response->assertSee('Semen Portland Komposit');

        // Indonesian preference
        $user->update(['locale' => 'id']);
        $responseId = $this->actingAs($user)->get(route('materials.index'));
        $responseId->assertStatus(200);
        $responseId->assertSee('Logistik &amp; Inventaris Material', false);
    }

    public function test_material_can_be_stored(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('materials.store'), $this->validMaterialData());

        $response->assertRedirect(route('materials.index'));
        $this->assertDatabaseHas('materials', [
            'code' => 'MAT-CMT-01',
            'unit' => 'Sak',
        ]);
    }

    public function test_material_code_must_be_unique(): void
    {
        $user = User::factory()->create();
        Material::create($this->validMaterialData());

        $response = $this->actingAs($user)->post(route('materials.store'), $this->validMaterialData([
            'name' => 'Duplikat Semen',
        ]));

        $response->assertSessionHasErrors('code');
    }

    public function test_material_show_and_edit_pages_can_be_accessed(): void
    {
        $user = User::factory()->create();
        $material = Material::create($this->validMaterialData());

        $this->actingAs($user)->get(route('materials.show', $material))
            ->assertStatus(200)
            ->assertSee($material->code)
            ->assertSee($material->name);

        $this->actingAs($user)->get(route('materials.edit', $material))
            ->assertStatus(200)
            ->assertSee($material->code);
    }

    public function test_material_can_be_updated(): void
    {
        $user = User::factory()->create();
        $material = Material::create($this->validMaterialData());

        $response = $this->actingAs($user)->put(route('materials.update', $material), array_merge(
            $this->validMaterialData(),
            ['name' => 'Semen Portland Gresik 50kg', 'price' => 72000]
        ));

        $response->assertRedirect(route('materials.index'));
        $this->assertDatabaseHas('materials', [
            'id'    => $material->id,
            'name'  => 'Semen Portland Gresik 50kg',
            'price' => 72000,
        ]);
    }

    public function test_material_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $material = Material::create($this->validMaterialData());

        $response = $this->actingAs($user)->delete(route('materials.destroy', $material));

        $response->assertRedirect(route('materials.index'));
        $this->assertDatabaseMissing('materials', [
            'id' => $material->id,
        ]);
    }

    public function test_material_stock_can_be_adjusted_in(): void
    {
        $user = User::factory()->create();
        $material = Material::create($this->validMaterialData(['stock' => 50]));

        $response = $this->actingAs($user)->post(route('materials.adjust', $material), [
            'type'   => 'in',
            'amount' => 30,
            'notes'  => 'Pengiriman Supplier SJ-102',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(80, $material->fresh()->stock);
    }

    public function test_material_stock_can_be_adjusted_out(): void
    {
        $user = User::factory()->create();
        $material = Material::create($this->validMaterialData(['stock' => 50]));

        $response = $this->actingAs($user)->post(route('materials.adjust', $material), [
            'type'   => 'out',
            'amount' => 20,
            'notes'  => 'Pengecoran Lantai 2',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(30, $material->fresh()->stock);
    }

    public function test_material_stock_cannot_be_adjusted_out_beyond_available(): void
    {
        $user = User::factory()->create();
        $material = Material::create($this->validMaterialData(['stock' => 10]));

        $response = $this->actingAs($user)->post(route('materials.adjust', $material), [
            'type'   => 'out',
            'amount' => 15,
            'notes'  => 'Permintaan Berlebih',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(10, $material->fresh()->stock);
    }
}
