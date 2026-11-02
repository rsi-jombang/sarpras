<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemReturn;
use App\Models\ItemStock;
use App\Models\Request as RequestModel;
use App\Models\RequestItem;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\User;
use App\Services\ItemReturnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemReturnTest extends TestCase
{
    use RefreshDatabase;

    private function makeUnit(): User
    {
        $u = Unit::create(['name' => 'Unit ' . uniqid(), 'code' => 'U' . uniqid()]);
        return User::factory()->create([
            'role'      => UserRole::Unit,
            'unit_id'   => $u->id,
            'is_active' => true,
        ]);
    }

    private function makeSarpras(): User
    {
        return User::factory()->create(['role' => UserRole::Sarpras, 'is_active' => true]);
    }

    private function makeItem(): Item
    {
        $category = Category::create(['name' => 'Kat ' . uniqid(), 'code' => 'K' . uniqid(), 'item_type' => 'consumable']);
        return Item::create(['category_id' => $category->id, 'name' => 'Item ' . uniqid(), 'code' => 'I' . uniqid(), 'unit_of_measure' => 'pcs', 'item_type' => 'consumable']);
    }

    private function makeRequestItemFor(User $unit, Item $item): RequestItem
    {
        $req = RequestModel::create([
            'request_number' => 'REQ-TEST-' . uniqid(),
            'requested_by'   => $unit->id,
            'unit_id'        => $unit->unit_id,
            'status'         => 'completed',
            'priority'       => 'normal',
            'request_date'   => now()->toDateString(),
        ]);
        $stock = ItemStock::create(['item_id' => $item->id, 'quantity' => 10]);
        return RequestItem::create([
            'request_id'         => $req->id,
            'item_id'            => $item->id,
            'item_stock_id'      => $stock->id,
            'quantity_requested' => 5,
            'quantity_approved'  => 5,
            'quantity_given'     => 5,
            'status'             => 'approved',
        ]);
    }

    public function test_can_create_return_with_items_and_defaults(): void
    {
        $user = $this->makeUnit();
        $item = $this->makeItem();
        $r = app(ItemReturnService::class)->create($user, [
            'reason' => 'Kelebihan',
            'notes'  => 'test',
            'items'  => [['item_id' => $item->id, 'quantity_returned' => 5, 'notes' => 'n1']],
        ]);
        $this->assertSame('draft', $r->status);
        $this->assertSame(1, $r->items()->count());
        $this->assertSame(5, $r->items()->first()->quantity_returned);
        $this->assertStringStartsWith('RET-', $r->return_number);
    }

    public function test_submit_then_receive_adds_stock_and_movement(): void
    {
        $unit = $this->makeUnit();
        $sar  = $this->makeSarpras();
        $item = $this->makeItem();
        $ri = $this->makeRequestItemFor($unit, $item);

        $svc = app(ItemReturnService::class);
        $r = $svc->create($unit, [
            'reason' => 'Kelebihan',
            'items'  => [[
                'item_id'           => $item->id,
                'request_item_id'   => $ri->id,
                'quantity_returned' => 3,
            ]],
        ]);
        $r = $svc->submit($r);
        $this->assertSame('submitted', $r->status);

        $before = (int) ItemStock::where('item_id', $item->id)->sum('quantity');
        $svc->receive($r, $sar);
        $r = $r->fresh();
        $this->assertSame('received', $r->status);
        $this->assertSame($sar->id, $r->received_by);

        $after = (int) ItemStock::where('item_id', $item->id)->sum('quantity');
        $this->assertSame($before + 3, $after);
        $mov = StockMovement::where('reference_type', ItemReturn::class)
            ->where('reference_id', $r->id)->first();
        $this->assertNotNull($mov);
        $this->assertSame('return', $mov->movement_type->value);
        $this->assertSame(3, $mov->quantity);
    }

    public function test_cancel_does_not_touch_stock(): void
    {
        $unit = $this->makeUnit();
        $item = $this->makeItem();

        $svc = app(ItemReturnService::class);
        $r = $svc->create($unit, [
            'reason' => 'x',
            'items'  => [['item_id' => $item->id, 'quantity_returned' => 2]],
        ]);
        $before = (int) ItemStock::where('item_id', $item->id)->sum('quantity');
        $svc->cancel($r, $unit);
        $after = (int) ItemStock::where('item_id', $item->id)->sum('quantity');
        $this->assertSame($before, $after);
        $this->assertSame('cancelled', $r->fresh()->status);
    }

    public function test_policy_gates(): void
    {
        $unit  = $this->makeUnit();
        $unit2 = $this->makeUnit();
        $sar   = $this->makeSarpras();
        $admin = User::factory()->create(['role' => UserRole::Admin, 'is_active' => true]);

        $item = $this->makeItem();
        $r = app(ItemReturnService::class)->create($unit, [
            'reason' => 'x',
            'items'  => [['item_id' => $item->id, 'quantity_returned' => 1]],
        ]);
        $policy = app(\App\Policies\ReturnPolicy::class);

        $this->assertTrue($unit->can('view', $r));
        $this->assertTrue($unit->can('update', $r));
        $this->assertTrue($unit->can('submit', $r));
        $this->assertFalse($sar->can('submit', $r));
        $r2 = $r->fresh();
        $r2 = app(ItemReturnService::class)->submit($r->fresh());
        $this->assertTrue($sar->can('receive', $r2));
        $this->assertTrue($admin->can('delete', $r));
        $this->assertFalse($sar->can('delete', $r));
        $this->assertFalse($unit->can('delete', $r));
        $this->assertFalse($unit2->can('view', $r));
    }
}