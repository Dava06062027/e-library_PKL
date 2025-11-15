<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'birth_date',
        'id_document',
        'address_proof',
        'status',
        'verification_token',
        'email_verified_at',
        'reviewed_at',
        'approved_at',
        'reviewed_by',
        'approved_by',
        'review_notes',
        'rejection_reason',
        'temp_card_number'
    ];

    protected $hidden = [
        'password',
        'verification_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'birth_date' => 'date'
    ];

    // Relationships
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Generate temporary card number
    public static function generateTempCardNumber()
    {
        do {
            $number = 'TEMP' . date('Ym') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('temp_card_number', $number)->exists());

        return $number;
    }

    // Generate verification token
    public static function generateVerificationToken()
    {
        return Str::random(64);
    }

    // Status helpers
    public function isPendingVerification()
    {
        return $this->status === 'pending_verification';
    }

    public function isEmailVerified()
    {
        return !is_null($this->email_verified_at);
    }

    public function isUnderReview()
    {
        return $this->status === 'under_review';
    }

    public function isPendingApproval()
    {
        return $this->status === 'pending_approval';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // Status badge color helper
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending_verification' => 'bg-secondary',
            'email_verified' => 'bg-info',
            'under_review' => 'bg-primary',
            'document_requested' => 'bg-warning',
            'pending_approval' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    // Status label helper
    public function getStatusLabel()
    {
        return match($this->status) {
            'pending_verification' => 'Menunggu Verifikasi Email',
            'email_verified' => 'Email Terverifikasi',
            'under_review' => 'Sedang Direview',
            'document_requested' => 'Butuh Dokumen Tambahan',
            'pending_approval' => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Unknown'
        };
    }
}
