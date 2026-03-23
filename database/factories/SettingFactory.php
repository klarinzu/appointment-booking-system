<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bname' => 'DOCUMATE',
            'email' => 'office@example.com',
            'phone' => '09000000000',
            'currency' => 'PHP',
            'meta_title' => 'DOCUMATE - VPSD Transaction System',
        ];
    }
}
