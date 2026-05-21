@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-dark">{{ __('Profile') }}</h1>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- Update Profile Info --}}
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div class="card-body p-4">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- Update Password --}}
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div class="card-body p-4">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                {{-- Delete Account --}}
                <div class="card shadow-sm border-0 rounded-lg mb-4">
                    <div class="card-body p-4 border-start border-danger border-4">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
