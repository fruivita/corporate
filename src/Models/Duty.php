<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Função comissionada de uma determinada pessoa.
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
     * Pessoas ocupantes de uma determinada função.
     */
    public function persons()
    {
        return $this->hasMany(Person::class, 'duty_id', 'id');
    }
}
