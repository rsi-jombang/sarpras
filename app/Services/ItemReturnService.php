<?php

namespace App\Services;

use App\Models\ItemReturn;
use App\Models\ItemReturnItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ItemReturnService
{
    public function __construct(
        private readonly NumberGeneratorService $numberGenerator,
        private readonly StockService $stockService,
    ) {}

    public function create(User $creator, array $data): ItemReturn
    {
        return DB::transaction(function () use ($creator, $data) {
            $ret = ItemReturn::create([
                'return_number' => $this->numberGenerator->generateReturnNumber(),
                'created_by'    => $creator->id,
                'unit_id'       => $data['unit_id'] ?? $creator->unit_id,
                'status'        => 'draft',
                'reason'        => $data['reason'] ?? null,
                'notes'         => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $ret->items()->create([
                    'request_item_id'  => $item['request_item_id'] ?? null,
                    'item_id'          => $item['item_id'],
                    'quantity_returned' => $item['quantity_returned'],
                    'condition'        => $item['condition'] ?? 'usable',
                    'notes'            => $item['notes'] ?? null,
                ]);
            }

            return $ret->fresh('items');
        });
    }

    public function update(ItemReturn $r, array $data): ItemReturn
    {
        return DB::transaction(function () use ($r, $data) {
            $r->update([
                'unit_id'   => $data['unit_id'] ?? $r->unit_id,
                'reason'    => $data['reason'] ?? null,
                'notes'     => $data['notes'] ?? null,
            ]);

            $r->items()->delete();
            foreach ($data['items'] as $item) {
                $r->items()->create([
                    'request_item_id'   => $item['request_item_id'] ?? null,
                    'item_id'           => $item['item_id'],
                    'quantity_returned' => $item['quantity_returned'],
                    'condition'         => $item['condition'] ?? 'usable',
                    'notes'             => $item['notes'] ?? null,
                ]);
            }
            return $r->fresh('items');
        });
    }

    public function submit(ItemReturn $r): ItemReturn
    {
        $r->update(['status' => 'submitted', 'submitted_at' => now()]);
        return $r->fresh();
    }

    public function receive(ItemReturn $r, User $receiver): ItemReturn
    {
        $this->stockService->receiveReturn($r, $receiver->id);
        $r->refresh();
        $r->update([
            'status'       => 'received',
            'received_by'  => $receiver->id,
            'received_at'  => now(),
        ]);
        return $r->fresh();
    }

    public function cancel(ItemReturn $r, ?User $by = null, string $reason = ''): ItemReturn
    {
        $r->update([
            'status'        => 'cancelled',
            'cancelled_by'  => $by?->id,
            'cancelled_at'  => now(),
            'cancel_reason' => $reason ?: $r->cancel_reason,
        ]);
        return $r->fresh();
    }

    public function delete(ItemReturn $r): void
    {
        $r->items()->delete();
        $r->delete();
    }
}