<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Http\Requests\Request\CompleteRequestRequest;
use App\Http\Requests\Request\ReviewRequestRequest;
use App\Http\Requests\Request\StoreRequestRequest;
use App\Models\Item;
use App\Models\Request as RequestModel;
use App\Services\RequestService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function __construct(
        private readonly RequestService $requestService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', RequestModel::class);

        return view('requests.index');
    }

    public function create(): View
    {
        $this->authorize('create', RequestModel::class);

        return view('requests.create');
    }

    public function store(StoreRequestRequest $request): RedirectResponse
    {
        $this->authorize('create', RequestModel::class);

        $req = $this->requestService->create($request->user(), $request->validated());

        return redirect()->route('requests.show', $req)
            ->with('success', 'Draft permintaan ' . $req->request_number . ' berhasil disimpan. Silakan kirim setelah selesai.');
    }

    // -------------------------------------------------------------------------
    // Unit kirim draft → pending
    // -------------------------------------------------------------------------
    public function submit(Request $httpRequest, RequestModel $request): RedirectResponse
    {
        $this->authorize('submit', $request);

        $this->requestService->submit($request);

        return redirect()->route('requests.show', $request)
            ->with('success', 'Permintaan ' . $request->request_number . ' berhasil dikirim dan menunggu review Sarpras.');
    }

    public function show(RequestModel $request): View
    {
        $this->authorize('view', $request);

        $request->load([
            'requester', 'unit', 'reviewer', 'server',
            'items.item.category', 'items.itemStock',
        ]);

        return view('requests.show', compact('request'));
    }

    public function pdf(RequestModel $request): \Illuminate\Http\Response
    {
        $this->authorize('view', $request);

        $request->load([
            'requester', 'unit', 'reviewer', 'server',
            'items.item.category', 'items.itemStock',
        ]);

        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoBase64;
        } else {
            $logo = null;
        }

        $pdf = Pdf::loadView('requests.pdf', compact('request', 'logo'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        return $pdf->download('bukti-permintaan-' . $request->request_number . '.pdf');
    }

    public function reviewForm(RequestModel $request): View
    {
        $this->authorize('review', $request);

        $request->load(['requester', 'unit', 'items.item.category', 'items.itemStock']);

        return view('requests.review', compact('request'));
    }

    public function review(ReviewRequestRequest $httpRequest, RequestModel $request): RedirectResponse
    {
        $this->authorize('review', $request);

        $this->requestService->review($request, $httpRequest->user(), $httpRequest->validated());

        $label = match ($httpRequest->action) {
            'approve' => 'disetujui',
            'partial' => 'disetujui sebagian',
            'reject'  => 'ditolak',
        };

        return redirect()->route('requests.show', $request)
            ->with('success', "Permintaan {$request->request_number} berhasil {$label}.");
    }

    public function fulfill(Request $httpRequest, RequestModel $request): RedirectResponse
    {
        $this->authorize('fulfill', $request);

        $this->requestService->fulfill($request, $httpRequest->user());

        return redirect()->route('requests.show', $request)
            ->with('success', 'Barang sudah disiapkan. Menunggu pengambilan oleh unit.');
    }

    public function complete(CompleteRequestRequest $httpRequest, RequestModel $request): RedirectResponse
    {
        $this->authorize('complete', $request);

        $this->requestService->complete($request, $httpRequest->validated());

        return redirect()->route('requests.show', $request)
            ->with('success', 'Permintaan selesai. Barang telah diterima.');
    }

    public function edit(RequestModel $request): View
    {
        $this->authorize('update', $request);

        $request->load('items.item.category');

        return view('requests.edit', compact('request'));
    }

    public function update(StoreRequestRequest $httpRequest, RequestModel $request): RedirectResponse
    {
        $this->authorize('update', $request);

        $this->requestService->update($request, $httpRequest->validated());

        return redirect()->route('requests.show', $request)
            ->with('success', 'Draft permintaan ' . $request->request_number . ' berhasil diperbarui.');
    }

    public function cancel(Request $httpRequest, RequestModel $request): RedirectResponse
    {
        $this->authorize('cancel', $request);

        $this->requestService->cancel($request);

        return redirect()->route('requests.index')
            ->with('success', 'Permintaan berhasil dibatalkan.');
    }
}
