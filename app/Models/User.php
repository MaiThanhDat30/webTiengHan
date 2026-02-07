<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use App\Services\ResendMailService;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function sendEmailVerificationNotification()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $this->id,
                'hash' => sha1($this->email),
            ]
        );
    
        ResendMailService::send(
            $this->email,
            'Xác thực email',
            "
            <h3>Xin chào {$this->name}</h3>
            <p>Vui lòng click link bên dưới để xác thực email:</p>
            <a href='{$url}'>Xác thực email</a>
            <p>Link có hiệu lực trong 60 phút.</p>
            "
        );
    }
    public function vocabProgresses()
    {
        return $this->hasMany(\App\Models\UserVocabProgress::class);
    }
    
}
