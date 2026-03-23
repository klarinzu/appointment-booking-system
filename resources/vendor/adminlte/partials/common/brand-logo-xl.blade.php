@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@php($logo = trim((string) config('adminlte.logo_img', '')))
@php($logoXl = trim((string) (config('adminlte.logo_img_xl') ?: $logo)))
@php($logoText = trim(strip_tags((string) config('adminlte.logo', 'LNU DOCUMATE'))))

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand logo-switch {{ config('adminlte.classes_brand') }}"
    @else
        class="brand-link logo-switch {{ config('adminlte.classes_brand') }}"
    @endif>

    @if ($logo !== '')
        {{-- Small brand logo --}}
        <img src="{{ asset($logo) }}"
             alt="{{ config('adminlte.logo_img_alt', 'LNU DOCUMATE') }}"
             class="{{ config('adminlte.logo_img_class', 'brand-image-xl') }} logo-xs">

        {{-- Large brand logo --}}
        <img src="{{ asset($logoXl) }}"
             alt="{{ config('adminlte.logo_img_alt', 'LNU DOCUMATE') }}"
             class="{{ config('adminlte.logo_img_xl_class', 'brand-image-xs') }} logo-xl">
    @else
        <span class="doc-brand-badge doc-brand-badge-xl" aria-hidden="true">LNU</span>
        <span class="doc-brand-copy">
            <span class="doc-brand-title">{{ $logoText }}</span>
            <span class="doc-brand-sub">Leyte Normal University</span>
        </span>
    @endif

</a>
