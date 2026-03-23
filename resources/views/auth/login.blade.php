@extends('auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', 'Welcome Back to LNU DOCUMATE')

@section('auth_body')
    <div class="doc-auth-tip mb-4">
        <strong>Use your registered student or staff account.</strong>
        <div class="doc-note">Your dashboard will show your role, available transactions, approval status, and upcoming appointments as soon as you sign in.</div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <strong>Please check the following:</strong>
            <ul class="mb-0 pl-3 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="post">
        @csrf

        <div class="form-group">
            <label for="login-email">Email address</label>
            <div class="input-group mb-1">
                <input type="email" id="login-email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus autocomplete="email">

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <small class="doc-form-help">Use the email address tied to your LNU DOCUMATE account.</small>
        </div>

        <div class="form-group">
            <label for="login-password">Password</label>
            <div class="input-group mb-1">
                <input type="password" id="login-password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="{{ __('adminlte::adminlte.password') }}" autocomplete="current-password">

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>

                <div class="input-group-append">
                    <button type="button" class="btn doc-password-toggle" data-password-target="login-password" aria-label="Show password">
                        <span class="fas fa-eye"></span>
                    </button>
                </div>

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <small class="doc-form-help">If you forgot your password, use the recovery link below before creating a second account.</small>
        </div>

        <div class="row align-items-center">
            <div class="col-sm-7 mb-3 mb-sm-0">
                <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label for="remember">
                        {{ __('adminlte::adminlte.remember_me') }}
                    </label>
                </div>
            </div>

            <div class="col-sm-5">
                <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    @if (Route::has('password.request'))
        <p class="my-0">
            <a href="{{ route('password.request') }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif

    <p class="mt-2 mb-0">
        <a href="{{ route('register') }}">
            Create Account
        </a>
    </p>
@stop

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-password-target]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(button.dataset.passwordTarget);

                    if (!input) {
                        return;
                    }

                    const icon = button.querySelector('.fas');
                    const showingPassword = input.type === 'text';

                    input.type = showingPassword ? 'password' : 'text';
                    button.setAttribute('aria-label', showingPassword ? 'Show password' : 'Hide password');

                    if (icon) {
                        icon.classList.toggle('fa-eye', showingPassword);
                        icon.classList.toggle('fa-eye-slash', !showingPassword);
                    }
                });
            });
        });
    </script>
@endpush
