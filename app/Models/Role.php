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
