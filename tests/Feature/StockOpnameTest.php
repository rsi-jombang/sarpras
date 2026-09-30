<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\User;
use App\Services\StockOpnameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOpnameTest extends TestCase
{
    use RefreshDatabase;

    private function makeSarpras(): User
    {
        return User::factory()->create([
            'role'      => UserRole::Sarpras,
            'is_active' => true,
        ]);
    }

    private function makeItemWithBatches(array $quantities): Item
    {
        $category = Category::create([
            'name'      => 'Alkes ' . uniqid(),
            'code'      => 'CAT-' . uniqid(),
            'item_type' => 'consumable',
        ]);

        $item = Item::create([
            'category_id'     => $category->id,
            'name'            => 'Item ' . uniqid(),
            'code'            => 'ITM-' . uniqid(),
            'unit_of_measure' => 'pcs',
            'item_type'       => 'consumable',
        ]);

        $daysAgo = count($quantities) + 1;
        foreach ($quantities as $qty) {
            $stock = new ItemStock(['item_id' => $item->id, 'quantity' => $qty]);
            $stock->created_at = now()->subDays($daysAgo--);
            $stock->save();
        }

        return $item->fresh();
    }

    private function service(): StockOpnameService
    {
        return app(StockOpnameService::class);
    }

    public function test_create_snapshots_one_row_per_item_with_total(): void
    {
        $user = $this->makeSarpras();
        $item = $this->makeItemWithBatches([10, 5]);

        $opname = $this->service()->create($user, ['opname_date' => now()->toDateString()]);

        $this->assertEquals(1, $opname->items()->count());

        $row = $opname->items()->first();
        $this->assertEquals($item->id, $row->item_id);
        $this->assertNull($row->item_stock_id);
        $this->assertEquals(15, $row->system_quantity);
        $this->assertNull($row->physical_quantity);
    }

    public function test_cannot_create_while_another_is_open(): void
    {
        $user = $this->makeSarpras();
        $this->service()->create($user, ['opname_date' => now()->toDateString()]);

        $this->expectException(\RuntimeException::class);
        $this->service()->create($user, ['opname_date' => now()->toDateString()]);
    }

    public function test_close_applies_deficit_to_oldest_batch_first(): void
    {
        $user = $this->makeSarpras();
        $item = $this->makeItemWithBatches([10, 5]); // total 15, tertua = 10

        $opname  = $this->service()->create($user, ['opname_date' => now()->toDateString()]);
        $row     = $opname->items()->first();
        $oldest  = ItemStock::where('item_id', $item->id)->orderBy('created_at', 'asc')->first();
        $newest  = ItemStock::where('item_id', $item->id)->orderBy('created_at', 'desc')->first();

        $this->service()->updateItems($opname, [
            'items' => [[
                'opname_item_id'    => $row->id,
                'physical_quantity' => 12, // selisih -3
            ]],
        ], $user);

        $this->service()->close($opname, $user);

        $this->assertEquals(7, $oldest->fresh()->quantity);
        $this->assertEquals(5, $newest->fresh()->quantity);

        $movement = $opname->stockMovements()->first();
        $this->assertNotNull($movement);
        $this->assertEquals('adjustment', $movement->movement_type->value);
        $this->assertEquals(-3, $movement->quantity);
        $this->assertEquals($oldest->id, $movement->item_stock_id);

        $row->refresh();
        $this->assertTrue((bool) $row->is_adjusted);
        $this->assertEquals($oldest->id, $row->item_stock_id);
        $this->assertEquals('closed', $opname->fresh()->status);
    }

    public function test_close_applies_surplus_to_oldest_batch(): void
    {
        $user = $this->makeSarpras();
        $item = $this->makeItemWithBatches([10, 5]); // total 15

        $opname = $this->service()->create($user, ['opname_date' => now()->toDateString()]);
        $row    = $opname->items()->first();

        $this->service()->updateItems($opname, [
            'items' => [[
                'opname_item_id'    => $row->id,
                'physical_quantity' => 20, // selisih +5
            ]],
        ], $user);

        $this->service()->close($opname, $user);

        $this->assertEquals(20, ItemStock::where('item_id', $item->id)->sum('quantity'));
        $this->assertEquals(
            5,
            $opname->stockMovements()->sum('quantity')
        );
    }

    public function test_close_twice_throws_runtime_exception(): void
    {
        $user = $this->makeSarpras();
        $this->makeItemWithBatches([10]);

        $opname = $this->service()->create($user, ['opname_date' => now()->toDateString()]);
        $row    = $opname->items()->first();

        $this->service()->updateItems($opname, [
            'items' => [[
                'opname_item_id'    => $row->id,
                'physical_quantity' => 10,
            ]],
        ], $user);

        $this->service()->close($opname, $user);

        $this->expectException(\RuntimeException::class);
        $this->service()->close($opname->fresh(), $user);
    }

    public function test_update_items_records_counter(): void
    {
        $user = $this->makeSarpras();
        $this->makeItemWithBatches([10]);

        $opname = $this->service()->create($user, ['opname_date' => now()->toDateString()]);
        $row    = $opname->items()->first();

        $this->service()->updateItems($opname, [
            'items' => [[
                'opname_item_id'    => $row->id,
                'physical_quantity' => 9,
            ]],
        ], $user);

        $this->assertEquals($user->id, $row->fresh()->counted_by);
        $this->assertEquals($user->name, $row->fresh()->counter->name);
    }

    public function test_opname_pages_render_for_sarpras(): void
    {
        $user = $this->makeSarpras();
        $this->makeItemWithBatches([10]);

        $opname = $this->service()->create($user, ['opname_date' => now()->toDateString()]);

        $this->actingAs($user)->get(route('stock-opname.index'))->assertOk();
        $this->actingAs($user)->get(route('stock-opname.show', $opname))->assertOk();
        $this->actingAs($user)->get(route('stock-opname.close.confirm', $opname))->assertOk();
    }

    public function test_store_second_opname_redirects_with_error(): void
    {
        $user = $this->makeSarpras();
        $this->makeItemWithBatches([10]);

        $this->service()->create($user, ['opname_date' => now()->toDateString()]);

        $this->actingAs($user)
            ->post(route('stock-opname.store'), ['opname_date' => now()->toDateString()])
            ->assertRedirect(route('stock-opname.index'))
            ->assertSessionHas('error');
    }

    public function test_sync_items_adds_missing_items(): void
    {
        $user = $this->makeSarpras();
        $item = $this->makeItemWithBatches([4]);

        $opname = $this->service()->create($user, ['opname_date' => now()->toDateString()]);
        $opname->items()->where('item_id', $item->id)->delete();

        $this->actingAs($user)
            ->post(route('stock-opname.sync-items', $opname))
            ->assertRedirect(route('stock-opname.show', $opname))
            ->assertSessionHas('success');

        $this->assertEquals(
            4,
            $opname->items()->where('item_id', $item->id)->value('system_quantity')
        );
    }
}
