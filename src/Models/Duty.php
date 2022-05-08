<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Duty (Função comissionada) of a given user.
 *
 * @see https://laravel.com/docs/9.x/eloquent
 */
class Duty extends Model
{
    use HasFactory;

    protected $table = 'duties';

    protected $fillable = ['id', 'name'];

    public $incrementing = false;

    /**
     * Users with a certain duty.
     *
     * Relationship duty (1:N) user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'duty_id', 'id');
    }
}
