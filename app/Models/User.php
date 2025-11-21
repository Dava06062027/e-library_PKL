<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'role',
        'nik',
        'ktp_photo',
        'phone',
        'birth_date',
        'address',
        'approved_by',
        'approved_at',
        'member_card_photo',
    ];

    /**
     * Check if user is online
     *
     * @return bool
     */
    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    /**
     * Get online status as string
     *
     * @return string
     */
    public function getOnlineStatus()
    {
        return $this->isOnline() ? 'Online' : 'Offline';
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'id_member');
    }

    /**
     * Relationship with approver (Officer/Admin who approved this member)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }
}
