<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    /**
     * Display a listing of registrations
     */
    public function index(Request $request)
    {
        $query = Registration::with(['reviewer', 'approver'])->latest();

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

    // Show single registration detail
    public function show(Registration $registration)
    {
        $registration->load(['reviewer', 'approver']);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($registration);
        }

        return view('admin.registrations.show', compact('registration'));
    }

    // Review registration (approve documents)
    public function review(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve_review,request_documents,reject',
            'notes' => 'nullable|string',
            'rejection_reason' => 'nullable|required_if:action,reject|string'
        ]);

        $registration->reviewed_by = auth()->id();
        $registration->reviewed_at = now();
        $registration->review_notes = $validated['notes'] ?? null;

        switch ($validated['action']) {
            case 'approve_review':
                // Dokumen valid, lanjut ke pending approval
                $registration->status = 'pending_approval';
                $message = 'Dokumen disetujui. Menunggu approval Officer.';

                // Notify officer/admin
                $this->notifyOfficers($registration);
                break;

            case 'request_documents':
                // Minta dokumen tambahan
                $registration->status = 'document_requested';
                $message = 'Permintaan dokumen tambahan dikirim ke member.';

                // Send email to member
                $this->sendDocumentRequestEmail($registration, $validated['notes']);
                break;

            case 'reject':
                // Reject pendaftaran
                $registration->status = 'rejected';
                $registration->rejection_reason = $validated['rejection_reason'];
                $message = 'Pendaftaran ditolak.';

                // Send rejection email
                $this->sendRejectionEmail($registration);
                break;
        }

        $registration->save();

        return response()->json(['message' => $message, 'registration' => $registration]);
    }

    // Approve registration (final approval by Officer/Admin)
    public function approve(Registration $registration)
    {
        // Middleware isOfficerOrAdmin udah handle authorization, gak perlu cek lagi

        // Create user account
        $user = User::create([
            'name' => $registration->name,
            'email' => $registration->email,
            'password' => $registration->password, // Already hashed
            'role' => 'Member',
            'email_verified_at' => $registration->email_verified_at
        ]);

        // Update registration
        $registration->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        // Send welcome email with credentials
        $this->sendWelcomeEmail($user, $registration);

        return response()->json([
            'message' => 'Member berhasil disetujui dan akun telah dibuat!',
            'user' => $user
        ]);
    }

    // Reject registration
    public function reject(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        // Send rejection email
        $this->sendRejectionEmail($registration);

        return response()->json(['message' => 'Pendaftaran ditolak.']);
    }

    // Bulk approve
    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['error' => 'Tidak ada pendaftaran yang dipilih.'], 422);
        }

        $registrations = Registration::whereIn('id', $ids)
            ->where('status', 'pending_approval')
            ->get();

        $count = 0;
        foreach ($registrations as $registration) {
            // Create user
            $user = User::create([
                'name' => $registration->name,
                'email' => $registration->email,
                'password' => $registration->password,
                'role' => 'Member',
                'email_verified_at' => $registration->email_verified_at
            ]);

            // Update registration
            $registration->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            $this->sendWelcomeEmail($user, $registration);
            $count++;
        }

        return response()->json(['message' => "{$count} pendaftaran berhasil disetujui."]);
    }

    // Bulk reject
    public function bulkReject(Request $request)
    {
        $ids = $request->input('ids', []);
        $reason = $request->input('reason', 'Dokumen tidak memenuhi syarat.');

        if (empty($ids)) {
            return response()->json(['error' => 'Tidak ada pendaftaran yang dipilih.'], 422);
        }

        Registration::whereIn('id', $ids)->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        return response()->json(['message' => count($ids) . ' pendaftaran ditolak.']);
    }

    // Helper: Send document request email
    private function sendDocumentRequestEmail($registration, $notes)
    {
        Mail::send('emails.document-request', [
            'name' => $registration->name,
            'notes' => $notes
        ], function ($message) use ($registration) {
            $message->to($registration->email)
                ->subject('Permintaan Dokumen Tambahan - Perpustakaan Remen Maos');
        });
    }

    // Helper: Send rejection email
    private function sendRejectionEmail($registration)
    {
        Mail::send('emails.registration-rejected', [
            'name' => $registration->name,
            'reason' => $registration->rejection_reason
        ], function ($message) use ($registration) {
            $message->to($registration->email)
                ->subject('Pendaftaran Ditolak - Perpustakaan Remen Maos');
        });
    }

    // Helper: Send welcome email
    private function sendWelcomeEmail($user, $registration)
    {
        Mail::send('emails.registration-approved', [
            'name' => $user->name,
            'email' => $user->email,
            'tempCardNumber' => $registration->temp_card_number
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Selamat Datang! Pendaftaran Disetujui - Perpustakaan Remen Maos');
        });
    }

    // Helper: Notify officers for approval
    private function notifyOfficers($registration)
    {
        $officers = User::whereIn('role', ['Admin', 'Officer'])->get();

        foreach ($officers as $officer) {
            Mail::send('emails.notify-officer', [
                'officerName' => $officer->name,
                'memberName' => $registration->name,
                'memberEmail' => $registration->email,
                'reviewUrl' => route('admin.registrations.show', $registration)
            ], function ($message) use ($officer) {
                $message->to($officer->email)
                    ->subject('Pendaftaran Member Baru Menunggu Approval');
            });
        }
    }
}
