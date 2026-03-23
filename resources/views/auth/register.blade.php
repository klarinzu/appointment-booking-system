@extends('auth.auth-page', ['auth_type' => 'register'])

@php
    $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', 'Graduate'];
    $extensionOptions = ['N/A', 'Sr.', 'Jr.', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
    $noMiddleName = old('has_no_middle_name');
@endphp

@section('auth_header', 'Sign Up Account')

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

    <form action="{{ route('register') }}" method="post">
        @csrf

        <div class="doc-auth-grid">
            <section class="doc-auth-section">
                <div class="doc-auth-section-title">Name Details</div>

                <div class="doc-auth-name-grid">
                    <div class="form-group">
                        <label for="register-last-name">Last Name</label>
                        <div class="input-group mb-1">
                            <input type="text" id="register-last-name" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                value="{{ old('last_name') }}" placeholder="Last Name" autocomplete="family-name">

                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                </div>
                            </div>

                            @error('last_name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="register-first-name">First Name</label>
                        <div class="input-group mb-1">
                            <input type="text" id="register-first-name" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                value="{{ old('first_name') }}" placeholder="First Name" autocomplete="given-name">

                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                </div>
                            </div>

                            @error('first_name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="register-middle-name">Middle Name</label>
                        <div class="input-group mb-1">
                            <input type="text" id="register-middle-name" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror"
                                value="{{ old('middle_name') }}" placeholder="Middle Name" autocomplete="additional-name" @disabled($noMiddleName)>

                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                </div>
                            </div>

                            @error('middle_name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="custom-control custom-checkbox doc-middle-name-toggle">
                            <input type="checkbox" class="custom-control-input" id="register-no-middle-name" name="has_no_middle_name" value="1" @checked($noMiddleName)>
                            <label class="custom-control-label" for="register-no-middle-name">I do not have a middle name</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="register-extension-name">Extension Name</label>
                        <div class="input-group mb-1">
                            <select id="register-extension-name" name="extension_name" class="custom-select @error('extension_name') is-invalid @enderror">
                                @foreach ($extensionOptions as $extensionOption)
                                    <option value="{{ $extensionOption }}" @selected(old('extension_name', 'N/A') === $extensionOption)>{{ $extensionOption }}</option>
                                @endforeach
                            </select>

                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-chevron-down {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                </div>
                            </div>
                        </div>
                        @error('extension_name')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="doc-auth-section">
                <div class="doc-auth-section-title">Account Details</div>

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
                            value="{{ old('phone') }}" placeholder="09XXXXXXXXX" autocomplete="tel">

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
                    <label for="register-student-number">Student ID</label>
                    <div class="input-group mb-1">
                        <input type="text" id="register-student-number" name="student_number" class="form-control @error('student_number') is-invalid @enderror"
                            value="{{ old('student_number') }}" placeholder="Student ID">

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
                    <label for="register-course">Program / Course</label>
                    <div class="input-group mb-1">
                        <input type="text" id="register-course" name="course" class="form-control @error('course') is-invalid @enderror"
                            value="{{ old('course') }}" placeholder="Program / Course">

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
                    <label for="register-year-level">Year level</label>
                    <div class="input-group mb-1">
                        <select id="register-year-level" name="year_level" class="custom-select @error('year_level') is-invalid @enderror">
                            <option value="">Select year level</option>
                            @foreach ($yearLevels as $yearLevel)
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

                        <div class="input-group-append">
                            <button type="button" class="btn doc-password-toggle" data-password-target="register-password" aria-label="Show password">
                                <span class="fas fa-eye"></span>
                            </button>
                        </div>

                        @error('password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <small class="doc-form-help">Use at least 8 characters with 1 uppercase letter, 1 lowercase letter, and 1 special character.</small>
                </div>

                <div class="form-group mb-0">
                    <label for="register-password-confirmation">Confirm Password</label>
                    <div class="input-group mb-1">
                        <input type="password" id="register-password-confirmation" name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            placeholder="{{ __('adminlte::adminlte.retype_password') }}" autocomplete="new-password">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                            </div>
                        </div>

                        <div class="input-group-append">
                            <button type="button" class="btn doc-password-toggle" data-password-target="register-password-confirmation" aria-label="Show password">
                                <span class="fas fa-eye"></span>
                            </button>
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
                    By signing up, you are creating the student profile that LNU DOCUMATE will use for official VPSD transactions.
                </div>
                <button type="submit" class="btn {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-user-plus"></span>
                    Sign Up
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('login') }}">
            Sign In
        </a>
    </p>
@stop

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const middleNameInput = document.getElementById('register-middle-name');
            const noMiddleNameCheckbox = document.getElementById('register-no-middle-name');

            function syncMiddleNameState() {
                if (!middleNameInput || !noMiddleNameCheckbox) {
                    return;
                }

                middleNameInput.disabled = noMiddleNameCheckbox.checked;

                if (noMiddleNameCheckbox.checked) {
                    middleNameInput.value = '';
                }
            }

            if (noMiddleNameCheckbox) {
                noMiddleNameCheckbox.addEventListener('change', syncMiddleNameState);
                syncMiddleNameState();
            }

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
