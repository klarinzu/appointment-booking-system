@extends('adminlte::master')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('adminlte_css')
    @include('backend.partials.documate-theme')
    @stack('css')
    @yield('css')
@stop

@section('classes_body'){{ ($auth_type ?? 'login') . '-page' }}@stop

@section('body')
    @php
        $authType = $auth_type ?? 'login';
        $panelLogo = trim((string) config('adminlte.logo_img', ''));
        $logoLabel = trim(strip_tags((string) config('adminlte.logo', 'LNU DOCUMATE')));
        $authCopy = match ($authType) {
            'register' => [
                'eyebrow' => 'Student Onboarding',
                'title' => 'Create your LNU DOCUMATE student account.',
                'body' => 'Register once so your Leyte Normal University student record, VPSD transactions, appointment schedules, and clearance updates stay in one place.',
            ],
            'passwords.email' => [
                'eyebrow' => 'Account Recovery',
                'title' => 'Reset access without leaving the LNU DOCUMATE workflow.',
                'body' => 'Use your registered email address to recover access and continue your VPSD transaction progress.',
            ],
            default => [
                'eyebrow' => 'Secure Access',
                'title' => 'Sign in to continue your LNU DOCUMATE transactions.',
                'body' => 'Track approvals, open official forms, schedule appointments, and review VPSD transaction history from one dashboard.',
            ],
        };
    @endphp

    <div class="doc-auth-layout">
        <aside class="doc-auth-hero">
            <div class="doc-hero-eyebrow text-white">{{ $authCopy['eyebrow'] }}</div>
            <h1 class="mb-3">{{ $authCopy['title'] }}</h1>
            <p class="mb-4">{{ $authCopy['body'] }}</p>

            <div class="doc-inline-stats mb-4">
                <div class="doc-inline-stat">
                    <strong>{{ count(config('documate.statuses', [])) }}</strong>
                    <span class="doc-note">trackable transaction stages</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ data_get(config('documate.appointments'), 'daily', 50) }}</strong>
                    <span class="doc-note">daily appointment capacity</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ count(config('documate.office_locations', [])) }}</strong>
                    <span class="doc-note">guided office destinations</span>
                </div>
            </div>

            <div class="doc-auth-tip mb-3">
                <strong>What students can do inside LNU DOCUMATE</strong>
                <div class="doc-note">Request eligible forms, print approved documents, follow signatory instructions, book appointments, and monitor transaction progress in real time.</div>
            </div>

            <div class="doc-auth-tip">
                <strong>Appointment policy</strong>
                <div class="doc-note">LNU DOCUMATE accepts 25 morning and 25 afternoon appointments each day so students can choose a manageable schedule.</div>
            </div>
        </aside>

        <div class="doc-auth-shell">
            <div class="{{ $auth_type ?? 'login' }}-box">

                {{-- Logo --}}
                <div class="{{ $auth_type ?? 'login' }}-logo mb-4">
                    <a href="{{ $dashboard_url }}" class="doc-auth-logo-link">

                        {{-- Logo Image --}}
                        @if (config('adminlte.auth_logo.enabled', false))
                            <img src="{{ asset(config('adminlte.auth_logo.img.path')) }}"
                                alt="{{ config('adminlte.auth_logo.img.alt') }}"
                                @if (config('adminlte.auth_logo.img.class', null))
                                    class="{{ config('adminlte.auth_logo.img.class') }}"
                                @endif
                                @if (config('adminlte.auth_logo.img.width', null))
                                    width="{{ config('adminlte.auth_logo.img.width') }}"
                                @endif
                                @if (config('adminlte.auth_logo.img.height', null))
                                    height="{{ config('adminlte.auth_logo.img.height') }}"
                                @endif>
                        @elseif ($panelLogo !== '')
                            <img src="{{ asset($panelLogo) }}"
                                alt="{{ config('adminlte.logo_img_alt', 'LNU DOCUMATE') }}" height="52">
                        @else
                            <span class="doc-brand-badge doc-brand-badge-xl" aria-hidden="true">LNU</span>
                        @endif

                        <span class="doc-auth-logo-copy">
                            <span class="doc-brand-title">{{ $logoLabel }}</span>
                            <span class="doc-brand-sub">Leyte Normal University</span>
                        </span>

                    </a>
                </div>

                {{-- Card Box --}}
                <div class="card {{ config('adminlte.classes_auth_card', 'card-outline card-primary') }}">

                    {{-- Card Header --}}
                    @hasSection('auth_header')
                        <div class="card-header {{ config('adminlte.classes_auth_header', '') }}">
                            <h3 class="card-title float-none text-center">
                                @yield('auth_header')
                            </h3>
                        </div>
                    @endif

                    {{-- Card Body --}}
                    <div class="card-body {{ $auth_type ?? 'login' }}-card-body {{ config('adminlte.classes_auth_body', '') }}">
                        @yield('auth_body')
                    </div>

                    {{-- Card Footer --}}
                    @hasSection('auth_footer')
                        <div class="card-footer {{ config('adminlte.classes_auth_footer', '') }}">
                            @yield('auth_footer')
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
