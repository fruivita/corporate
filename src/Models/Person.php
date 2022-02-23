<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @see https://laravel.com/docs/8.x/eloquent
 */
class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = ['username', 'name', 'department_id', 'occupation_id', 'duty_id'];

    /**
     * Lotação de uma determinado pessoa.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    /**
     * Cargo de uma determinado pessoa.
     */
    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id', 'id');
    }

    /**
     * Função de uma determinado pessoa.
     */
    public function duty()
    {
        return $this->belongsTo(Duty::class, 'duty_id', 'id');
    }
}
