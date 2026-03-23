<?php

namespace App\Http\Controllers;

use App\Models\StudentProfile;
use App\Notifications\DocumateTransactionNotification;
use Illuminate\Http\Request;

class DocumateClearanceController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isDocumateAdmin() || $request->user()->isStudentOfficer(), 403);

        $query = StudentProfile::query()->with('user', 'taggedBy')->latest();

        if ($request->filled('course')) {
            $query->where('course', 'like', '%' . $request->string('course')->trim()->toString() . '%');
        }

        if ($request->filled('clearance_status')) {
            $query->where('clearance_status', $request->string('clearance_status')->toString());
        }

        return view('backend.clearances.index', [
            'profiles' => $query->get(),
        ]);
    }

    public function update(Request $request, StudentProfile $studentProfile)
    {
        abort_unless($request->user()->isDocumateAdmin() || $request->user()->isStudentOfficer(), 403);

        $data = $request->validate([
            'clearance_status' => 'required|in:pending,cleared,hold',
            'clearance_notes' => 'nullable|string|max:2000',
        ]);

        $studentProfile->update([
            'clearance_status' => $data['clearance_status'],
            'clearance_notes' => $data['clearance_notes'] ?? null,
            'tagged_by' => $request->user()->id,
            'tagged_at' => now(),
        ]);

        if ($studentProfile->user) {
            $studentProfile->user->notify(
                new DocumateTransactionNotification(
                    new \App\Models\DocumateTransaction([
                        'id' => 0,
                        'reference_no' => 'CLEARANCE-' . $studentProfile->user_id,
                        'status' => $studentProfile->clearance_status,
                    ]),
                    'Clearance status updated',
                    'Your clearance status is now marked as ' . ucfirst($studentProfile->clearance_status) . '.',
                    route('dashboard')
                )
            );
        }

        return back()->with('success', 'Clearance status updated successfully.');
    }
}
