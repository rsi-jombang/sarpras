<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\Request as RequestModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RequestService
{
    public function __construct(
        private readonly NumberGeneratorService $numberGenerator,
        private readonly StockService           $stockService,
    ) {}

    // -------------------------------------------------------------------------
    // Buat permintaan baru oleh unit
    // -------------------------------------------------------------------------
    public function create(User $requester, array $data): RequestModel
    {
        return DB::transaction(function () use ($requester, $data) {
            $request = RequestModel::create([
                'request_number' => $this->numberGenerator->generateRequestNumber(),
                'requested_by'   => $requester->id,
                'unit_id'        => $requester->unit_id,
                'status'         => RequestStatus::Draft,
                'priority'       => $data['priority'],
                'request_date'   => now()->toDateString(),
                'needed_date'    => $data['needed_date'] ?? null,
                'purpose'        => $data['purpose'] ?? null,
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $request->items()->create([
                    'item_id'            => $item['item_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'notes'              => $item['notes'] ?? null,
                    'status'             => 'pending',
                ]);
            }

            return $request;
        });
    }

    // -------------------------------------------------------------------------
    // Unit kirim draft → pending
    // -------------------------------------------------------------------------
    public function submit(RequestModel $request): RequestModel
    {
        $request->update(['status' => RequestStatus::Pending]);

        return $request->fresh();
    }

    // -------------------------------------------------------------------------
    // Sarpras review: approve / partial / reject
    // -------------------------------------------------------------------------
    public function review(RequestModel $request, User $reviewer, array $data): RequestModel
    {
        return DB::transaction(function () use ($request, $reviewer, $data) {
            $action = $data['action'];

            if (in_array($action, ['approve', 'partial'])) {
                foreach ($data['items'] ?? [] as $itemData) {
                    $reqItem     = $request->items()->findOrFail($itemData['request_item_id']);
                    $qtyApproved = (int) $itemData['quantity_approved'];

                    $itemStatus = match (true) {
                        $qtyApproved <= 0                           => 'rejected',
                        $qtyApproved < $reqItem->quantity_requested => 'partial',
                        default                                     => 'approved',
                    };

                    $reqItem->update([
                        'quantity_approved' => $qtyApproved,
                        'status'            => $itemStatus,
                        'rejection_reason'  => $itemData['rejection_reason'] ?? null,
                    ]);
                }
            }

            if ($action === 'reject') {
                $request->items()->each->update([
                    'status'           => 'rejected',
                    'quantity_approved' => 0,
                ]);
            }

            $newStatus = match ($action) {
                'approve' => RequestStatus::Approved,
                'partial' => RequestStatus::Partial,
                'reject'  => RequestStatus::Rejected,
            };

            $request->update([
                'status'       => $newStatus,
                'reviewed_by'  => $reviewer->id,
                'reviewed_at'  => now(),
                'review_notes' => $data['review_notes'] ?? null,
            ]);

            return $request->fresh();
        });
    }

    // -------------------------------------------------------------------------
    // Sarpras siapkan barang → status Ready (kurangi stok via StockService)
    // -------------------------------------------------------------------------
    public function fulfill(RequestModel $request, User $server): RequestModel
    {
        $this->stockService->fulfillRequest($request, $server->id);

        return $request->fresh();
    }

    // -------------------------------------------------------------------------
    // Unit konfirmasi pengambilan barang → status Completed
    // -------------------------------------------------------------------------
    public function complete(RequestModel $request, array $data): RequestModel
    {
        $request->update([
            'status'           => RequestStatus::Completed,
            'received_by_name' => $data['received_by_name'],
            'received_at'      => now(),
        ]);

        return $request->fresh();
    }

    // -------------------------------------------------------------------------
    // Unit update draft permintaan
    // -------------------------------------------------------------------------
    public function update(RequestModel $request, array $data): RequestModel
    {
        return DB::transaction(function () use ($request, $data) {
            $request->update([
                'priority'     => $data['priority'],
                'needed_date'  => $data['needed_date'] ?? null,
                'purpose'      => $data['purpose'] ?? null,
                'notes'        => $data['notes'] ?? null,
            ]);

            // Hapus items lama, buat ulang dari data baru
            $request->items()->delete();
            foreach ($data['items'] as $item) {
                $request->items()->create([
                    'item_id'            => $item['item_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'notes'              => $item['notes'] ?? null,
                    'status'             => 'pending',
                ]);
            }

            return $request->fresh('items.item.category');
        });
    }

    // -------------------------------------------------------------------------
    // Unit batalkan permintaan yang masih Pending
    // -------------------------------------------------------------------------
    public function cancel(RequestModel $request): RequestModel
    {
        $request->update(['status' => RequestStatus::Cancelled]);

        return $request->fresh();
    }
}
