<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnverifiedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    /**
     * Display list of unverified users
     */
    public function index(Request $request)
    {
        $query = UnverifiedUser::with('verifier')->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('q') && $request->q !== '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('temp_card_number', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(15);

        // AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'rows' => view('admin.registrations.partials.rows', compact('registrations'))->render(),
                'pagination' => view('admin.registrations.partials.pagination', compact('registrations'))->render(),
                'total' => $registrations->total()
            ]);
        }

        return view('admin.registrations.index', compact('registrations'));
    }

    /**
     * Show single registration detail
     */
    public function show(UnverifiedUser $registration)
    {
        $registration->load('verifier');

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($registration);
        }

        return view('admin.registrations.show', compact('registration'));
    }

    /**
     * Approve registration and create user account
     */
    public function approve(Request $request, UnverifiedUser $registration)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:users,nik',
            'ktp_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Upload KTP photo
        $ktpPath = $request->file('ktp_photo')->store('ktp_photos', 'public');

        // Create user account
        $user = User::create([
            'name' => $registration->name,
            'email' => $registration->email,
            'password' => $registration->password, // Already hashed
            'photo' => null,
            'role' => 'Member',
            'nik' => $validated['nik'],
            'ktp_photo' => $ktpPath,
            'phone' => $registration->phone,
            'birth_date' => $registration->birth_date,
            'address' => $registration->address,
            'email_verified_at' => now(),
            'approved_by' => auth()->id(), // Track who approved
            'approved_at' => now() // Track when approved
        ]);

        // Update unverified user status
        $registration->update([
            'status' => 'approved',
            'verified_by' => auth()->id(),
            'verified_at' => now()
        ]);

        // Delete from unverified_users after successful transfer
        $registration->delete();

        return response()->json([
            'message' => 'Member berhasil disetujui! Akun telah dibuat.',
            'user' => $user
        ]);
    }

    /**
     * Reject registration
     */
    public function reject(Request $request, UnverifiedUser $registration)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        // Update status to rejected
        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'verified_by' => auth()->id(),
            'verified_at' => now()
        ]);

        // Delete rejected registration
        $registration->delete();

        return response()->json(['message' => 'Pendaftaran ditolak dan data telah dihapus.']);
    }

    /**
     * Bulk approve
     */
    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['error' => 'Tidak ada pendaftaran yang dipilih.'], 422);
        }

        // Get registrations
        $registrations = UnverifiedUser::whereIn('id', $ids)
            ->where('status', 'pending')
            ->get();

        if ($registrations->isEmpty()) {
            return response()->json(['error' => 'Tidak ada pendaftaran pending yang valid.'], 422);
        }

        // Cannot bulk approve - need NIK and KTP for each
        return response()->json([
            'error' => 'Bulk approve tidak tersedia. Silakan approve satu per satu dengan memasukkan NIK dan foto KTP.'
        ], 422);
    }

    /**
     * Bulk reject
     */
    public function bulkReject(Request $request)
    {
        $ids = $request->input('ids', []);
        $reason = $request->input('reason', 'Data tidak sesuai dengan persyaratan.');

        if (empty($ids)) {
            return response()->json(['error' => 'Tidak ada pendaftaran yang dipilih.'], 422);
        }

        $registrations = UnverifiedUser::whereIn('id', $ids)->get();

        foreach ($registrations as $registration) {
            $registration->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);
            $registration->delete();
        }

        return response()->json(['message' => count($registrations) . ' pendaftaran ditolak dan dihapus.']);
    }
}
