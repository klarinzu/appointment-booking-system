<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Events\BookingCreated;
use App\Events\StatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class AppointmentController extends Controller
{

    public function index()
    {
        $appointments = Appointment::latest()->get();
        // dd($appointments); // for debugging only
        $services = Service::where('status', 1)->get(); // Get active services
        return view('backend.appointment.index', compact('appointments', 'services'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'employee_id' => 'required|exists:employees,id',
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
            'amount' => 'required|numeric',
            'booking_date' => 'required|date',
            'booking_time' => 'required|string',
            'status' => 'required|in:Pending payment,Processing,Confirmed,Cancelled,Completed,On Hold,Rescheduled,No Show',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $validated['booking_time'] = $this->normalizeBookingTime($validated['booking_time'], $employee->slot_duration);
        $validated['booking_date'] = Carbon::parse($validated['booking_date'])->toDateString();

        $this->ensureSlotIsAvailable($employee, $validated['booking_date'], $validated['booking_time']);

        $isPrivilegedRole = Auth::check() && (
            Auth::user()->hasRole('admin') ||
            Auth::user()->hasRole('moderator') ||
            Auth::user()->hasRole('employee')
        );

        if ($isPrivilegedRole) {
            $validated['user_id'] = null;
        } elseif (Auth::check() && !$request->has('user_id')) {
            // Otherwise, assign user_id to the authenticated user
            $validated['user_id'] = Auth::id();
        }

        // Generate unique booking ID
        $validated['booking_id'] = 'BK-' . now()->format('YmdHis') . Str::upper(Str::random(4));

        $appointment = Appointment::create($validated);
        $appointment->loadMissing(['employee.user', 'service', 'user']);

        event(new BookingCreated($appointment));

        return response()->json([
            'success' => true,
            'message' => 'Appointment booked successfully!',
            'booking_id' => $appointment->booking_id,
            'appointment' => $appointment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'status' => 'required|in:Pending payment,Processing,Confirmed,Cancelled,Completed,On Hold,Rescheduled,No Show',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $appointment->status = $request->status;
        $appointment->save();

        event(new StatusUpdated($appointment));

        return redirect()->back()->with('success', 'Appointment status updated successfully.');
    }

    protected function normalizeBookingTime(string $bookingTime, ?int $slotDuration): string
    {
        $parts = preg_split('/\s*-\s*/', trim($bookingTime));

        if (count($parts) === 2) {
            $start = $this->parseTimeValue($parts[0]);
            $end = $this->parseTimeValue($parts[1]);
        } else {
            if (empty($slotDuration)) {
                throw ValidationException::withMessages([
                    'booking_time' => 'The selected employee does not have a slot duration configured.',
                ]);
            }

            $start = $this->parseTimeValue($bookingTime);
            $end = (clone $start)->addMinutes($slotDuration);
        }

        if ($end->lessThanOrEqualTo($start)) {
            throw ValidationException::withMessages([
                'booking_time' => 'The selected booking time is invalid.',
            ]);
        }

        return $start->format('g:i A') . ' - ' . $end->format('g:i A');
    }

    protected function parseTimeValue(string $time): Carbon
    {
        $formats = ['H:i', 'g:i A', 'g:iA', 'h:i A'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, trim($time));
            } catch (\Throwable) {
                //
            }
        }

        throw ValidationException::withMessages([
            'booking_time' => 'The selected booking time format is invalid.',
        ]);
    }

    protected function ensureSlotIsAvailable(Employee $employee, string $bookingDate, string $bookingTime): void
    {
        [$requestedStart, $requestedEnd] = array_map(
            fn (string $time) => Carbon::parse($bookingDate . ' ' . trim($time)),
            preg_split('/\s*-\s*/', $bookingTime)
        );

        $conflictExists = Appointment::query()
            ->whereDate('booking_date', $bookingDate)
            ->where('employee_id', $employee->id)
            ->whereNotIn('status', ['Cancelled'])
            ->get(['booking_time'])
            ->contains(function (Appointment $appointment) use ($bookingDate, $requestedStart, $requestedEnd) {
                $times = preg_split('/\s*-\s*/', (string) $appointment->booking_time);

                if (count($times) !== 2) {
                    return false;
                }

                $existingStart = Carbon::parse($bookingDate . ' ' . trim($times[0]));
                $existingEnd = Carbon::parse($bookingDate . ' ' . trim($times[1]));

                return $requestedStart->lt($existingEnd) && $requestedEnd->gt($existingStart);
            });

        if ($conflictExists) {
            throw ValidationException::withMessages([
                'booking_time' => 'The selected time slot is no longer available.',
            ]);
        }
    }

}
