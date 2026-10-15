<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Searchable;


class Role extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;

    public const CLOSER = 'Closer';

    // A Closer with oversight of the TSR and Closer teams. Everywhere the
    // app treats someone as a closer, this role counts too (CLOSER_ROLES).
    public const CLOSER_SALES_MANAGER = 'Closer - Sales Manager';

    public const CLOSER_ROLES = [self::CLOSER, self::CLOSER_SALES_MANAGER];

    protected $dates = ['deleted_at'];
    protected $gaurd_name = 'web';

    protected $fillable = [
        'name', 'guard_name	', 'created_by', 'updated_by', 'deleted_by'
    ];
    protected function getSearchableFields()
    {
        return ['name'];
    }


    public function users()
    {
    	return $this->hasMany(User::class);
    }
    public function permissions()
    {
        return $this->hasMany(Permission::class,'role_id');
    }
}
