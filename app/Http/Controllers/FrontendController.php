<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Appointment;
use App\Models\DocumateTransactionType;
use Spatie\OpeningHours\OpeningHours;
use Carbon\Carbon;
use Illuminate\Support\Number;

class FrontendController extends Controller
{
    public function index()
    {
        return view('frontend.index', [
            'transactionTypes' => DocumateTransactionType::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'officeLocations' => config('documate.office_locations'),
            'statusLabels' => config('documate.statuses'),
            'handbook' => config('documate.handbook'),
        ]);
    }


    public function getServices(Request $request, Category $category)
    {
        $setting = Setting::current();

        $services = $category->services()
            ->where('status', 1)
            ->with('category')
            ->get()
            ->map(function ($service) use ($setting) {
                if (isset($service->price)) {
                    $service->price = $this->formatCurrency($service->price, $setting->currency);
                }

                if (isset($service->sale_price)) {
                    $service->sale_price = $this->formatCurrency($service->sale_price, $setting->currency);
                }

                return $service;
            });

        return response()->json([
            'success' => true,
            'services' => $services
        ]);
    }


    public function getEmployees(Request $request, Service $service)
    {
        $employees = $service->employees()
            ->whereHas('user', function ($query) {
                $query->where('status', 1);
            })
            ->with('user') // Eager load user details
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No employees available for this service'
            ]);
        }

        return response()->json([
            'success' => true,
            'employees' => $employees,
            'service' => $service
        ]);
    }




    public function getEmployeeAvailability(Employee $employee, $date = null)
    {
        // Use current date if not provided
        $date = $date ? Carbon::parse($date) : now();

        // Validate slot duration exists
        if (!$employee->slot_duration) {
            return response()->json(['error' => 'Slot duration not set for this employee'], 400);
        }

        try {
            $formatTimeRange = static function ($timeRange) {
                // Handle appointment format (e.g., "06:00 AM - 06:30 AM")
                if (str_contains($timeRange, 'AM') || str_contains($timeRange, 'PM')) {
                    $timeRange = str_replace([' AM', ' PM', ' '], '', $timeRange);
                }

                $times = explode('-', $timeRange);
                $formattedTimes = array_map(function ($time) {
                    $parts = explode(':', $time);
                    $hours = str_pad(trim($parts[0]), 2, '0', STR_PAD_LEFT);
                    return $hours . ':' . $parts[1];
                }, $times);

                return implode('-', $formattedTimes);
            };

            // Process holidays expections
            $holidaysExceptions = $employee->holidays->mapWithKeys(function ($holiday) use ($formatTimeRange) {
                $hours = !empty($holiday->hours)
                    ? collect($holiday->hours)->map(function ($timeRange) use ($formatTimeRange) {
                        return $formatTimeRange($timeRange);
                    })->toArray()
                    : [];

                return [$holiday->date => $hours];
            })->toArray();

            // using spatie opening hours package to process data and expections
            $openingHours = OpeningHours::create(array_merge(
                $employee->days ?? [],
                ['exceptions' => $holidaysExceptions]
            ));

            // Get available time ranges for the requested date
            $availableRanges = $openingHours->forDate($date);

            // If no availability for this date
            if ($availableRanges->isEmpty()) {
                return response()->json(['available_slots' => []]);
            }

            // Generate time slots - NOW PASSING THE EMPLOYEE ID
            $slots = $this->generateTimeSlots(
                $availableRanges,
                $employee->slot_duration,
                $employee->break_duration ?? 0,
                $date,
                $employee->id  // This is the crucial addition
            );

            return response()->json([
                'employee_id' => $employee->id,
                'date' => $date->toDateString(),
                'available_slots' => $slots,
                'slot_duration' => $employee->slot_duration,
                'break_duration' => $employee->break_duration,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing availability: ' . $e->getMessage()], 500);
        }
    }


    protected function generateTimeSlots($availableRanges, $slotDuration, $breakDuration, $date, $employeeId)
    {
        $slots = [];
        $now = now();
        $isToday = $date->isToday();

        // Get existing appointments for this date and employee
        $existingAppointments = Appointment::where('booking_date', $date->toDateString())
            ->where('employee_id', $employeeId)
            ->whereNotIn('status', ['Cancelled']) // Exclude cancelled/ here could add more status to make expection
            ->get(['booking_time']);

        // Convert existing appointments to time ranges we can compare against
        $bookedSlots = $existingAppointments->map(function ($appointment) {
            $times = preg_split('/\s*-\s*/', (string) $appointment->booking_time);

            if (count($times) !== 2) {
                return null;
            }

            return [
                'start' => Carbon::createFromFormat('g:i A', trim($times[0]))->format('H:i'),
                'end' => Carbon::createFromFormat('g:i A', trim($times[1]))->format('H:i')
            ];
        })->filter()->values()->toArray();

        foreach ($availableRanges as $range) {
            $start = Carbon::parse($date->toDateString() . ' ' . $range->start()->format('H:i'));
            $end = Carbon::parse($date->toDateString() . ' ' . $range->end()->format('H:i'));

            // Skip if the entire range is in the past (only for today)
            if ($isToday && $end->lte($now)) {
                continue;
            }

            $currentSlotStart = clone $start;

            // If today and current slot start is in the past, adjust to current time
            if ($isToday && $currentSlotStart->lt($now)) {
                $currentSlotStart = clone $now;

                // Round up to nearest slot interval
                $minutes = $currentSlotStart->minute;
                $remainder = $minutes % $slotDuration;
                if ($remainder > 0) {
                    $currentSlotStart->addMinutes($slotDuration - $remainder)->second(0);
                }
            }

            while ($currentSlotStart->copy()->addMinutes($slotDuration)->lte($end)) {
                $slotEnd = $currentSlotStart->copy()->addMinutes($slotDuration);

                // Check if this slot conflicts with any existing booking
                $isAvailable = true;
                foreach ($bookedSlots as $bookedSlot) {
                    $bookedStart = Carbon::parse($date->toDateString() . ' ' . $bookedSlot['start']);
                    $bookedEnd = Carbon::parse($date->toDateString() . ' ' . $bookedSlot['end']);

                    if ($currentSlotStart->lt($bookedEnd) && $slotEnd->gt($bookedStart)) {
                        $isAvailable = false;
                        break;
                    }
                }

                // Only add slots that are available and in the future (for today)
                if ($isAvailable && (!$isToday || $slotEnd->gt($now))) {
                    $slots[] = [
                        'start' => $currentSlotStart->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                        'display' => $currentSlotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                    ];
                }

                // Add break duration if specified
                $currentSlotStart->addMinutes($slotDuration + $breakDuration);

                // Check if next slot would exceed end time
                if ($currentSlotStart->copy()->addMinutes($slotDuration)->gt($end)) {
                    break;
                }
            }
        }

        return $slots;
    }

    protected function formatCurrency(float|int|string $amount, ?string $currency): string
    {
        $currency = $currency ?: 'PHP';

        try {
            return Number::currency((float) $amount, $currency);
        } catch (\Throwable) {
            return trim($currency . ' ' . number_format((float) $amount, 2));
        }
    }

}
