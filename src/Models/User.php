<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Usuário (pessoa).
 *
 * Notar que o tratamento dado à pessoa, presume-a como usuária da aplicação.
 *
 * @see https://laravel.com/docs/9.x/eloquent
 */
class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = ['username', 'name', 'department_id', 'occupation_id', 'duty_id'];

    /**
     * Lotação de um determinado usuário.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    /**
     * Cargo de um determinado usuário.
     */
    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id', 'id');
    }

    /**
     * Função de um determinado usuário.
     */
    public function duty()
    {
        return $this->belongsTo(Duty::class, 'duty_id', 'id');
    }
}
