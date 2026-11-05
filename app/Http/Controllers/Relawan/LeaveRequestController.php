<?php

namespace App\Http\Controllers\Relawan;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $leaves = $user->leaveRequests()->orderByDesc('created_at')->paginate(10);

        return view('relawan.leave.index', compact('leaves'));
    }

    public function create()
    {
        return view('relawan.leave.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:izin,sakit,cuti',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072',
        ]);

        $user = Auth::user();
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments/leave', 'public');
        }

        LeaveRequest::create([
            'user_id'         => $user->id,
            'type'            => $request->type,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'reason'          => $request->reason,
            'attachment_path' => $attachmentPath,
            'status'          => 'pending',
        ]);

        return redirect()->route('relawan.leave.index')
            ->with('success', 'Permohonan ' . $request->type . ' berhasil diajukan dan menunggu persetujuan admin.');
    }
}

