<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Searchable;
use Illuminate\Notifications\Notification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use SoftDeletes;
    use Searchable;
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected function getSearchableFields()
    {
        return ['name', 'email'];
    }
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }
    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }
    //     public function routeNotificationForSlack(Notification $notification): mixed
    // {
    //     return 'U09CFSU190Q';
    //     // return '#it-dept';
    // }
        public function routeNotificationForSlack(Notification $notification): mixed
    {
        return $this->slack_member_id;
    }
}
