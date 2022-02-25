<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Cargo de uma determinada pessoa.
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
     * Pessoas ocupantes de um determinado cargo.
     */
    public function persons()
    {
        return $this->hasMany(Person::class, 'occupation_id', 'id');
    }
}
