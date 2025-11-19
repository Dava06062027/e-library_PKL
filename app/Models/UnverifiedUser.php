<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnverifiedUser extends Model
{
    use HasFactory;

    protected $table = 'unverified_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birth_date',
        'address',
        'temp_card_number',
        'status',
        'rejection_reason',
        'verified_at',
        'verified_by'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'verified_at' => 'datetime',
    ];

    // Relationship
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Generate temporary card number
    public static function generateTempCardNumber()
    {
        do {
            $number = 'TEMP' . date('Ym') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('temp_card_number', $number)->exists());

        return $number;
    }

    // Status helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // Status badge color
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    // Status label
    public function getStatusLabel()
    {
        return match($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Unknown'
        };
    }
}
