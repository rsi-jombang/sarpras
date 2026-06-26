<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockAdjustmentApiController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', StockAdjustment::class);

        if ($request->ajax() && $request->has('draw')) {
            $adjustments = StockAdjustment::with(['item', 'adjustedBy'])
                ->select('stock_adjustments.*');

            return DataTables::of($adjustments)
                ->addColumn('item_name', fn($row) => $row->item->name)
                ->addColumn('adjusted_by_name', fn($row) => $row->adjustedBy->name)
                ->addColumn('action', fn($row) => $this->actionButtons($row))
                ->rawColumns(['action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    private function actionButtons(StockAdjustment $adjustment): string
    {
        $viewUrl = route('stock-adjustments.show', $adjustment);
        $deleteUrl = route('stock-adjustments.destroy', $adjustment);

        $html = "<a href='{$viewUrl}' class='btn btn-sm btn-info' title='Lihat Detail'>
                    <i class='bi bi-eye'></i>
                 </a>";

        if (auth()->user()->can('delete', $adjustment)) {
            $html .= " <form method='POST' action='{$deleteUrl}' style='display:inline;' onsubmit='return confirm(\"Yakin ingin menghapus?\");'>
                        " . csrf_field() . method_field('DELETE') . "
                        <button type='submit' class='btn btn-sm btn-danger' title='Hapus'>
                            <i class='bi bi-trash'></i>
                        </button>
                       </form>";
        }

        return $html;
    }
}
