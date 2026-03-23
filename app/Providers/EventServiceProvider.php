<?php

namespace App\Providers;

use App\Events\BookingCreated;
use App\Events\StatusUpdated;
use App\Listeners\AdminNotifyBookingCreated;
use App\Listeners\EmployeeNotifyBookingCreated;
use App\Listeners\NotifyAdminAppointmentStatusUpdated;
use App\Listeners\NotifyEmployeeAppointmentStatusUpdated;
use App\Listeners\NotifyUserAppointmentStatusUpdated;
use App\Listeners\UserNotifyBookingCreated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookingCreated::class => [
            AdminNotifyBookingCreated::class,
            EmployeeNotifyBookingCreated::class,
            UserNotifyBookingCreated::class,
        ],
        StatusUpdated::class => [
            NotifyAdminAppointmentStatusUpdated::class,
            NotifyEmployeeAppointmentStatusUpdated::class,
            NotifyUserAppointmentStatusUpdated::class,
        ],
    ];
}
