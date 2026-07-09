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
    public function createDraft(User $requester, array $data): RequestModel
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
    // Update DRAFT permintaan oleh unit
    // -------------------------------------------------------------------------
    public function updateDraft(RequestModel $request, array $data): RequestModel
    {
        return DB::transaction(function () use ($request, $data) {
            $request->update([
                'priority'       => $data['priority'],
                'needed_date'    => $data['needed_date'] ?? null,
                'purpose'        => $data['purpose'] ?? null,
                'notes'          => $data['notes'] ?? null,
            ]);

            $existingItemIds = $request->items->pluck('id')->toArray();
            $newItemIds = [];

            // Update atau buat item baru
            foreach ($data['items'] as $itemData) {
                if (isset($itemData['request_item_id']) && in_array($itemData['request_item_id'], $existingItemIds)) {
                    // Update item yang sudah ada
                    $reqItem = $request->items()->find($itemData['request_item_id']);
                    $reqItem->update([
                        'quantity_requested' => $itemData['quantity_requested'],
                        'notes'              => $itemData['notes'] ?? null,
                    ]);
                    $newItemIds[] = $reqItem->id;
                } else {
                    // Buat item baru
                    $newReqItem = $request->items()->create([
                        'item_id'            => $itemData['item_id'],
                        'quantity_requested' => $itemData['quantity_requested'],
                        'notes'              => $itemData['notes'] ?? null,
                        'status'             => 'pending',
                    ]);
                    $newItemIds[] = $newReqItem->id;
                }
            }

            // Hapus item yang tidak ada di request baru
            $itemsToDelete = array_diff($existingItemIds, $newItemIds);
            if (!empty($itemsToDelete)) {
                $request->items()->whereIn('id', $itemsToDelete)->delete();
            }

            return $request->fresh();
        });
    }

    // -------------------------------------------------------------------------
    // Unit submit draft → status Pending
    // -------------------------------------------------------------------------
    public function submitDraft(RequestModel $request): RequestModel
    {
        $request->update([
            'status'       => RequestStatus::Pending,
            'request_date' => now()->toDateString(), // Update tanggal saat disubmit
        ]);

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
    // Unit batalkan permintaan yang masih Pending
    // -------------------------------------------------------------------------
    public function cancel(RequestModel $request): RequestModel
    {
        $request->update(['status' => RequestStatus::Cancelled]);

        return $request->fresh();
    }
}
