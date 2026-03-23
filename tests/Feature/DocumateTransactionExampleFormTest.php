<?php

use App\Models\DocumateTransactionType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders an example preview for every documate transaction form type', function () {
    $this->seed(DatabaseSeeder::class);

    $student = User::where('email', 'student@example.com')->firstOrFail();
    $transactionType = DocumateTransactionType::where('code', 'F-SDM-004')->firstOrFail();

    $response = $this->actingAs($student)->get(route('documate.transactions.example', $transactionType));

    $response->assertOk();
    $response->assertSee('Example Preview Only');
    $response->assertSee($transactionType->name);
    $response->assertSee('DOC-EX-' . $transactionType->code);
    $response->assertSee('Print Example');
});

it('shows example form links in the student transaction catalog', function () {
    $this->seed(DatabaseSeeder::class);

    $student = User::where('email', 'student@example.com')->firstOrFail();
    $transactionType = DocumateTransactionType::where('code', 'F-SDM-001')->firstOrFail();

    $response = $this->actingAs($student)->get(route('documate.transactions.index'));

    $response->assertOk();
    $response->assertSee(route('documate.transactions.example', $transactionType), false);
    $response->assertSee('View Example Form');
});
