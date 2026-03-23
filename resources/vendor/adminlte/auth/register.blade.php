@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
@endif

@section('auth_header', 'Create an LNU DOCUMATE Student Account')

@section('auth_body')
    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <strong>Please complete the required fields correctly.</strong>
            <ul class="mb-0 pl-3 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="doc-auth-tip mb-4">
        <strong>Registration prepares your official student profile.</strong>
        <div class="doc-note">The details you enter here will be reused on official LNU DOCUMATE transaction forms so students do not have to retype the same information every time.</div>
    </div>

    <form action="{{ $register_url }}" method="post">
        @csrf

        <div class="doc-auth-grid">
            <section class="doc-auth-section">
                <div class="doc-auth-section-title">Account Details</div>

                <div class="form-group">
                    <label for="register-name">Full name</label>
                    <div class="input-group mb-1">
                        <input type="text" id="register-name" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="{{ __('adminlte::adminlte.full_name') }}" autofocus autocomplete="name">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>

                        @error('name')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="register-email">Email address</label>
                    <div class="input-group mb-1">
                        <input type="email" id="register-email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autocomplete="email">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>

                        @error('email')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <small class="doc-form-help">Use an active email so LNU DOCUMATE updates remain reachable.</small>
                </div>

                <div class="form-group mb-0">
                    <label for="register-phone">Phone number</label>
                    <div class="input-group mb-1">
                        <input type="tel" id="register-phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}" placeholder="09XXXXXXXXX">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>

                        @error('phone')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="doc-auth-section">
                <div class="doc-auth-section-title">Academic Profile</div>

                <div class="form-group">
                    <label for="register-student-number">Student number</label>
                    <div class="input-group mb-1">
                        <input type="text" id="register-student-number" name="student_number" class="form-control @error('student_number') is-invalid @enderror"
                            value="{{ old('student_number') }}" placeholder="Student number">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-id-card {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>

                        @error('student_number')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="register-course">Course</label>
                    <div class="input-group mb-1">
                        <input type="text" id="register-course" name="course" class="form-control @error('course') is-invalid @enderror"
                            value="{{ old('course') }}" placeholder="Course">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-book {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>
                    </div>
                    @error('course')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="register-college">College</label>
                    <div class="input-group mb-1">
                        <input type="text" id="register-college" name="college" class="form-control @error('college') is-invalid @enderror"
                            value="{{ old('college') }}" placeholder="College">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-university {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>
                    </div>
                    @error('college')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="register-year-level">Year level</label>
                    <div class="input-group mb-1">
                        <select id="register-year-level" name="year_level" class="custom-select @error('year_level') is-invalid @enderror">
                            <option value="">Select year level</option>
                            @foreach (['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', 'Graduate'] as $yearLevel)
                                <option value="{{ $yearLevel }}" @selected(old('year_level') === $yearLevel)>{{ $yearLevel }}</option>
                            @endforeach
                        </select>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-layer-group {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>
                    </div>
                    @error('year_level')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-0">
                    <label for="register-section">Section</label>
                    <div class="input-group mb-1">
                        <input type="text" id="register-section" name="section" class="form-control @error('section') is-invalid @enderror"
                            value="{{ old('section') }}" placeholder="Section (optional)">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-users {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>
                    </div>
                    @error('section')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </section>

            <section class="doc-auth-section">
                <div class="doc-auth-section-title">Contact and Guardian</div>

                <div class="form-group">
                    <label for="register-address">Current address</label>
                    <div class="input-group mb-1">
                        <textarea id="register-address" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Address">{{ old('address') }}</textarea>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-map-marker-alt {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>
                    </div>
                    @error('address')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="register-guardian-name">Parent or guardian name</label>
                    <div class="input-group mb-1">
                        <input type="text" id="register-guardian-name" name="guardian_name" class="form-control @error('guardian_name') is-invalid @enderror"
                            value="{{ old('guardian_name') }}" placeholder="Parent or guardian name">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user-friends {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>
                    </div>
                    @error('guardian_name')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-0">
                    <label for="register-guardian-contact">Parent or guardian contact</label>
                    <div class="input-group mb-1">
                        <input type="text" id="register-guardian-contact" name="guardian_contact" class="form-control @error('guardian_contact') is-invalid @enderror"
                            value="{{ old('guardian_contact') }}" placeholder="Parent or guardian contact">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone-alt {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>
                    </div>
                    @error('guardian_contact')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </section>

            <section class="doc-auth-section">
                <div class="doc-auth-section-title">Password Setup</div>

                <div class="form-group">
                    <label for="register-password">Password</label>
                    <div class="input-group mb-1">
                        <input type="password" id="register-password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="{{ __('adminlte::adminlte.password') }}" autocomplete="new-password">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>

                        @error('password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <small class="doc-form-help">Use at least 8 characters so your LNU DOCUMATE account stays protected.</small>
                </div>

                <div class="form-group mb-0">
                    <label for="register-password-confirmation">Confirm password</label>
                    <div class="input-group mb-1">
                        <input type="password" id="register-password-confirmation" name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            placeholder="{{ __('adminlte::adminlte.retype_password') }}" autocomplete="new-password">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>

                        @error('password_confirmation')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </section>

            <div class="doc-span-2 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div class="doc-note">
                    By registering, you are creating the student profile that LNU DOCUMATE will use for official VPSD transactions.
                </div>
                <button type="submit" class="btn {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-user-plus"></span>
                    {{ __('adminlte::adminlte.register') }}
                </button>
            </div>
        </div>

    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $login_url }}">
            {{ __('adminlte::adminlte.i_already_have_a_membership') }}
        </a>
    </p>
@stop
