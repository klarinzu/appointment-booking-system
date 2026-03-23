<?php

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('shows the customized sign up page', function () {
    $response = $this->get('/register');

    $response->assertOk();
    $response->assertSee('Sign Up Account');
    $response->assertSee('Student ID');
    $response->assertSee('Program / Course');
    $response->assertSee('Confirm Password');
});

it('registers a student with separated name fields and signs them in', function () {
    $response = $this->post('/register', [
        'first_name' => 'Maria',
        'middle_name' => 'Lopez',
        'last_name' => 'Santos',
        'extension_name' => 'Jr.',
        'email' => 'maria@example.com',
        'phone' => '09171234567',
        'student_number' => '2026-1010',
        'college' => 'College of Education',
        'course' => 'BS Secondary Education',
        'year_level' => '3rd Year',
        'section' => 'A',
        'address' => 'Tacloban City',
        'guardian_name' => 'Ana Santos',
        'guardian_contact' => '09179876543',
        'password' => 'Password@1',
        'password_confirmation' => 'Password@1',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();

    $user = User::where('email', 'maria@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Maria Lopez Santos Jr.');
    expect($user->first_name)->toBe('Maria');
    expect($user->middle_name)->toBe('Lopez');
    expect($user->last_name)->toBe('Santos');
    expect($user->extension_name)->toBe('Jr.');
    expect($user->has_no_middle_name)->toBeFalse();
    expect($user->hasRole('student'))->toBeTrue();

    $profile = StudentProfile::where('user_id', $user->id)->first();

    expect($profile)->not->toBeNull();
    expect($profile->student_number)->toBe('2026-1010');
    expect($profile->college)->toBe('College of Education');
    expect($profile->course)->toBe('BS Secondary Education');
});

it('rejects a weak registration password', function () {
    $response = $this->from('/register')->post('/register', [
        'first_name' => 'Maria',
        'middle_name' => 'Lopez',
        'last_name' => 'Santos',
        'extension_name' => 'N/A',
        'email' => 'weakpass@example.com',
        'phone' => '09170000001',
        'student_number' => '2026-1011',
        'college' => 'College of Education',
        'course' => 'BS Secondary Education',
        'year_level' => '3rd Year',
        'section' => 'A',
        'address' => 'Tacloban City',
        'guardian_name' => 'Ana Santos',
        'guardian_contact' => '09179876543',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

it('logs in an existing student account', function () {
    Role::findOrCreate('student', 'web');

    $user = User::create([
        'name' => 'Student User',
        'first_name' => 'Student',
        'last_name' => 'User',
        'extension_name' => 'N/A',
        'email' => 'student-login@example.com',
        'phone' => '09170000002',
        'password' => Hash::make('Password@1'),
    ]);

    $user->assignRole('student');

    $response = $this->post('/login', [
        'email' => 'student-login@example.com',
        'password' => 'Password@1',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});
