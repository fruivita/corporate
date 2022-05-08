<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Duty (Cargo) of a given user.
 *
 * @see https://laravel.com/docs/9.x/eloquent
 */
class Occupation extends Model
{
    use HasFactory;

    protected $table = 'occupations';

    protected $fillable = ['id', 'name'];

    public $incrementing = false;

    /**
     * Users with a certain occupation.
     *
     * Relationship occupation (1:N) user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'occupation_id', 'id');
    }
}
