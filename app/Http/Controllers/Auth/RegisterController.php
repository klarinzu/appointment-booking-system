<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $data['name'] = $this->buildFullName($data);

        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => [
                Rule::requiredIf(! $this->hasNoMiddleName($data)),
                'nullable',
                'string',
                'max:255',
            ],
            'last_name' => ['required', 'string', 'max:255'],
            'extension_name' => ['required', 'string', Rule::in($this->extensionOptions())],
            'has_no_middle_name' => ['nullable', 'boolean'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'student_number' => ['required', 'string', 'max:50', 'unique:student_profiles,student_number'],
            'course' => ['required', 'string', 'max:255'],
            'college' => ['required', 'string', 'max:255'],
            'year_level' => ['required', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:500'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'guardian_contact' => ['required', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->symbols()],
        ], [], [
            'student_number' => 'student ID',
            'course' => 'program / course',
            'guardian_name' => 'parent or guardian name',
            'guardian_contact' => 'parent or guardian contact',
            'extension_name' => 'extension name',
            'has_no_middle_name' => 'no middle name',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $fullName = $this->buildFullName($data);
            $hasNoMiddleName = $this->hasNoMiddleName($data);

            $user = User::create([
                'name' => $fullName,
                'first_name' => trim((string) ($data['first_name'] ?? '')),
                'middle_name' => $hasNoMiddleName ? null : trim((string) ($data['middle_name'] ?? '')),
                'last_name' => trim((string) ($data['last_name'] ?? '')),
                'extension_name' => trim((string) ($data['extension_name'] ?? 'N/A')),
                'has_no_middle_name' => $hasNoMiddleName,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
            ]);

            StudentProfile::create([
                'user_id' => $user->id,
                'student_number' => $data['student_number'],
                'course' => $data['course'],
                'college' => $data['college'],
                'year_level' => $data['year_level'],
                'section' => $data['section'] ?? null,
                'address' => $data['address'],
                'guardian_name' => $data['guardian_name'],
                'guardian_contact' => $data['guardian_contact'],
            ]);

            Role::findOrCreate('student', 'web');
            $user->assignRole('student');

            return $user;
        });
    }

    protected function buildFullName(array $data): string
    {
        $parts = [
            trim((string) ($data['first_name'] ?? '')),
        ];

        if (! $this->hasNoMiddleName($data) && filled($data['middle_name'] ?? null)) {
            $parts[] = trim((string) $data['middle_name']);
        }

        $parts[] = trim((string) ($data['last_name'] ?? ''));

        $extensionName = trim((string) ($data['extension_name'] ?? ''));

        if ($extensionName !== '' && $extensionName !== 'N/A') {
            $parts[] = $extensionName;
        }

        return trim(implode(' ', array_filter($parts)));
    }

    protected function hasNoMiddleName(array $data): bool
    {
        return filter_var($data['has_no_middle_name'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    protected function extensionOptions(): array
    {
        return ['N/A', 'Sr.', 'Jr.', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
    }
}
