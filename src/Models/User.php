<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * User (pessoa).
 *
 * Note that the treatment given to the person (pessoa) presumes him/her as a
 * user of the application.
 *
 * @see https://laravel.com/docs/9.x/eloquent
 */
class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = ['username', 'name', 'department_id', 'occupation_id', 'duty_id'];

    /**
     * Department of a given user.
     *
     * Relationship user (N:1) department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    /**
     * Occupation of a given user.
     *
     * Relationship user (N:1) occupation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id', 'id');
    }

    /**
     * Duty of a given user.
     *
     * Relationship user (N:1) duty.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function duty()
    {
        return $this->belongsTo(Duty::class, 'duty_id', 'id');
    }
}
