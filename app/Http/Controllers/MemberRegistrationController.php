<?php

namespace App\Http\Controllers;

use App\Models\UnverifiedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberRegistrationController extends Controller
{
    /**
     * Show registration form
     */
    public function create()
    {
        return view('registration.create');
    }

    /**
     * Store registration data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:unverified_users,email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'address' => 'required|string',
        ]);

        // Generate temporary card number
        $tempCardNumber = UnverifiedUser::generateTempCardNumber();

        // Create unverified user
        UnverifiedUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'address' => $validated['address'],
            'temp_card_number' => $tempCardNumber,
            'status' => 'pending'
        ]);

        return redirect()->route('registration.success')
            ->with('temp_card_number', $tempCardNumber)
            ->with('name', $validated['name']);
    }

    /**
     * Show success page
     */
    public function success()
    {
        if (!session()->has('temp_card_number')) {
            return redirect()->route('registration.create');
        }

        return view('registration.success');
    }

    /**
     * Check registration status
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'temp_card_number' => 'required|string'
        ]);

        $registration = UnverifiedUser::where('email', $request->email)
            ->where('temp_card_number', $request->temp_card_number)
            ->first();

        if (!$registration) {
            return back()->withErrors(['error' => 'Data tidak ditemukan. Pastikan email dan nomor kartu temporary benar.']);
        }

        return view('registration.status', compact('registration'));
    }
}
