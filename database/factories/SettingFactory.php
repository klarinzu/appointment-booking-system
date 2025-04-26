<?php

namespace Database\Factories;

use Illuminate\Console\Scheduling\Schedule;
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
            'bname' => 'Room Cleaning Scheduler',
            'email' => 'kalingg.magpatoc@gmail.com',
            'phone' => '09773579442',
            'currency' => 'PHP',
            'meta_title' => 'RCSS - Booking System',
        ];
    }
}
