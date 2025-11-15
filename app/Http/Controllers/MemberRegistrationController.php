<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MemberRegistrationController extends Controller
{
    // Form pendaftaran (public)
    public function create()
    {
        return view('registration.create');
    }

    // Submit pendaftaran
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:registrations,email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'address_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'g-recaptcha-response' => 'required' // CAPTCHA validation
        ]);

        // Upload files
        $idDocPath = $request->file('id_document')->store('registrations/id_documents', 'public');
        $addressProofPath = $request->file('address_proof')->store('registrations/address_proofs', 'public');

        // Generate tokens
        $verificationToken = Registration::generateVerificationToken();
        $tempCardNumber = Registration::generateTempCardNumber();

        // Create registration
        $registration = Registration::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'],
            'phone' => $validated['phone'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'id_document' => $idDocPath,
            'address_proof' => $addressProofPath,
            'status' => 'pending_verification',
            'verification_token' => $verificationToken,
            'temp_card_number' => $tempCardNumber
        ]);

        // Send verification email
        $this->sendVerificationEmail($registration);

        return redirect()->route('registration.success')
            ->with('temp_card_number', $tempCardNumber)
            ->with('email', $registration->email);
    }

    // Success page setelah register
    public function success()
    {
        return view('registration.success');
    }

    // Verify email
    public function verify($token)
    {
        $registration = Registration::where('verification_token', $token)->firstOrFail();

        if ($registration->isEmailVerified()) {
            return redirect()->route('login')->with('info', 'Email sudah terverifikasi sebelumnya.');
        }

        $registration->update([
            'email_verified_at' => now(),
            'status' => 'under_review',
            'verification_token' => null
        ]);

        return redirect()->route('registration.verified')
            ->with('success', 'Email berhasil diverifikasi! Pendaftaran Anda sedang dalam proses review.');
    }

    // Verified page
    public function verified()
    {
        return view('registration.verified');
    }

    // Status check (untuk member cek status pendaftaran)
    public function checkStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'temp_card_number' => 'required|string'
        ]);

        $registration = Registration::where('email', $request->email)
            ->where('temp_card_number', $request->temp_card_number)
            ->first();

        if (!$registration) {
            return back()->withErrors(['error' => 'Data tidak ditemukan.']);
        }

        return view('registration.status', compact('registration'));
    }

    // Send verification email
    private function sendVerificationEmail($registration)
    {
        $verificationUrl = route('registration.verify', $registration->verification_token);

        // Kirim email (gunakan Mail facade atau queue)
        Mail::send('emails.registration-verification', [
            'name' => $registration->name,
            'verificationUrl' => $verificationUrl,
            'tempCardNumber' => $registration->temp_card_number
        ], function ($message) use ($registration) {
            $message->to($registration->email)
                ->subject('Verifikasi Email Pendaftaran Member - Perpustakaan Remen Maos');
        });
    }
}
