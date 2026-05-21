@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-start
                justify-content-between gap-3 flex-wrap">
        <div>
            <h1 class="page-title">Overview Dashboard</h1>
            <p class="page-subtitle">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
        <a href="" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add New
        </a>
    </div>

@endsection
