<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Função comissionada de um determinada usuário.
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
     * Usuários ocupantes de uma determinada função.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'duty_id', 'id');
    }
}
