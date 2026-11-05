<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RelawanController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        $query = User::with('division')
            ->when($currentUser->role === 'admin', fn($q) => $q->where('role', 'relawan'))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->division_id, fn($q) => $q->where('division_id', $request->division_id))
            ->orderBy('name');

        $relawan = $query->paginate(15);
        $divisions = Division::all();

        return view('admin.relawan.index', compact('relawan', 'divisions'));
    }

    public function create()
    {
        $divisions = Division::all();
        $isSuperAdmin = Auth::user()->isSuperAdmin();
        return view('admin.relawan.create', compact('divisions', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        $isSuperAdmin = Auth::user()->isSuperAdmin();

        $rules = [
            'name'        => 'required|string|max:100',
            'nik'         => 'nullable|string|max:20|unique:users,nik',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:255',
            'division_id' => 'nullable|exists:divisions,id',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
        ];

        if ($isSuperAdmin) {
            $rules['role'] = ['required', Rule::in(['super_admin', 'admin', 'relawan'])];
        }

        $data = $request->validate($rules);
        $data['password'] = Hash::make($data['password']);
        $data['role']     = $isSuperAdmin && isset($data['role']) ? $data['role'] : 'relawan';
        $data['is_active']= true;

        User::create($data);

        return redirect()->route('admin.relawan.index')->with('success', 'User / Relawan berhasil ditambahkan.');
    }

    public function edit(User $relawan)
    {
        $divisions = Division::all();
        $isSuperAdmin = Auth::user()->isSuperAdmin();

        // Admin biasa tidak boleh mengedit akun admin lain atau super admin
        if (!Auth::user()->isSuperAdmin() && $relawan->role !== 'relawan') {
            abort(403, 'Anda tidak memiliki hak akses mengedit akun ini.');
        }

        return view('admin.relawan.edit', compact('relawan', 'divisions', 'isSuperAdmin'));
    }

    public function update(Request $request, User $relawan)
    {
        if (!Auth::user()->isSuperAdmin() && $relawan->role !== 'relawan') {
            abort(403);
        }

        $isSuperAdmin = Auth::user()->isSuperAdmin();

        $rules = [
            'name'        => 'required|string|max:100',
            'nik'         => ['nullable', 'string', 'max:20', Rule::unique('users', 'nik')->ignore($relawan->id)],
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:255',
            'division_id' => 'nullable|exists:divisions,id',
            'email'       => ['required', 'email', Rule::unique('users', 'email')->ignore($relawan->id)],
        ];

        if ($isSuperAdmin) {
            $rules['role'] = ['required', Rule::in(['super_admin', 'admin', 'relawan'])];
        }

        $data = $request->validate($rules);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $data['password'] = Hash::make($request->password);
        }

        if (!$isSuperAdmin) {
            unset($data['role']);
        }

        $relawan->update($data);

        return redirect()->route('admin.relawan.index')->with('success', 'Data relawan berhasil diperbarui.');
    }

    public function toggleActive(User $relawan)
    {
        if (!Auth::user()->isSuperAdmin() && $relawan->role !== 'relawan') {
            abort(403);
        }

        $relawan->update(['is_active' => !$relawan->is_active]);
        $status = $relawan->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Akun berhasil {$status}.");
    }

    public function resetWajah(User $relawan)
    {
        $relawan->faceEmbedding()?->delete();
        $relawan->update([
            'face_enrolled' => false,
            'face_id'       => null,
        ]);

        return redirect()->back()->with('success', 'Data pendaftaran wajah relawan berhasil direset.');
    }
}
