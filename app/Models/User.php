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
        'slack_member_id',
        'status',
        'user_type',
        'avatar'
    ];

    public const AVATAR_DIR = 'profile-photos';

    /**
     * Single source of truth for "may this user open that user's profile?".
     * Everyone can open their own. Beyond that: Admin, Executives, anyone who
     * can view the Sale Report (they already see that person's sales there),
     * and the Closer - Sales Manager for the TSRs and Closers they oversee.
     */
    public function canViewProfileOf(User $target): bool
    {
        if ((int) $this->id === (int) $target->id) {
            return true;
        }

        $roleName = optional($this->role)->name;

        if ((int) $this->role_id === 1 || $roleName === 'Executives') {
            return true;
        }

        if (\App\Helpers\GlobalHelper::modulePermission($this, 'salereport')->view) {
            return true;
        }

        return $roleName === Role::CLOSER_SALES_MANAGER
            && in_array(optional($target->role)->name, ['TSR', ...Role::CLOSER_ROLES], true);
    }

    /**
     * URL of the user's uploaded photo, or null when they haven't set one
     * (callers fall back to initials / the default avatar).
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset(self::AVATAR_DIR . '/' . $this->avatar) : null;
    }

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
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
