<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows example form entry points on the student dashboard', function () {
    $this->seed(DatabaseSeeder::class);

    $student = User::where('email', 'student@example.com')->firstOrFail();

    $response = $this->actingAs($student)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Open Example Forms');
    $response->assertSee('View Form Examples');
});

it('shows example buttons for recent transactions on the dashboard', function () {
    $this->seed(DatabaseSeeder::class);

    $student = User::where('email', 'student@example.com')->firstOrFail();

    $response = $this->actingAs($student)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Example Form');
});
