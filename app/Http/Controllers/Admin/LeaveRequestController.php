<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $requests = LeaveRequest::with(['user', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.leave.index', compact('requests', 'status'));
    }

    public function approve(LeaveRequest $leave)
    {
        $leave->update([
            'status'         => 'approved',
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => now(),
            'rejection_note' => null,
        ]);

        return redirect()->back()->with('success', "Permohonan {$leave->type} dari {$leave->user->name} telah disetujui.");
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $request->validate([
            'rejection_note' => 'required|string|max:255',
        ]);

        $leave->update([
            'status'         => 'rejected',
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => now(),
            'rejection_note' => $request->rejection_note,
        ]);

        return redirect()->back()->with('success', "Permohonan {$leave->type} telah ditolak.");
    }
}

